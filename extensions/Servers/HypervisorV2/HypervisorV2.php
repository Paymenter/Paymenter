<?php

namespace Paymenter\Extensions\Servers\HypervisorV2;
use App\Classes\Extension\Server;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HypervisorV2 extends Server
{
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'label' => 'Hostname',
                'required' => true,
                'validation' => 'url'
            ],
            [
                'name' => 'accesshash',
                'type' => 'text',
                'label' => 'Access Hash',
                'required' => true,
                'encrypted' => true
            ]
        ];
    }

    private function sendCommand($route, $params = [], $method = 'post')
    {
        $url = 'https://' . rtrim($this->config('host'),'/') . '/api/v1/billing' . $route;
        $request = Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', $this->config('accesshash')),
            'Accept' => 'application/json'
        ])->$method($url, $params);

        if (!$request->successful()) {
            throw new \Exception('Oops, something went wrong');
        }

        $decoded = json_decode($request->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON Response: '. $request->getBody());
        }

        if (!$decoded['success'])
        {
            throw new \Exception($decoded['message'] ?? 'Oops, something went wrong');
        }

        return $decoded;
    }


    public function getProductConfig($values = []): array
    {
        $requestPlans = $this->sendCommand('plans', [], 'get');
        $plans = [];
        foreach ($requestPlans['plans'] as $plan) {
            $plans[$plan['id']] = $plan['name'];
        }

        $requestHypervisorGroups = $this->sendCommand('hypervisor/groups', [], 'get');
        $hypervisorGroups = [];
        foreach ($requestHypervisorGroups['groups'] as $name => $id) {
            $hypervisorGroups[$id] = $name;
        }

        $requestHypervisors = $this->sendCommand('hypervisors', [], 'get');
        $hypervisors = [];
        foreach ($requestHypervisors['hypervisors'] as $hypervisor) {
            $hypervisors[$hypervisor['id']] = $hypervisor['name'];
        }

        return [
            [
                'name' => 'plan_id',
                'type' => 'select',
                'label' => 'Plan',
                'options' => $plans,
                'required' => true
            ],
            [
                'name' => 'hypervisor_group_id',
                'type' => 'select',
                'label' => 'Hypervisor Group',
                'options' => $hypervisorGroups,
                'required' => true
            ],
            [
                'name' => 'hypervisor_id',
                'type' => 'select',
                'label' => 'Hypervisor',
                'options' => $hypervisors,
                'required' => true
            ],
            [
                'name' => 'override_hypervisor_group',
                'type' => 'select',
                'label' => 'Override Hypervisor Group to Selected Hypervisor',
                'required' => true,
                'options' => [
                    '0' => 'No',
                    '1' => 'Yes'
                ]
            ]
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->sendCommand('auth',[],'get');
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }


    public function createServer(Service $service, $settings, $properties)
    {
        if (isset($properties['server_id'])) {
            throw new \Exception('Server already exists!');
        }

        $usernameParts = explode(" ", $service->user->name);
        if (count($usernameParts) > 1) {
            $first_name = $usernameParts[0];
            $last_name = $usernameParts[1];
        } else {
            $first_name = $last_name = $service->user->name;
        }

        $getUser = $this->sendCommand('users', [
            'email' => $service->user->email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => Str::random(20)
        ]);

        if (!$getUser['success']) {
            throw new \Exception($getUser['message'] ?? 'Oops, something went wrong');
        }

        $payload = [
            'plan_id' => $settings['plan_id'],
            'user_id' => $getUser['user']['id'],
            'hypervisor_group_id' => $settings['hypervisor_group_id'],
            'hypervisor_id' => $settings['hypervisor_id'],
        ];

        if ($settings['override_hypervisor_group'] === 'yes')
        {
            unset($payload['hypervisor_group_id']);
        }

        $create = $this->sendCommand('instances', $payload);

        if (!$create['success']) {
            throw new \Exception($create['message'] ?? 'Oops, something went wrong');
        }

        $service->properties()->updateOrCreate([
            'key' => 'server_id'
        ],[
            'name' => 'Instance ID',
            'value' => $create['instance']['id']
        ]);

        return $create['instance'];
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id']))
        {
            throw new \Exception('Server does not exist!');
        }

        $suspend = $this->sendCommand('instance/'.$properties['server_id'].'/suspend', [
            'suspended_reason' => "Billing System"
        ]);

        if (!$suspend['success']) {
            throw new \Exception($suspend['message'] ?? 'Oops, something went wrong');
        }

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id']))
        {
            throw new \Exception('Server does not exist!');
        }

        $unsuspend = $this->sendCommand('instance/'.$properties['server_id'].'/unsuspend');
        if (!$unsuspend['success'])
        {
            throw new \Exception($unsuspend['message'] ?? 'Oops, something went wrong');
        }

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id']))
        {
            throw new \Exception('Server does not exist!');
        }

        $terminate = $this->sendCommand('instance/'.$properties['server_id'].'/delete');
        if (!$terminate['success'])
        {
            throw new \Exception($terminate['message'] ?? 'Oops, something went wrong');
        }

        $service->properties()->where('key','server_id')->delete();

        return true;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id']))
        {
            throw new \Exception('Server does not exist!');
        }

        $upgradePlan = $this->sendCommand('instance/'.$properties['server_id'].'/upgrade',[
            'plan_id' => $settings['plan_id']
        ]);

        if (!$upgradePlan['success'])
        {
            throw new \Exception( $upgradePlan['message'] ?? 'Oops, something went wrong');
        }

        return true;
    }

    public function getActions(Service $service): array
    {
        return [
            [
                'type' => 'button',
                'label' => 'Launch Control Panel',
                'function' => 'ssoAuth'
            ]
        ];
    }

    public function ssoAuth(Service $service)
    {
        $getUser = $this->sendCommand('users/',[
            'email' => $service->user->email
        ]);

        if (!$getUser['success']) {
                throw new \Exception($getUser['message'] ?? 'Oops, something went wrong');
        }

        $getLink = $this->sendCommand('users/'.$getUser['user']['id'].'/sso/link');
        if (!isset($getLink['success'])) {
            throw new \Exception($getLink['message'] ?? 'Oops, something went wrong');
        }

        return $getLink['payload'];
    }

}
