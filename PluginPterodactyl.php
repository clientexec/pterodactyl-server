<?php

class PluginPterodactyl extends ServerPlugin
{

    public $features = [
        'packageName' => false,
        'testConnection' => true,
        'showNameservers' => false,
        'directlink' => true,
        'upgrades' => false
    ];
    public function getVariables()
    {
        $variables = [
            'Name' => [
                'type' => 'hidden',
                'description' => 'Used by CE to show plugin',
                'value' => 'Pterodactyl'
            ],
            'Description' => [
                'type' => 'hidden',
                'description' => 'Description viewable by admin in server settings',
                'value' => 'Pterodactyl Server Integration'
            ],
            'API Key' => [
                'type' => 'text',
                'description' => 'Pterodactyl API Key',
                'value' => '',
                'encryptable' => true
            ],
            'Use SSL' => [
                'type' => 'yesno',
                'description' => 'Use SSL to connect to Pterodactyl API?',
                'value' => '1'
            ],
            'Location Custom Field' => [
                "type" => 'text',
                'description' => 'Enter the name of the package custom field that will hold the Location (ID).',
                'value'       => ''
            ],
            'Username Custom Field' => [
                "type" => 'text',
                'description' => 'Enter the name of the package custom field that will hold the username.',
                'value'       => ''
            ],
            'Password Custom Field' => [
                "type" => 'text',
                'description' => 'Enter the name of the package custom field that will hold the password.',
                'value' => ''
            ],
            'Actions' => [
                'type' => 'hidden',
                'description' => 'Current actions that are active for this plugin per server',
                'value' => 'Create,Delete,Suspend,UnSuspend'
            ],
            'Registered Actions For Customer' => [
                'type' => 'hidden',
                'description' => 'Current actions that are active for this plugin per server for customers',
                'value' => ''
            ],
            'package_addons' => [
                'type' => 'hidden',
                'description' => 'Supported signup addons variables',
                'value' => []
            ],
            'package_vars' => [
                'type' => 'hidden',
                'description' => 'Whether package settings are set',
                'value' => '1',
            ],
            'package_vars_values' => [
                'type'  => 'hidden',
                'description' => lang('Package Settings'),
                'value' => [
                    'nest_id' => [
                        'type' => 'text',
                        'label' => 'Nest ID',
                        'description' => 'Nest ID',
                        'value' => '',
                    ],
                    'egg_id' => [
                        'type' => 'text',
                        'label' => 'Egg ID',
                        'description' => 'Egg ID',
                        'value' => '',
                    ],
                    'cpu' => [
                        'type' => 'text',
                        'label' => 'CPU Limit (%)',
                        'description' => 'CPU Limit (%)',
                        'value' => '',
                    ],
                    'disk' => [
                        'type' => 'text',
                        'label' => 'Disk Space (MB)',
                        'description' => 'Disk Space (MB)',
                        'value' => '',
                    ],
                    'memory' => [
                        'type' => 'text',
                        'label' => 'Memory (MB)',
                        'description' => 'Memory (MB)',
                        'value' => '',
                    ],
                    'swap' => [
                        'type' => 'text',
                        'label' => 'Swap (MB)',
                        'description' => 'Swap (MB)',
                        'value' => '',
                    ],
                    'io' => [
                        'type' => 'text',
                        'label' => 'IO Weight',
                        'description' => 'IO Weight',
                        'value' => '500',
                    ],
                    'disable_oom' => [
                        'type' => 'yesno',
                        'label' => 'Disable OOM?',
                        'description' => 'Should OOM be disabled?',
                        'value' => '0',
                    ],
                    'dedicated_ip' => [
                        'type' => 'yesno',
                        'label' => 'Dedicated IP?',
                        'description' => 'Should a dedicated IP be assigned?',
                        'value' => '0',
                    ],
                    'databases' => [
                        'type' => 'text',
                        'label' => 'Databases',
                        'description' => 'How many databases the client can create',
                        'value' => '0',
                    ],
                    'backups' => [
                        'type' => 'text',
                        'label' => 'Backups',
                        'description' => 'How many backups the client can create',
                        'value' => '0',
                    ],
                    'allocations' => [
                        'type' => 'text',
                        'label' => 'Allocations',
                        'description' => 'How many allocations the client can create',
                        'value' => '0',
                    ]
                ]
            ]
        ];

        return $variables;
    }

    public function validateCredentials($args)
    {
    }

    public function doDelete($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->delete($args);
        return 'Package has been deleted.';
    }

