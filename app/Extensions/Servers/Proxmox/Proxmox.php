<?php

namespace App\Extensions\Servers\Proxmox;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class Proxmox extends Server
{
    public function getMetadata()
    {
        return [
            'display_name' => 'Proxmox',
            'version' => '1.0.1',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'host',
                'friendlyName' => 'Host',
                'type' => 'text',
                'required' => true,
                'description' => 'The IP address or domain name of the Proxmox server (with http:// or https://)',
            ],
            [
                'name' => 'port',
                'friendlyName' => 'Port',
                'type' => 'text',
                'required' => true,
                'description' => 'The port of the Proxmox server',
            ],
            [
                'name' => 'username',
                'friendlyName' => 'Username',
                'type' => 'text',
                'required' => true,
                'description' => 'The api username of the Proxmox server',
            ],
            [
                'name' => 'password',
                'friendlyName' => 'API Token',
                'type' => 'text',
                'required' => true,
                'description' => 'The API Token of the Proxmox server',
            ]
        ];
    }

    public function getProductConfig($options)
    {
        $nodes = $this->getRequest('/nodes');
        if (!$nodes->json()) throw new Exception('Unable to get nodes');
        foreach ($nodes->json()['data'] as $node) {
            $nodeList[] = [
                'name' => $node['node'],
                'value' => $node['node']
            ];
        }

        $currentNode = isset($options['node']) ? $options['node'] : null;
        $storageName = isset($options['storage']) ? $options['storage'] : null;
        if ($currentNode == null) {
            $currentNode = $nodeList[0]['value'];
        }
        $storage = $this->getRequest('/nodes/' . $currentNode . '/storage');
        $storageList = [];
        if (!$storage->json()) throw new Exception('Unable to get storage');
        foreach ($storage->json()['data'] as $storage) {
            $storageList[] = [
                'name' => $storage['storage'],
                'value' => $storage['storage']
            ];
        }

        $resourcePool = $this->getRequest('/pools');
        $poolList = [
            [
                'name' => 'None',
                'value' => ''
            ]
        ];

        if (!$resourcePool->json()) throw new Exception('Unable to get resource pool');
        foreach ($resourcePool->json()['data'] as $pool) {
            $poolList[] = [
                'name' => $pool['poolid'],
                'value' => $pool['poolid']
            ];
        }

        // Only list contentVztmpl 
        $templateList = [];
        $isoList = [];
        foreach ($nodeList as $node) {
            // Get all storage
            $storage = $this->getRequest('/nodes/' . $node['value'] . '/storage');
            if (!$storage->json()) throw new Exception('Unable to get storage');
            foreach ($storage->json()['data'] as $storage) {
                $storageName = $storage['storage'];
                $template = $this->getRequest('/nodes/' . $node['value'] . '/storage/' . $storageName . '/content');
                if (!$template->json()) throw new Exception('Unable to get template');
                foreach ($template->json()['data'] as $template) {
                    if ($template['content'] == 'vztmpl') {
                        $templateList[] = [
                            'name' => $template['volid'],
                            'value' => $template['volid']
                        ];
                    } else if ($template['content'] == 'iso') {
                        $isoList[] = [
                            'name' => $template['volid'],
                            'value' => $template['volid']
                        ];
                    }
                }
            }
        }



        $bridgeList = [];
        $bridge = $this->getRequest('/nodes/' . $currentNode . '/network');
        if (!$bridge->json()) throw new Exception('Unable to get bridge');
        foreach ($bridge->json()['data'] as $bridge) {
            if (!isset($bridge['active'])) continue;
            if (!$bridge['active']) continue;
            $bridgeList[] = [
                'name' => $bridge['iface'],
                'value' => $bridge['iface']
            ];
        }

        $cpuList = [
            [
                'name' => 'Default',
                'value' => ''
            ]
        ];
        $cpu = $this->getRequest('/nodes/' . $currentNode . '/capabilities/qemu/cpu');
        if (!$cpu->json()) throw new Exception('Unable to get cpu');
        foreach ($cpu->json()['data'] as $cpu) {
            $cpuList[] = [
                'name' => $cpu['name'] . ' (' . $cpu['vendor'] . ')',
                'value' => $cpu['name']
            ];
        }



        return [
            [
                'type' => 'title',
                'friendlyName' => 'General',
                'description' => 'General options',
            ],
            [
                'name' => 'node',
                'type' => 'dropdown',
                'friendlyName' => 'Node',
                'required' => true,
                'description' => 'The node name of the wanted node (submit to update the storage list)',
                'options' =>  $nodeList
            ],
            [
                'name' => 'storage',
                'type' => 'dropdown',
                'friendlyName' => 'Storage',
                'description' => 'The storage name of the wanted storage',
                'options' =>  $storageList
            ],
            [
                'name' => 'pool',
                'type' => 'dropdown',
                'friendlyName' => 'Resource Pool',
                'description' => 'Resource Pool places VMs in a group',
                'options' =>  $poolList
            ],
            [
                'name' => 'type',
                'type' => 'dropdown',
                'friendlyName' => 'Type',
                'required' => true,
                'description' => 'The type of the wanted VM',
                'options' => [
                    [
                        'name' => 'qemu',
                        'value' => 'qemu'
                    ],
                    [
                        'name' => 'lxc',
                        'value' => 'lxc'
                    ]
                ]
            ],
            [
                'name' => 'cores',
                'type' => 'text',
                'friendlyName' => 'Cores',
                'required' => true,
                'description' => 'The number of cores of the wanted VM',
            ],
            [
                'name' => 'memory',
                'type' => 'text',
                'friendlyName' => 'Memory (MB)',
                'required' => true,
                'description' => 'The amount of memory of the wanted VM',
            ],
            [
                'name' => 'disk',
                'type' => 'text',
                'friendlyName' => 'Disk (GB)',
                'required' => true,
                'description' => 'The amount of disk of the wanted VM',
            ],
            [
                'name' => 'network_limit',
                'type' => 'text',
                'friendlyName' => 'Network Limit (MB)',
                'description' => 'The network limit of the wanted VM',
            ],


            [
                'name' => 'lxc',
                'type' => 'title',
                'friendlyName' => 'LXC',
                'description' => 'All LXC options',
            ],
            [
                'name' => 'template',
                'type' => 'dropdown',
                'friendlyName' => 'Template',
                'description' => 'The template name of the wanted VM',
                'options' => $templateList
            ],
            [
                'name' => 'unprivileged',
                'type' => 'boolean',
                'friendlyName' => 'Unprivileged Container',
                'description' => 'Enable/disable unprivileged container',
            ],
            [
                'name' => 'nesting',
                'type' => 'boolean',
                'friendlyName' => 'Nesting',
                'description' => 'Enable/disable nesting',
            ],
            [
                'name' => 'ostypelxc',
                'type' => 'dropdown',
                'friendlyName' => 'OS Type',
                'description' => 'The OS type of the wanted VM',
                'options' => [
                    [
                        'name' => 'debian',
                        'value' => 'debian'
                    ],
                    [
                        'name' => 'devuan',
                        'value' => 'devuan'
                    ],
                    [
                        'name' => 'ubuntu',
                        'value' => 'ubuntu'
                    ],
                    [
                        'name' => 'centos',
                        'value' => 'centos'
                    ],
                    [
                        'name' => 'fedora',
                        'value' => 'fedora'
                    ],
                    [
                        'name' => 'opensuse',
                        'value' => 'opensuse'
                    ],
                    [
                        'name' => 'archlinux',
                        'value' => 'archlinux'
                    ],
                    [
                        'name' => 'alpine',
                        'value' => 'alpine'
                    ],
                    [
                        'name' => 'gentoo',
                        'value' => 'gentoo'
                    ],
                    [
                        'name' => 'nixos',
                        'value' => 'nixos'
                    ],
                    [
                        'name' => 'unmanaged',
                        'value' => 'unmanaged'
                    ]
                ]
            ],
            [
                'type' => 'text',
                'name' => 'swap',
                'friendlyName' => 'Swap (MB)',
                'description' => 'The amount of swap of the wanted VM',
            ],
            [
                'type' => 'text',
                'name' => 'ips',
                'friendlyName' => 'IPs',
                'description' => 'Available IPs to assign to the VM\'s. Separate IPs with a comma',
            ],
            [
                'type' => 'text',
                'name' => 'gateway',
                'friendlyName' => 'Gateway',
                'description' => 'The gateway of the VM',
            ],

            [
                'type' => 'title',
                'friendlyName' => 'QEMU',
                'description' => 'All QEMU options',
            ],
            [
                'name' => 'nonetwork',
                'type' => 'boolean',
                'friendlyName' => 'No Network',
                'description' => 'Enable/disable network',
            ],
            [
                'name' => 'bridge',
                'type' => 'dropdown',
                'friendlyName' => 'Bridge',
                'options' => $bridgeList
            ],
            [
                'name' => 'model',
                'type' => 'dropdown',
                'friendlyName' => 'Model',
                'options' => [
                    [
                        'name' => 'VirtIO',
                        'value' => 'virtio'
                    ],
                    [
                        'name' => 'Intel E1000',
                        'value' => 'e1000'
                    ],
                    [
                        'name' => 'Realtek RTL8139',
                        'value' => 'rtl8139'
                    ],
                    [
                        'name' => 'VMWare VMXNET3',
                        'value' => 'vmxnet3'
                    ]
                ]
            ],
            [
                'name' => 'vlantag',
                'type' => 'text',
                'friendlyName' => 'VLAN Tag',
                'description' => 'Optional VLAN tag',
            ],
            [
                'name' => 'firewall',
                'type' => 'boolean',
                'friendlyName' => 'Firewall',
                'description' => 'Enable/disable firewall',
            ],
            [
                'name' => 'os',
                'type' => 'dropdown',
                'friendlyName' => 'OS',
                'required' => true,
                'options' => [
                    [
                        'name' => 'ISO',
                        'value' => 'iso'
                    ],
                    [
                        'name' => 'Pysical CD/DVD drive',
                        'value' => 'cdrom'
                    ],
                    [
                        'name' => 'None',
                        'value' => 'none'
                    ]
                ]
            ],
            [
                'name' => 'iso',
                'type' => 'dropdown',
                'friendlyName' => 'ISO',
                'description' => 'The ISO name of the wanted VM',
                'options' => $isoList
            ],
            [
                'name' => 'cloudinit',
                'type' => 'boolean',
                'friendlyName' => 'Cloudinit',
                'description' => 'Enable/disable cloudinit',
            ],
            [
                'name' => 'storageType',
                'type' => 'dropdown',
                'friendlyName' => 'Bus/Device',
                'description' => 'The bus/device of the VM',
                'options' =>
                [
                    [
                        'name' => 'IDE',
                        'value' => 'ide'
                    ],
                    [
                        'name' => 'SATA',
                        'value' => 'sata'
                    ],
                    [
                        'name' => 'SCSI',
                        'value' => 'scsi'
                    ],
                    [
                        'name' => 'VirtIO block',
                        'value' => 'virtio'
                    ]
                ]
            ],
            [
                'name' => 'storageFormat',
                'type' => 'dropdown',
                'friendlyName' => 'Storage Format',
                'description' => 'The storage format of the VM',
                'options' => [
                    [
                        'name' => 'Raw',
                        'value' => 'raw'
                    ],
                    [
                        'name' => 'Qcow2',
                        'value' => 'qcow2'
                    ],
                    [
                        'name' => 'VMDK',
                        'value' => 'vmdk'
                    ],
                ]
            ],
            [
                'name' => 'cache',
                'type' => 'dropdown',
                'friendlyName' => 'Cache',
                'description' => 'The cache of the VM',
                'options' => [
                    [
                        'name' => 'Default (no cache)',
                        'value' => 'default'
                    ],
                    [
                        'name' => 'Direct Sync',
                        'value' => 'directsync'
                    ],
                    [
                        'name' => 'Write Through',
                        'value' => 'writethrough'
                    ],
                    [
                        'name' => 'Write Back',
                        'value' => 'write back'
                    ],
                    [
                        'name' => 'Write Back (unsafe)',
                        'value' => 'unsafe'
                    ],
                    [
                        'name' => 'No Cache',
                        'value' => 'none'
                    ],
                ]
            ],
            [
                'name' => 'ostype',
                'type' => 'dropdown',
                'friendlyName' => 'Guest OS type',
                'description' => 'The OS type of the VM',
                'options' => [
                    [
                        'name' => 'other',
                        'value' => 'other'
                    ],
                    [
                        'name' => 'Windows XP',
                        'value' => 'wxp'
                    ],
                    [
                        'name' => 'Windows 2000',
                        'value' => 'w2k'
                    ],
                    [
                        'name' => 'Windows 2003',
                        'value' => 'w2k3'
                    ],
                    [
                        'name' => 'Windows 2008',
                        'value' => 'w2k8'
                    ],
                    [
                        'name' => 'Windows Vista',
                        'value' => 'wvista'
                    ],
                    [
                        'name' => 'Windows 7',
                        'value' => 'win7'
                    ],
                    [
                        'name' => 'Windows 8',
                        'value' => 'win8'
                    ],
                    [
                        'name' => 'Windows 10',
                        'value' => 'win10'
                    ],
                    [
                        'name' => 'Windows 11',
                        'value' => 'win11'
                    ],
                    [
                        'name' => 'Linux 2.4 Kernel',
                        'value' => 'l24'
                    ],
                    [
                        'name' => 'Linux 6.x - 2.6 Kernel',
                        'value' => 'l26'
                    ],
                    [
                        'name' => 'solaris',
                        'value' => 'solaris'
                    ]
                ]
            ],
            [
                'name' => 'cputype',
                'type' => 'dropdown',
                'friendlyName' => 'CPU type',
                'description' => 'The CPU type of the VM',
                'options' => $cpuList
            ],
            [
                'name' => 'vcpu',
                'type' => 'number',
                'friendlyName' => 'vCPU cores',
                'description' => 'The number of vCPU cores of the VM',
            ],
            [
                'name' => 'sockets',
                'type' => 'number',
                'friendlyName' => 'Sockets',
                'description' => 'The number of sockets of the VM',
            ],

            [
                'type' => 'title',
                'friendlyName' => 'Clone options',
                'description' => 'Options for cloning a VM'
            ],
            [
                'name' => 'clone',
                'type' => 'boolean',
                'friendlyName' => 'Clone',
                'description' => 'Enable/disable cloning',
            ],
            [
                'name' => 'vmId',
                'type' => 'number',
                'friendlyName' => 'VM ID',
                'description' => 'The ID of the VM to clone',
            ],
        ];
    }

    private function getRequest($url)
    {
        $response = Http::withHeaders([
            'Authorization' => 'PVEAPIToken=' . ExtensionHelper::getConfig('Proxmox', 'username') . '=' . ExtensionHelper::getConfig('Proxmox', 'password'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withoutVerifying()->get(ExtensionHelper::getConfig('Proxmox', 'host') . ':' . ExtensionHelper::getConfig('Proxmox', 'port') . '/api2/json' . $url);

        return $response;
    }

    private function postRequest($url, $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'PVEAPIToken=' . ExtensionHelper::getConfig('Proxmox', 'username') . '=' . ExtensionHelper::getConfig('Proxmox', 'password'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withoutVerifying()->post(ExtensionHelper::getConfig('Proxmox', 'host') . ':' . ExtensionHelper::getConfig('Proxmox', 'port') . '/api2/json' . $url, $data);

        return $response;
    }

    private function deleteRequest($url)
    {
        $response = Http::withHeaders([
            'Authorization' => 'PVEAPIToken=' . ExtensionHelper::getConfig('Proxmox', 'username') . '=' . ExtensionHelper::getConfig('Proxmox', 'password'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withoutVerifying()->delete(ExtensionHelper::getConfig('Proxmox', 'host') . ':' . ExtensionHelper::getConfig('Proxmox', 'port') . '/api2/json' . $url);

        return $response;
    }

    private function putRequest($url, $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'PVEAPIToken=' . ExtensionHelper::getConfig('Proxmox', 'username') . '=' . ExtensionHelper::getConfig('Proxmox', 'password'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withoutVerifying()->put(ExtensionHelper::getConfig('Proxmox', 'host') . ':' . ExtensionHelper::getConfig('Proxmox', 'port') . '/api2/json' . $url, $data);

        return $response;
    }

    public function test()
    {
        $response = $this->getRequest('/nodes');
        if (!$response->json()) throw new Exception('Unable to get nodes');
        return true;
    }

    public function getUserConfig(Product $product)
    {
        $currentConfig = $product->settings;
        if ($currentConfig->where('name', 'type')->first()->value == 'lxc') {
            return [
                [
                    'name' => 'hostname',
                    'type' => 'text',
                    'friendlyName' => 'Hostname',
                    'description' => 'The hostname of the VM',
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'friendlyName' => 'Password',
                    'description' => 'The password of the VM',
                    'required' => true,
                ],
            ];
        }

        return [
            [
                'name' => 'hostname',
                'type' => 'text',
                'friendlyName' => 'Hostname',
                'description' => 'The hostname of the VM',
            ],
            [
                'name' => 'password',
                'type' => 'password',
                'friendlyName' => 'Password',
                'description' => 'The password of the VM',
                'required' => true,
            ],
        ];
    }

    public function createServer($user, $parmas, $order, $product, $configurableOptions)
    {
        $node = isset($configurableOptions['node']) ? $configurableOptions['node'] : $parmas['node'];
        $storage = isset($configurableOptions['storage']) ? $configurableOptions['storage'] : $parmas['storage'];
        $pool = isset($configurableOptions['pool']) ? $configurableOptions['pool'] : $parmas['pool'];
        $cores = isset($configurableOptions['cores']) ? $configurableOptions['cores'] : $parmas['cores'];
        $memory = isset($configurableOptions['memory']) ? $configurableOptions['memory'] : $parmas['memory'];
        $disk = isset($configurableOptions['disk']) ? $configurableOptions['disk'] : $parmas['disk'];
        $swap = isset($configurableOptions['swap']) ? $configurableOptions['swap'] : $parmas['swap'];
        $network_limit = isset($configurableOptions['network_limit']) ? $configurableOptions['network_limit'] : ($parmas['network_limit'] ?? null);
        $cpu = isset($configurableOptions['cpu']) ? $configurableOptions['cpu'] : ($parmas['cpu'] ?? null);

        $vmid = $this->getRequest('/cluster/nextid')->json()['data'];

        // Assign it to the orderProduct for further use
        ExtensionHelper::setOrderProductConfig('vmid', $vmid, $product->id);
        $postData = [];

        $currentConfig = $product->product->settings;
        $postData = [];
        $vmType = $currentConfig->where('name', 'type')->first()->value;
        if ($currentConfig->where('name', 'clone')->first()->value == '1') {
            $postData = [
                'newid' => $vmid,
                'target' => $node,
                'full' => 1,
            ];
            isset($parmas['pool']) && $postData['pool'] = $parmas['pool'];
            $response = $this->postRequest('/nodes/' . $node . '/' . $vmType . '/' . $parmas['vmId'] . '/clone', $postData);
            if (!$response->json()) throw new Exception('Unable to clone server');

            // Update hardware
            $postData = [
                'cores' => $cores,
                'memory' => $memory,
                'cipassword' => $parmas['config']['password'],
            ];
            $response = $this->putRequest('/nodes/' . $node . '/' . $vmType . '/' . $vmid . '/config', $postData);
            if (!$response->json()) throw new Exception('Unable to update hardware');

            // Get disk
            $disk = $this->getRequest('/nodes/' . $node . '/' . $vmType . '/' . $vmid . '/config')->json()['data'];
            $disk = explode('order=', $disk['boot'])[1];
            $disk = explode(',', $disk)[0];
            $postData = [
                'disk' => $disk,
                'size' => $parmas['disk'] . 'G',
            ];
            $response = $this->putRequest('/nodes/' . $node . '/' . $vmType . '/' . $vmid . '/resize', $postData);
            return true;
        } else if ($vmType == 'lxc') {
            $postData = [
                'vmid' => $vmid,
                'node' => $node,
                'storage' => $storage,
                'cores' => $cores,
                'memory' => $memory,
                'onboot' => 1,
                'ostemplate' => $parmas['template'],
                'ostype' => $parmas['ostypelxc'],
                'description' => $parmas['config']['hostname'],
                'hostname' => $parmas['config']['hostname'],
                'password' => $parmas['config']['password'],
                'swap' => $swap ?? 512,
                'unprivileged' => isset($parmas['unprivileged']) ? 1 : 0,
                'net0' => 'name=test' . ',bridge=' . $parmas['bridge'] . ',' . (isset($parmas['firewall']) ? 'firewall=1' : 'firewall=0') . (isset($network_limit) ? ',rate=' . $network_limit : ''),
            ];
            $ips = isset($configurableOptions['ips']) ? $configurableOptions['ips'] : ($parmas['ips'] ?? null);
            if (isset($ips)) {
                $ips = explode(',', $ips);
                // Get all ips which are not used
                $usedIps = OrderProduct::where('product_id', $product->product->id)->where('status', '!=', 'terminated')->with('config')->get();
                $usedIps = $usedIps->map(function ($orderProduct) {
                    return $orderProduct->config->where('key', 'ips')->first()->value ?? false;
                })->toArray();
                $ips = array_diff($ips, $usedIps);
                if (count($ips) == 0) throw new Exception('No more IPs available');
                // Only one
                $ips = $ips[0];
                ExtensionHelper::setOrderProductConfig('ips', $ips, $product->id);
                $postData['net0'] .= ',ip=' . $ips . '/24';
            }
            $gateway = isset($configurableOptions['gateway']) ? $configurableOptions['gateway'] : ($parmas['gateway'] ?? null);
            isset($gateway) ? $postData['net0'] .= ',gw=' . $gateway : null;
            isset($pool) ? $postData['pool'] = $pool : null;
            $response = $this->postRequest('/nodes/' . $node . '/lxc', $postData);
        } else {
            $socket = isset($configurableOptions['sockets']) ? $configurableOptions['sockets'] : ($parmas['sockets'] ?? null);
            $vcpu = isset($configurableOptions['vcpu']) ? $configurableOptions['vcpu'] : ($parmas['vcpu'] ?? null);
            $postData = [
                'vmid' => $vmid,
                'node' => $node,
                'storage' => $storage,
                'cores' => $cores,
                'memory' => $memory,
                'onboot' => 1,
                'sockets' => $socket ?? 1,
                'agent' => 1,
                'ostype' => $parmas['ostype'],
                'name' => $parmas['config']['hostname'],
                'description' => $parmas['config']['hostname'],
                $parmas['storageType'] . '0' => $parmas['storage'] . ':' . $disk . ',format=' . $parmas['storageFormat'],
                'net0' => $parmas['model'] . ',bridge=' . $parmas['bridge'] . ',' . (isset($parmas['firewall']) ? 'firewall=1' : 'firewall=0'),
            ];
            isset($pool) ? $postData['pool'] = $pool : null;
            isset($parmas['cloudinit']) ? $postData[$parmas['storageType'] . '0'] = $parmas['storage'] . ':cloudinit' . ',format=' . $parmas['storageFormat'] : null;
            isset($cpu) ? $postData['cpu'] = $cpu : null;
            isset($vcpu) ? $postData['vcpus'] = $vcpu : null;
            if (isset($parmas['os']) && $parmas['os'] == 'iso') {
                $postData['ide2'] = $parmas['iso'] . ',media=cdrom';
            }
            $response = $this->postRequest('/nodes/' . $node . '/qemu', $postData);
        }
        if (!$response->json()) throw new Exception('Unable to create server' . $response->body());
        return true;
    }

    public function suspendServer($user, $parmas, $order, $product, $configurableOptions)
    {
        throw new Exception('Not implemented');
    }

    public function unsuspendServer($user, $parmas, $order, $product, $configurableOptions)
    {
        throw new Exception('Not implemented');
    }

    public function terminateServer($user, $parmas, $order, $product, $configurableOptions)
    {
        $vmType = $parmas['type'];
        $vmid = $parmas['config']['vmid'];
        // Stop the VM first
        $response = $this->postRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/status/stop');
        // Delete the VM
        $postData = [
            'purge' => 1,
            'destroy-unreferenced-disks' => 1,
        ];
        $response = $this->deleteRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid, $postData);
        if (!$response->json()) throw new Exception('Unable to terminate server');
        return true;
    }


    public function getCustomPages($user, $parmas, $order, $product, $configurableOptions)
    {
        $vmType = $parmas['type'];
        $vmid = $parmas['config']['vmid'];
        $status = $this->getRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/status/current');
        if (!$status->json()) throw new Exception('Unable to get server status');
        $status = $status->json()['data'];

        $stats = $this->getRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/rrddata?timeframe=hour');
        if (!$stats->json()) throw new Exception('Unable to get server stats');
        $stats = $stats->json()['data'];

        // $vnc;
        // if ($vmType == 'lxc') $vnc = $this->postRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/vncproxy', ['websocket' => 1]);
        // else  $vnc = $this->postRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/vncproxy', ['websocket' => 1, 'generate-password' => 1]);
        // if (!$vnc->json()) throw new Exception('Unable to get server vnc');
        // $vnc = $vnc->json()['data'];
        // $websocket = ExtensionHelper::getConfig('Proxmox', 'host') . ':' . ExtensionHelper::getConfig('Proxmox', 'port') . '/?console=kvm&novnc=1&node=' . $parmas['node'] . '&resize=1&vmid=' . $vmid . '&path=api2/json/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/vncwebsocket/port/' . $vnc['port'] . '/"vncticket"/' . $vnc['ticket'];

        $users = $this->getRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/agent/get-users');
        if (!$users->json()) throw new Exception('Unable to get server users');
        $users = $users->json()['data'];

        $config = $this->getRequest('/nodes/' . $parmas['node'] . '/' . $vmType . '/' . $vmid . '/config');
        if (!$config->json()) throw new Exception('Unable to get server config');
        $config = $config->json()['data'];


        // Make url for iframe


        return [
            'name' => 'Proxmox',
            'template' => 'proxmox::control',
            'data' => [
                'status' => $status,
                'node' => $parmas['node'],
                'vmid' => $vmid,
                'stats' => $stats,
                // 'vnc' => $vnc,
                // 'websocket' => $websocket,
                'users' => $users,
                'config' => (object) $config,
            ],
            'pages' => [
                [
                    'template' => 'proxmox::stats',
                    'name' => 'Statistics',
                    'url' => 'stats',
                ],
                // [
                //     'template' => 'proxmox::vnc',
                //     'name' => 'VNC',
                //     'url' => 'vnc',
                // ],
                [
                    'template' => 'proxmox::settings',
                    'name' => 'Settings',
                    'url' => 'settings',
                ]
            ]
        ];
    }

    public function status(Request $request, OrderProduct $product)
    {
        if (!ExtensionHelper::hasAccess($product,  $request->user())) throw new Exception('You do not have access to this server');
        $request->validate([
            'status' => ['required', 'string', 'in:stop,start,reboot,shutdown'],
        ]);
        $data = ExtensionHelper::getParameters($product);
        $params = $data->config;
        $vmid = $params['config']['vmid'];
        $vmType = $params['type'];
        $postData = [
            'node' => $params['node'],
            'vmid' => $vmid,
        ];
        // Change status
        $status = $this->postRequest('/nodes/' . $params['node'] . '/' . $vmType . '/' . $vmid . '/status/' . $request->status,  $postData);
        if (!$status->json()) throw new Exception('Unable to ' . $request->status . ' server');

        // Return json response
        return response()->json([
            'status' => 'success',
            'message' => 'Server has been ' . $request->status . 'ed successfully'
        ]);
    }

    public function configure(Request $request, OrderProduct $product)
    {
        if (!ExtensionHelper::hasAccess($product,  $request->user())) throw new Exception('You do not have access to this server');
        $request->validate([
            'hostname' => ['required', 'string', 'max:255'],
        ]);
        $data = ExtensionHelper::getParameters($product);

        $params = $data->config;
        $vmid = $params['config']['vmid'];
        $vmType = $params['type'];

        $postData = [
            'hostname' => $request->hostname,
        ];
        $config = $this->putRequest('/nodes/' . $params['node'] . '/' . $vmType . '/' . $vmid . '/config',  $postData);

        if (!$config->json()) throw new Exception('Unable to configure server');
        return redirect()->back()->with('success', 'Server has been configured successfully');
    }
}
