<?php

namespace Paymenter\Extensions\Servers\CPanel;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[ExtensionMeta(
    name: 'cPanel',
    description: 'cPanel server extension',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/cpanel',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA0IiBoZWlnaHQ9IjcwIiB2aWV3Ym94PSIwIDAgMTA0IDcwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJob3ZlcjpzY2FsZS0xMjAgZmxleCBpdGVtcy1jZW50ZXIganVzdGlmeS1jZW50ZXIgc2l6ZS0xOCByb3VuZGVkLXhsIHAtNCBiZy1bI0ZGNkMyQ10gcmluZy02IHJpbmctWyNGRjZDMkNdLzMwIj48cGF0aCBkPSJNMjYuMjI2IDE3LjQ0OTRINDUuODQwN0w0Mi44MDYgMjkuMDc5NEM0Mi4zODg4IDMwLjYzMTEgNDEuNDc0MSAzMi4wMDMxIDQwLjIwMjIgMzIuOTg1QzM4LjkzNzUgMzMuOTY2IDM3LjM3NjIgMzQuNDg2NCAzNS43NzU5IDM0LjQ2MDVIMjYuNjU5OUMyNC42NzQ5IDM0LjQzMyAyMi43Mzg1IDM1LjA3NTEgMjEuMTYzMiAzNi4yODMxQzE5LjU0OTIgMzcuNTMyNSAxOC4zNzgxIDM5LjI2NjMgMTcuODIxNyA0MS4yMzAyQzE3LjQxOTkgNDIuNjczMiAxNy4zNzUyIDQ0LjE5MjMgMTcuNjkxNSA0NS42NTY1QzE3Ljk4MjIgNDYuOTkxOSAxOC41NTUzIDQ4LjI0OTYgMTkuMzcyNCA0OS4zNDUxQzIwLjIwMDMgNTAuNDMwMiAyMS4yNzAyIDUxLjMwNyAyMi40OTY5IDUxLjkwNTRDMjMuNzkxMSA1Mi41NDY5IDI1LjIxODQgNTIuODc0IDI2LjY2MjggNTIuODYwMUgzMi4yMTc0QzMyLjcxMzUgNTIuODUxNiAzMy4yMDQ0IDUyLjk2MTMgMzMuNjQ5NiA1My4xODAxQzM0LjA5NDkgNTMuMzk4OSAzNC40ODE2IDUzLjcyMDcgMzQuNzc3OCA1NC4xMTg2QzM1LjEwMjMgNTQuNTAyOSAzNS4zMjUzIDU0Ljk2MjUgMzUuNDI2NCA1NS40NTUzQzM1LjUyNzUgNTUuOTQ4IDM1LjUwMzQgNTYuNDU4MyAzNS4zNTY0IDU2LjkzOTNMMzEuODg0OCA2OS43ODQ0SDI1LjgwOTRDMjEuODEwNSA2OS44MjQzIDE3Ljg2MjYgNjguODg1OCAxNC4zMDk2IDY3LjA1MDVDMTAuODk3NSA2NS4zMDg5IDcuOTEyOTMgNjIuODM0MSA1LjU2OTczIDU5LjgwMzRDMy4yNDY1IDU2Ljc3NTggMS42MzAzIDUzLjI2NjYgMC44Mzk2MTcgNDkuNTMzMkMwLjAwMDc4NzIyOCA0NS42MTA5IDAuMTA1ODk0IDQxLjU0NTQgMS4xNDYyOCAzNy42NzE3TDEuNDkzNDQgMzYuMzY5OUMyLjk2MTU4IDMwLjkzMjIgNi4xNzgxNyAyNi4xMjg2IDEwLjY0NyAyMi43MDAzQzEyLjgzODUgMjEuMDM4NiAxNS4yNzk3IDE5LjczNSAxNy44Nzk2IDE4LjgzODFDMjAuNTYzNCAxNy45MDc1IDIzLjM4NTQgMTcuNDM4IDI2LjIyNiAxNy40NDk0WiIgZmlsbD0id2hpdGUiPjwvcGF0aD48cGF0aCBkPSJNMzYuMTIwMSA2OS43ODM2TDUzLjIyMDggNS43MzE4NUM1My42MzggNC4xODAxNiA1NC41NTI3IDIuODA4MTYgNTUuODI0NiAxLjgyNjI2QzU3LjA4OCAwLjg0NTA5OSA1OC42NDg1IDAuMzI0NTg4IDYwLjI0OCAwLjM1MDgxMUg3OC4zOTAyQzgyLjM4OSAwLjMxMTIzMiA4Ni4zMzY5IDEuMjQ5NzggODkuODkgMy4wODQ3M0M5My4yOTg2IDQuODIzNzQgOTYuMjc3OCA3LjI5ODkzIDk4LjYxMTkgMTAuMzMxMUMxMDAuOTQ2IDEzLjM2MzMgMTAyLjU3NyAxNi44NzY1IDEwMy4zODYgMjAuNjE2NUMxMDQuMjU1IDI0LjUzOCAxMDQuMTM1IDI4LjYxMzkgMTAzLjAzOSAzMi40Nzc5TDEwMi42OTIgMzMuNzc5OEMxMDEuOTYgMzYuNTExOSAxMDAuNzg3IDM5LjEwNjMgOTkuMjIwMSA0MS40NjA4Qzk2LjkwMTEgNDQuOTUxNyA5My43NTMgNDcuODEzNCA5MC4wNTc0IDQ5Ljc4OTlDODYuMzYxOCA1MS43NjY0IDgyLjIzNCA1Mi43OTYyIDc4LjA0MzEgNTIuNzg3SDYyLjMzMzlMNjUuNDU4NCA0MS4wNzAyQzY1Ljg5MjYgMzkuNTUwMiA2Ni44MDU3IDM4LjIxMDkgNjguMDYyMSAzNy4yNTE0QzY5LjMyNjggMzYuMjcwNSA3MC44ODgxIDM1Ljc1IDcyLjQ4ODQgMzUuNzc2SDc3LjUyMjNDNzkuNTQ5NyAzNS43NzgyIDgxLjUyMDIgMzUuMTA1NiA4My4xMjMxIDMzLjg2NDFDODQuNzI2IDMyLjYyMjcgODUuODcwMSAzMC44ODMgODYuMzc1IDI4LjkxOTVDODYuNzc0NCAyNy41Mjk5IDg2LjgxNDIgMjYuMDYxNSA4Ni40OTA3IDI0LjY1MjNDODYuMTk2NiAyMy4zMTYgODUuNjE5NSAyMi4wNTgyIDg0Ljc5ODMgMjAuOTYzN0M4My45NjM3IDE5Ljg3MzkgODIuODk2MiAxOC45ODQzIDgxLjY3MzggMTguMzU5OUM4MC4zOTIxIDE3LjY5MzYgNzguOTY2OCAxNy4zNTEgNzcuNTIyMyAxNy4zNjE4SDY3LjgwMTdMNTUuMTMwMiA2NC40ODkzQzU0LjY5NiA2Ni4wMDk0IDUzLjc4MjkgNjcuMzQ4NyA1Mi41MjY1IDY4LjMwODFDNTEuMjkgNjkuMjc5NSA0OS43NTkyIDY5Ljc5OTkgNDguMTg3IDY5Ljc4MzZIMzYuMTIwMVoiIGZpbGw9IndoaXRlIj48L3BhdGg+PC9zdmc+',
)]
class CPanel extends Server
{
    private function request($endpoint, $method = 'get', $data = [])
    {
        $host = rtrim($this->config('host'), '/');
        $response = Http::withHeaders([
            'Authorization' => 'whm ' . $this->config('username') . ':' . $this->config('apikey'),
        ])->$method($host . '/json-api' . $endpoint, $data)->throw();

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
                'type' => 'text',
                'label' => 'Hostname',
                'placeholder' => 'https://example.com:2087',
                'validation' => 'url:http,https',
                'required' => true,
            ],
            [
                'name' => 'username',
                'type' => 'text',
                'placeholder' => 'johndoe',
                'label' => 'Username',
                'required' => true,
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'placeholder' => '1234567890abcdef',
                'label' => 'API key',
                'required' => true,
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
        // Get all the packages
        $packages = $this->request('/listpkgs')->json();
        $packageOptions = [];
        foreach ($packages['package'] as $package) {
            $packageOptions[] = [
                'value' => $package['name'],
                'label' => $package['name'],
            ];
        }

        return [
            [
                'name' => 'package',
                'type' => 'select',
                'label' => 'Package',
                'options' => $packageOptions,
                'required' => true,
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        $request = $this->request('/listaccts');
        if (!$request->successful()) {
            return $request->json('statusmsg');
        }

        return true;
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
        $username = Str::random();
        // If first one is a number, add a letter
        if (is_numeric($username[0])) {
            $username = 'a' . substr($username, 1);
        }

        $response = $this->request(
            '/createacct',
            data: [
                'api.version' => 1,
                'username' => $username,
                'contactemail' => $service->user->email,
                'domain' => $properties['domain'],
                'plan' => $properties['package'] ?? $settings['package'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            $service->properties()->updateOrCreate([
                'key' => 'cpanel_username',
            ], [
                'name' => 'cPanel username',
                'value' => strtolower($username),
            ]);
        }

        return true;
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
        if (!isset($properties['cpanel_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request(
            '/suspendacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
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
        if (!isset($properties['cpanel_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request(
            '/unsuspendacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
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
        if (!isset($properties['cpanel_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request(
            '/removeacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            // Delete the properties
            $service->properties()->where('key', 'cpanel_username')->delete();

            return true;
        }

        return false;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['cpanel_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request(
            '/modifyacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
                'plan' => $settings['package'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
    }

    public function getLoginUrl(Service $service, $settings, $properties): string
    {
        if (!isset($properties['cpanel_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request(
            '/create_user_session',
            'post',
            [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
                'service' => 'cpaneld',
            ]
        )->json();

        if (isset($response['data']['url'])) {
            $url = $response['data']['url'];

            return $url;
        }

        throw new Exception('Unable to generate cPanel login URL');
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['cpanel_username'])) {
            return [];
        }

        return [
            [
                'label' => 'Access cPanel',
                'type' => 'button',
                'function' => 'getLoginUrl',
            ],
        ];
    }
}