    public function doCreate($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->create($args);
        return 'Package has been created.';
    }

    public function doUpdate($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->update($args);
        return 'Package has been updated.';
    }

    public function doSuspend($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->suspend($args);
        return 'Package has been suspended.';
    }

    public function doUnSuspend($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->unsuspend($args);
        return 'Package has been unsuspended.';
    }

    public function unsuspend($args)
    {
        $serverId = $this->getServerId($args);
        $response = $this->api($args, 'servers/' . $serverId . '/unsuspend', [], 'POST');
        if ($response['statusCode'] != 204) {
            $this->handleError($response);
        }
    }

    public function suspend($args)
    {
        $serverId = $this->getServerId($args);
        $response = $this->api($args, 'servers/' . $serverId . '/suspend', [], 'POST');

        if ($response['statusCode'] != 204) {
            $this->handleError($response);
        }
    }

    public function delete($args)
    {
        $serverId = $this->getServerId($args);
        $response = $this->api($args, 'servers/' . $serverId, [], 'DELETE');
        if ($response['statusCode'] != 204) {
            $this->handleError($response);
        }
    }

    public function update($args)
    {
    }

    public function create($args)
    {
        $userPackage = new UserPackage($args['package']['id']);

        // Check if user exists:
        $response = $this->api($args, 'users/external/' . $args['customer']['id']);
        if ($response['statusCode'] == 404) {
            // check if a user exists from email instead (created before CE was used?)
            $response = $this->api($args, 'users?filter[email]=' . urlencode($args['customer']['email']));
            if ($response['meta']['pagination']['total'] == 0) {
                if ($args['server']['variables']['plugin_pterodactyl_Username_Custom_Field'] != '') {
                    $username = $userPackage->getCustomField(
                        $args['server']['variables']['plugin_pterodactyl_Username_Custom_Field'],
                        CUSTOM_FIELDS_FOR_PACKAGE
                    );

                    $args['server']['variables']['plugin_pterodactyl_Username_Custom_Field'];
                } else {
                    $username = CE_Lib::generateUsername();
                    $userPackage->setCustomField($args['server']['variables']['plugin_pterodactyl_Username_Custom_Field'], $username, CUSTOM_FIELDS_FOR_PACKAGE);
                }
                $response = $this->api($args, 'users', [
                    'username' => $username,
                    'password' => $userPackage->getCustomField(
                        $args['server']['variables']['plugin_pterodactyl_Password_Custom_Field'],
                        CUSTOM_FIELDS_FOR_PACKAGE
                    ),
                    'email' => $args['customer']['email'],
                    'first_name' => $args['customer']['first_name'],
                    'last_name' => $args['customer']['last_name'],
                    'external_id' => $args['customer']['id'],

                ], 'POST');
                $userId = $response['attributes']['id'];
            } else {
                $userId = $response['data'][0]['attributes']['id'];
            }
        } else {
            $userId = $response['attributes']['id'];
        }
        $nestId = $args['package']['variables']['nest_id'];
        $eggId = $args['package']['variables']['egg_id'];

        $eggData = $this->api($args, 'nests/' . $nestId . '/eggs/' . $eggId . '?include=variables');
        $environment = [];
        foreach ($eggData['attributes']['relationships']['variables']['data'] as $key => $val) {
            $attr = $val['attributes'];
            $var = $attr['env_variable'];
            $default = $attr['default_value'];
            $environment[$var] = $default;
        }

        if ($args['server']['variables']['plugin_pterodactyl_Location_Custom_Field'] != '') {
            $locationId = $userPackage->getCustomField($args['server']['variables']['plugin_pterodactyl_Location_Custom_Field'], CUSTOM_FIELDS_FOR_PACKAGE);
        }

        $data = [
            'name' => $args['package']['name'] . " - ID #" . $args['package']['id'],
            'user' => $userId,
            'nest' => $nestId,
            'egg' => $eggId,
            'docker_image' => $eggData['attributes']['docker_image'],
            'startup' => $eggData['attributes']['startup'],
            'oom_disabled' => $args['package']['variables']['disable_oom'],
            'limits' => [
                'memory' => $args['package']['variables']['memory'],
                'swap' => $args['package']['variables']['swap'],
                'io' => $args['package']['variables']['io'],
                'cpu' => $args['package']['variables']['cpu'],
                'disk' => $args['package']['variables']['disk'],
            ],
            'feature_limits' => [
                'databases' => $args['package']['variables']['databases'],
                'allocations' => $args['package']['variables']['allocations'],
                'backups' => $args['package']['variables']['backups'],
            ],
            'deploy' => [
                'locations' => [$locationId],
                'dedicated_ip' => $args['package']['variables']['dedicated_ip'],
                'port_range' => [],
            ],
            'environment' => $environment,
            'start_on_completion' => true,
            'external_id' => $args['package']['id'],
        ];

        $server = $this->api($args, 'servers', $data, 'POST');
        if ($server['statusCode'] != 204 && $server['statusCode'] != 201) {
            $this->handleError($server);
        }
    }

