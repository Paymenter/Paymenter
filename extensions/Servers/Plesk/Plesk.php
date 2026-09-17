<?php

namespace Paymenter\Extensions\Servers\Plesk;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[ExtensionMeta(
    name: 'Plesk',
    description: 'Plesk server extension',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/plesk',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODgiIGhlaWdodD0iMzciIHZpZXdib3g9IjAgMCA4OCAzNyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBjbGFzcz0iaG92ZXI6c2NhbGUtMTIwIGZsZXggaXRlbXMtY2VudGVyIGp1c3RpZnktY2VudGVyIHNpemUtMTggcm91bmRlZC14bCBwLTQgYmctd2hpdGUgcmluZy02IHJpbmctd2hpdGUvMjAiPjxwYXRoIGQ9Ik0zLjQwMTA1IDM2Ljg5ODFIMC43Njc1NzhWOS4zMTkyNUMyLjg2MTkxIDguNjU1NyA1LjA1OTkzIDguMzAzMTggNy4yNzg2OCA4LjI4MjQ1QzguNzA5NDcgOC4yNjE3MSAxMC4xNDAzIDguNTEwNTQgMTEuNDg4MSA5LjAwODIxQzEyLjY5MDggOS40NDM2NiAxMy44MTA1IDEwLjEyOCAxNC43NDM2IDExLjAxOTZDMTUuNjU2IDExLjg5MDUgMTYuMzYxIDEyLjkyNzMgMTYuODE3MiAxNC4xMDkzQzE3LjMxNDkgMTUuMzMyNyAxNy41NDMgMTYuNjU5OCAxNy41NDMgMTcuOTg2OUMxNy41NDMgMTkuNjg3MiAxNy4yNTI3IDIxLjEzODggMTYuNjcyMSAyMi4zNjIyQzE2LjEzMyAyMy41MjM0IDE1LjM0NSAyNC41Mzk1IDE0LjM3MDQgMjUuMzY4OUMxMy40MTY1IDI2LjE1NjkgMTIuMzE3NSAyNi43Mzc1IDExLjExNDggMjcuMDlDOS45MTIxNSAyNy40NjMyIDguNjQ3MjYgMjcuNjQ5OSA3LjM4MjM2IDI3LjY0OTlDNi42OTgwNyAyNy42NDk5IDYuMDM0NTIgMjcuNjA4NCA1LjM3MDk3IDI3LjUwNDdDNC43MDc0MiAyNy40MDEgNC4wNDM4NyAyNy4yNTU5IDMuNDAxMDUgMjcuMDlWMzYuODk4MVpNMTQuNTM2MyAxNy45MjQ3QzE0LjUzNjMgMTUuNzI2NyAxMy45MTQyIDEzLjk0MzQgMTIuNjkwOCAxMi41NzQ4QzExLjQ2NzQgMTEuMjI3IDkuNzI1NTMgMTAuNTQyNyA3LjUwNjc4IDEwLjU0MjdDNi44MDE3NSAxMC41NDI3IDYuMTE3NDcgMTAuNTg0MSA1LjQxMjQ0IDEwLjY2NzFDNC43MjgxNSAxMC43NSA0LjA0Mzg3IDEwLjkxNTkgMy40MDEwNSAxMS4xNDRWMjQuNjAxN0M0LjAyMzEzIDI0LjgwOSA0LjY0NTIxIDI0Ljk3NDkgNS4yODgwMyAyNS4xMjAxQzUuOTEwMTEgMjUuMjY1MiA2LjUzMjE5IDI1LjMyNzQgNy4xNzUgMjUuMzI3NEM5LjQzNTIzIDI1LjMyNzQgMTEuMjE4NSAyNC42ODQ2IDEyLjU0NTYgMjMuMzk5QzEzLjg3MjcgMjIuMTM0MSAxNC41MzYzIDIwLjMwOTMgMTQuNTM2MyAxNy45MjQ3WiIgZmlsbD0iYmxhY2siPjwvcGF0aD48cGF0aCBkPSJNMjIuODUxNiAyNy40NjM3VjAuODU5Mzc1SDI1LjUwNThWMjcuNDQyOUgyMi44NTE2VjI3LjQ2MzdaIiBmaWxsPSJibGFjayI+PC9wYXRoPjxwYXRoIGQ9Ik00Ny4yNTY3IDI0LjY4NThDNDYuNDA2NiAyNS42MTkgNDUuMzQ5IDI2LjM2NTUgNDQuMTg3OCAyNi44NjMxQzQzLjAyNjYgMjcuMzYwOCA0MS42MTY1IDI3LjYwOTYgMzkuOTc4NCAyNy42MDk2QzM4LjYwOTggMjcuNjMwNCAzNy4yNDEzIDI3LjM4MTUgMzUuOTc2NCAyNi44MjE2QzM0Ljg3NzMgMjYuMzQ0NyAzMy45MDI4IDI1LjYxOSAzMy4xMzU1IDI0LjcwNjZDMzIuMzY4MyAyMy44MTQ5IDMxLjc4NzcgMjIuNzc4MSAzMS40MzUyIDIxLjY1ODRDMzEuMDQxMiAyMC40OTcyIDMwLjg1NDYgMTkuMjczNyAzMC44NTQ2IDE4LjA1MDNDMzAuODEzMSAxNi41NzgxIDMxLjA2MTkgMTUuMDg1MSAzMS41Mzg5IDEzLjY5NThDMzEuOTMyOCAxMi41NTUzIDMyLjU3NTcgMTEuNTE4NSAzMy40MDUxIDEwLjYyNjhDMzQuMTcyMyA5LjgzODg2IDM1LjEwNTQgOS4yMTY3OCAzNi4xNDIyIDguODIyOEMzNy4xOTk4IDguNDI4ODEgMzguMzE5NSA4LjI0MjE5IDM5LjQzOTMgOC4yNDIxOUM0Mi4yMTc5IDguMjQyMTkgNDQuMzMzIDkuMDkyMzcgNDUuNzQzIDEwLjgxMzVDNDcuMTUzMSAxMi41MTM4IDQ3LjgzNzMgMTUuMDAyMSA0Ny43OTU5IDE4LjI3ODRIMzMuNTUwMkMzMy42MzMyIDIwLjQzNSAzNC4yNTUzIDIyLjEzNTMgMzUuNDE2NSAyMy40MDAyQzM2LjU3NzcgMjQuNjY1MSAzOC4xNzQ0IDI1LjMwNzkgNDAuMTY1IDI1LjMwNzlDNDEuMjAxOCAyNS4zMjg3IDQyLjIzODYgMjUuMTQyIDQzLjIxMzIgMjQuNzQ4QzQ0LjE2NzEgMjQuMzU0MSA0NC45OTY1IDIzLjc1MjcgNDUuNjgwOCAyMi45ODU1TDQ3LjI1NjcgMjQuNjg1OFpNNDUuMTIwOSAxNi4wNTk3QzQ1LjEyMDkgMTUuMzEzMiA0NC45OTY1IDE0LjU2NjcgNDQuNzY4NCAxMy44NjE2QzQ0LjU0MDMgMTMuMjE4OCA0NC4yMDg1IDEyLjYxNzUgNDMuNzUyNCAxMi4xMTk4QzQzLjI3NTQgMTEuNjAxNCA0Mi42NzQxIDExLjIwNzQgNDIuMDMxMyAxMC45NTg2QzQwLjQzNDYgMTAuMzc4IDM4LjY3MiAxMC4zNzggMzcuMDc1NCAxMC45NTg2QzM2LjQ1MzMgMTEuMjA3NCAzNS44NzI3IDExLjU4MDcgMzUuNDE2NSAxMi4wNzgzQzM0Ljk2MDMgMTIuNTk2NyAzNC41ODcgMTMuMTc3NCAzNC4yOTY3IDEzLjc5OTRDMzMuOTQ0MiAxNC41MDQ1IDMzLjczNjkgMTUuMjcxNyAzMy42MzMyIDE2LjA1OTdINDUuMTIwOVoiIGZpbGw9ImJsYWNrIj48L3BhdGg+PHBhdGggZD0iTTY1LjE5MzYgMTIuOTQ5MUM2NC42MTMgMTIuMTgxOSA2My44ODczIDExLjU1OTggNjMuMDM3MSAxMS4xMDM2QzYyLjE4NjkgMTAuNjg4OSA2MS4yMzMxIDEwLjQ4MTUgNjAuMyAxMC41MjNDNTguOTUyMSAxMC41MjMgNTcuOTk4MyAxMC43NzE4IDU3LjQ1OTEgMTEuMjQ4OEM1Ni45MiAxMS43MDUgNTYuNjI5NyAxMi4zNjg1IDU2LjY1MDQgMTMuMDczNUM1Ni42Mjk3IDEzLjUyOTcgNTYuNzc0OCAxMy45ODU5IDU3LjAyMzcgMTQuMzU5MkM1Ny4yOTMyIDE0LjczMjQgNTcuNjI1IDE1LjA0MzUgNTguMDE5IDE1LjI3MTZDNTguNDc1MiAxNS41NDExIDU4Ljk1MjEgMTUuNzY5MiA1OS40NDk4IDE1LjkzNTFDNTkuOTg4OSAxNi4xMjE3IDYwLjU0ODggMTYuMzA4NCA2MS4xNTAxIDE2LjQ5NUM2MS44NzU5IDE2LjcyMzEgNjIuNTgwOSAxNi45NzE5IDYzLjMwNjcgMTcuMjQxNUM2NC4wMTE3IDE3LjQ5MDMgNjQuNjc1MyAxNy44NDI4IDY1LjI3NjYgMTguMjc4M0M2NS44NTcyIDE4LjY5MyA2Ni4zNTQ5IDE5LjIzMjEgNjYuNzI4MSAxOS44NTQyQzY3LjEyMjEgMjAuNTU5MiA2Ny4zMDg3IDIxLjM2NzkgNjcuMjg4IDIyLjE5NzRDNjcuMzA4NyAyMy4wMDYxIDY3LjEwMTQgMjMuNzk0MSA2Ni43MjgxIDI0LjUxOThDNjYuMzU0OSAyNS4yMDQxIDY1LjgzNjUgMjUuNzg0NyA2NS4yMTQ0IDI2LjI0MDlDNjQuNTMwMSAyNi43Mzg2IDYzLjc2MjkgMjcuMDkxMSA2Mi45NTQyIDI3LjI5ODRDNjAuNzE0NyAyNy44NzkgNTguMzMgMjcuNzU0NiA1Ni4xNzM1IDI2LjkyNTJDNTQuOTA4NiAyNi4zNjUzIDUzLjc2ODEgMjUuNTE1MSA1Mi44NzY1IDI0LjQ3ODNMNTQuOTUwMSAyMi42NTM2QzU2LjMzOTQgMjQuNDM2OSA1OC4xMjI3IDI1LjMwNzggNjAuMzIwNyAyNS4zMDc4QzYxLjc1MTUgMjUuMzA3OCA2Mi44MDkgMjUuMDM4MiA2My41MTQgMjQuNDk5MUM2NC4xOTgzIDIzLjk1OTkgNjQuNTUwOCAyMy4zMTcxIDY0LjU1MDggMjIuNTcwNkM2NC41NzE2IDIyLjA1MjIgNjQuNDQ3MiAyMS41NTQ2IDY0LjE3NzYgMjEuMDk4NEM2My45MDggMjAuNjgzNyA2My41NTU1IDIwLjM1MTkgNjMuMTQwOCAyMC4wODIzQzYyLjY2MzkgMTkuNzkyIDYyLjE2NjIgMTkuNTQzMiA2MS42MjcxIDE5LjM3NzNDNjEuMDQ2NSAxOS4xOTA3IDYwLjQ0NTEgMTkuMDA0IDU5LjgyMyAxOC44MTc0QzU5LjA5NzMgMTguNjEwMSA1OC4zOTIyIDE4LjM4MiA1Ny42ODcyIDE4LjEzMzFDNTcuMDIzNyAxNy45MDUgNTYuMzgwOCAxNy41NzMzIDU1LjgwMDIgMTcuMTU4NUM1NS4yNDA0IDE2Ljc2NDUgNTQuNzYzNCAxNi4yNDYxIDU0LjQzMTcgMTUuNjY1NUM1NC4wNTg0IDE0Ljk2MDUgNTMuODcxOCAxNC4xNTE4IDUzLjg5MjUgMTMuMzQzMUM1My44OTI1IDExLjY2MzUgNTQuNDkzOSAxMC4zNzc5IDU1LjcxNzMgOS40NjU0OEM1Ni45NDA3IDguNTUzMSA1OC41MTY3IDguMDk2OSA2MC40NjU4IDguMDk2OUM2MS43MzA3IDguMDc2MTcgNjIuOTc0OSA4LjMyNSA2NC4xMzYxIDguODAxOTJDNjUuMjE0NCA5LjI1ODEyIDY2LjIzMDQgMTAuMDY2OCA2Ny4yMjU4IDExLjIwNzNMNjUuMjc2NiAxMi45OTA2TDY1LjE5MzYgMTIuOTQ5MVoiIGZpbGw9ImJsYWNrIj48L3BhdGg+PHBhdGggZD0iTTcyLjI0NDYgMjcuNDYzMlYwLjkwMDM5MUg3NC44OTg4VjI3LjQ2MzJINzIuMjQ0NlpNNzQuOTE5NiAxNy42NTUxTDg0LjA0MzQgOC40NDgyOUg4Ny42MUw3OC4yNTgxIDE3LjU3MjFMODcuNTY4NSAyNy40NDI1SDgzLjg3NzVMNzQuOTE5NiAxNy42NTUxWiIgZmlsbD0iYmxhY2siPjwvcGF0aD48cGF0aCBkPSJNMzIuMDk5NSAzNi44OTc3SDE2LjA5MTNWMzQuMzg4N0gzMi4wOTk1VjM2Ljg5NzdaIiBmaWxsPSIjNTNCQ0U2Ij48L3BhdGg+PC9zdmc+',
)]
class Plesk extends Server
{
    private function request($endpoint, $method = 'get', $data = [])
    {
        $host = rtrim($this->config('host'), '/');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withBasicAuth($this->config('username'), $this->config('password'))->withoutVerifying()->$method($host . '/api/v2' . $endpoint, $data)->throw();

        return $response;
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     */
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'label' => 'Host',
                'type' => 'text',
                'required' => true,
                'description' => 'The IP address or domain name of the Plesk server (with http:// or https://)',
            ],
            [
                'name' => 'username',
                'label' => 'Username',
                'type' => 'text',
                'required' => true,
                'description' => 'The username of the Plesk server',
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'required' => true,
                'description' => 'The password of the Plesk server',
            ],
        ];
    }

    /**
     * Get product config
     *
     * @param  array  $values
     */
    public function getProductConfig($values = []): array
    {
        return [
            [
                'name' => 'plan',
                'type' => 'text',
                'label' => 'Plan',
                'required' => true,
                'description' => 'The plan name of the wanted service plan',
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('/server', 'get')->json();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCheckoutConfig()
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'label' => 'Domain',
                'required' => true,
                'validation' => [new Domain, 'required'],
                'placeholder' => 'domain.com',
            ],
        ];
    }

    /**
     * Create a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        $returnData = [
            'domain' => $properties['domain'],
            'username' => strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $service->user->name)),
            'ftp_password' => Str::password(12),
        ];

        $pleskCustomerId = $service->user->properties->where('key', 'plesk_id')->first();

        try {
            $user = $this->request('/clients/' . $pleskCustomerId->value)->json();
            // Check if user exists
        } catch (Exception $e) {
            $returnData['password'] = Str::password(12);

            $user = $this->request('/clients', 'post', [
                'username' => $returnData['username'],
                'email' => $service->user->email,
                'password' => $returnData['password'],
                'name' => $service->user->name,
                'login' => $returnData['username'],
                'type' => 'customer',
                'external_id' => $service->user->id,
            ])->json();

            $service->user->properties()->create([
                'key' => 'plesk_id',
                'name' => 'Plesk ID',
                'value' => $user['id'],
            ]);
        }

        $returnData['client_id'] = $user['id'];

        $response = $this->request('/domains', 'post', [
            'name' => $properties['domain'],
            'external_id' => $service->id,
            'owner_client' => [
                'id' => $returnData['client_id'],
                'login' => 'owner',
                'guid' => $user['guid'],
                'external_id' => $service->user->id,
            ],
            'hosting_type' => 'virtual',
            'hosting_settings' => [
                'ftp_login' => $returnData['username'],
                'ftp_password' => $returnData['ftp_password'],
            ],
            'plan' => [
                'name' => $settings['plan'],
            ],
        ])->json();

        $service->properties()->updateOrCreate([
            'key' => 'domain_id',
        ], [
            'name' => 'Plesk Domain ID',
            'value' => $response['id'],
        ]);

        return $returnData;
    }

    /**
     * Suspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'] . '/status', 'put', [
            'status' => 'suspended',
        ]);

        return true;
    }

    /**
     * Unsuspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'] . '/status', 'put', [
            'status' => 'active',
        ]);

        return true;
    }

    /**
     * Terminate a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'], 'delete');

        $service->properties()->where('key', 'domain_id')->delete();

        return true;
    }
}