    public function testConnection($args)
    {
        CE_Lib::log(4, 'Testing connection to Pterodactyl');
        $result = $this->api($args, 'nodes');
        if ($result['statusCode'] != 200) {
            $errorMessage = 'Connection to server failed.';
            if ($result['statusCode'] == 403) {
                $errorMessage = 'Invalid API Key';
            }
            throw new CE_Exception($errorMessage);
        }
    }

    public function getAvailableActions($userPackage)
    {
        $args = $this->buildParams($userPackage);
        $server = $this->getServerInfo($args);
        $actions = [];
        if ($server['statusCode'] == 404) {
            $actions[] = 'Create';
        } else {
            $actions[] = 'Delete';
            if ($server['attributes']['suspended'] == 1) {
                $actions[] = 'UnSuspend';
            } else {
                $actions[] = 'Suspend';
            }
        }
        return $actions;
    }

    private function api($params, $endPoint, $data = [], $method = 'GET')
    {
        $url = 'https://';
        if ($params['server']['variables']['plugin_pterodactyl_Use_SSL'] == '0') {
            $url = 'http://';
        }
        $url .= $params['server']['variables']['ServerHostName'] . '/api/application/' . $endPoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $caPathOrFile = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
        if (is_dir($caPathOrFile)) {
            curl_setopt($ch, CURLOPT_CAPATH, $caPathOrFile);
        } else {
            curl_setopt($ch, CURLOPT_CAINFO, $caPathOrFile);
        }
        CE_Lib::log(4, "Pterodactyl Request to: $url");

        $headers = [
            "Authorization: Bearer " . $params['server']['variables']['plugin_pterodactyl_API_Key'],
            "Accept: Application/vnd.pterodactyl.v1+json",
        ];

        if ($method === 'POST' || $method === 'PATCH') {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $response['statusCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response['statusCode'] == 0) {
            throw new CE_Exception(curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    private function getServerId($args)
    {
        $response = $this->api($args, 'servers/external/' . $args['package']['id'], [], 'GET');
        if ($response['statusCode'] == 200) {
            return $response['attributes']['id'];
        }
        return false;
    }

    private function getServerInfo($args)
    {
        return $this->api($args, 'servers/external/' . $args['package']['id'], [], 'GET');
    }

    private function handleError($response)
    {
        $errors = [];
        foreach ($response['errors'] as $error) {
            $errors[] = $error['detail'];
        }
        throw new CE_Exception(implode("\n", $errors));
    }

    public function getDirectLink($userPackage, $getRealLink = true, $fromAdmin = false, $isReseller = false)
    {
        $linkText = $this->user->lang('Login to Panel');
        $args = $this->buildParams($userPackage);
        $url = 'https://';
        if ($args['server']['variables']['plugin_pterodactyl_Use_SSL'] == '0') {
            $url = 'http://';
        }
        $url .= $args['server']['variables']['ServerHostName'];

        $server = $this->getServerInfo($args);
        $url .= '/server/' . $server['attributes']['identifier'];

        if ($fromAdmin) {
            $cmd = 'panellogin';
            return [
                'cmd' => $cmd,
                'label' => $linkText
            ];
        } elseif ($getRealLink) {
            return array(
                'link'    => '<li><a target="_blank" href="' . $url . '">' . $linkText . '</a></li>',
                'rawlink' =>  $url,
                'form'    => ''
            );
        } else {
            $link = 'index.php?fuse=clients&controller=products&action=openpackagedirectlink&packageId=' . $userPackage->getId() . '&sessionHash=' . CE_Lib::getSessionHash();

            return array(
                'link' => '<li><a target="_blank" href="' . $url .  '">' . $linkText . '</a></li>',
                'form' => ''
            );
        }
    }

    public function dopanellogin($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $response = $this->getDirectLink($userPackage);
        return $response['rawlink'];
    }
}
