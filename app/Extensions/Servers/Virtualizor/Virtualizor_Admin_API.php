<?php
namespace App\Extensions\Servers\Virtualizor;

class Virtualizor_Admin_API
{
    public $key = '';
    public $pass = '';
    public $ip = '';
    public $port = 4085;
    public $protocol = 'https';
    public $error = [];

    /**
     * Contructor.
     *
     * @author       Pulkit Gupta
     *
     * @param string $ip IP of the NODE
     * @param string $key The API KEY of your NODE
     * @param string $pass The API Password of your NODE
     * @param int $port (Optional) The port to connect to. Port 4085 is the default. 4084 is non-SSL
     *
     * @return null
     */
    public function Virtualizor_Admin_API($ip, $key, $pass, $port = 4085)
    {
        $this->key = $key;
        $this->pass = $pass;
        $this->ip = $ip;
        $this->port = $port;
        if ($port != 4085) {
            $this->protocol = 'http';
        }
    }

    /**
     * Dumps a variable.
     *
     * @author       Pulkit Gupta
     *
     * @param array $re the Array or any other variable
     *
     * @return null
     */
    public function r($re)
    {
        echo '<pre>';
        print_r($re);
        echo '</pre>';
    }

    /**
     * Unserializes a string.
     *
     * @author       Pulkit Gupta
     *
     * @param string $str The serialized string
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function _unserialize($str)
    {
        $var = @unserialize($str);

        if (empty($var)) {
            preg_match_all('!s:(\d+):"(.*?)";!s', $str, $matches);
            foreach ($matches[2] as $mk => $mv) {
                $tmp_str = 's:' . strlen($mv) . ':"' . $mv . '";';
                $str = str_replace($matches[0][$mk], $tmp_str, $str);
            }
            $var = @unserialize($str);
        }

        // If it is still empty false
        if (empty($var)) {
            return false;
        } else {
            return $var;
        }
    }

    /**
     * Make an API Key.
     *
     * @author       Pulkit Gupta
     *
     * @param string $key An 8 bit random string
     * @param string $pass The API Password of your NODE
     *
     * @return string The new APIKEY which will be used to query
     */
    public function make_apikey($key, $pass)
    {
        return $key . md5($pass . $key);
    }

    /**
     * Generates a random string for the given length.
     *
     * @author       Pulkit Gupta
     *
     * @param int $length The length of the random string to be generated
     *
     * @return string The generated random string
     */
    public function generateRandStr($length)
    {
        $randstr = '';
        for ($i = 0; $i < $length; ++$i) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } elseif ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }

        return strtolower($randstr);
    }

    /**
     * Makes an API request to the server to do a particular task.
     *
     * @author       Pulkit Gupta
     *
     * @param string $path The action you want to do
     * @param array $post An array of DATA that should be posted
     * @param array $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function call($path, $data = [], $post = [], $cookies = [])
    {
        $key = $this->generateRandStr(8);
        $apikey = $this->make_apikey($key, $this->pass);

        $url = $this->protocol . '://' . $this->ip . ':' . $this->port . '/' . $path;
        $url .= (strstr($url, '?') ? '' : '?');
        $url .= '&api=serialize&apikey=' . rawurlencode($apikey);

        // Pass some data if there
        if (!empty($data)) {
            $url .= '&apidata=' . rawurlencode(base64_encode(serialize($data)));
        }
        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Time OUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // UserAgent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Corwinus');

        // Cookies
        if (!empty($cookies)) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
        }

        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Get response from the server.
        $resp = curl_exec($ch);

        // The following line is a method to test
        // if(preg_match('/sync/is', $url)) echo $resp;

        if (empty($resp)) {
            return false;
        }

        $r = @unserialize($resp);

        if (empty($r)) {
            return false;
        }
        curl_close($ch);

        return $r;
    }

    /**
     * Create a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param string $path The action you want to do
     * @param array $post An array of DATA that should be posted
     * @param array $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function addippool($post)
    {
        $post['addippool'] = 1;
        $path = 'index.php?act=addippool';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addips($post)
    {
        $post['submitip'] = 1;
        $path = 'index.php?act=addips';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addiso($post)
    {
        $path = 'index.php?act=addiso';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deleteiso($post)
    {
        $path = 'index.php?act=iso';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addplan($post)
    {
        $post['addplan'] = 1;
        $path = 'index.php?act=addplan';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function mediagroups($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=mediagroups';
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=mediagroups&mgid=' . $post['mgid'] . '&mg_name=' . $post['mg_name'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function addserver($post)
    {
        $post['addserver'] = 1;
        $path = 'index.php?act=addserver';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function servergroups($post = 0)
    {
        $path = 'index.php?act=servergroups';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addtemplate($post)
    {
        $post['addtemplate'] = 1;
        $path = 'index.php?act=addtemplate';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function adduser($post = 0)
    {
        $path = 'index.php?act=adduser';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    /**
     * Create a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     * @param array $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function addvs($post, $cookies = '')
    {
        $path = 'index.php?act=addvs';
        $post = $this->clean_post($post);
        $ret = $this->call($path, '', $post, $cookies);

        return [
            'title' => $ret['title'],
            'error' => @empty($ret['error']) ? [] : $ret['error'],
            'vs_info' => $ret['newvs'],
            'globals' => $ret['globals'],
        ];
    }

    /**
     * Create a VPS (V2 Method).
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     * @param array $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function addvs_v2($post, $cookies = '')
    {
        $path = 'index.php?act=addvs';
        $post['addvps'] = 1;
        $post['node_select'] = 1;
        $ret = $this->call($path, '', $post, $cookies);

        return [
            'title' => $ret['title'],
            'error' => @empty($ret['error']) ? [] : $ret['error'],
            'vs_info' => $ret['newvs'],
            'globals' => $ret['globals'],
            'done' => $ret['done'] ?? 0,
        ];
    }

    public function addiprange($post)
    {
        $path = 'index.php?act=addiprange';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editiprange($post)
    {
        $path = 'index.php?act=editiprange&ipid=' . $post['ipid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function iprange($page, $reslen, $post)
    {
        if (empty($post)) {
            $path = 'index.php?act=ipranges&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        } elseif (isset($post['delete'])) {
            $path = 'index.php?act=ipranges';
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=ipranges&ipsearch=' . $post['ipsearch'] . '&ippoolsearch=' . $post['ippoolsearch'] . '&lockedsearch=' . $post['lockedsearch'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function addsg($post)
    {
        $post['addsg'] = 1;
        $path = 'index.php?act=addsg';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editsg($post)
    {
        $post['editsg'] = 1;
        $path = 'index.php?act=editsg&sgid=' . $post['sgid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deletesg($post)
    {
        $path = 'index.php?act=servergroups';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function listbackupplans($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=backup_plans&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addbackupplan($post = [])
    {
        $post['addbackup_plan'] = 1;
        $path = 'index.php?act=addbackup_plan';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editbackupplan($post = [])
    {
        $post['editbackup_plan'] = 1;
        $path = 'index.php?act=editbackup_plan';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deletebackupplan($post)
    {
        $path = 'index.php?act=backup_plans';
        $ret = $this->call($path, [], $post);
        unset($ret['backup_plans']);

        return $ret;
    }

    public function backupservers($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=backupservers&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deletebackupservers($post)
    {
        $path = 'index.php?act=backupservers';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function testbackupservers($post)
    {
        $path = 'index.php?act=backupservers';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addbackupserver($post)
    {
        $post['addbackupserver'] = 1;
        $path = 'index.php?act=addbackupserver';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editbackupserver($post)
    {
        $post['editbackupserver'] = 1;
        $path = 'index.php?act=editbackupserver&id=' . $post['id'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addstorage($post)
    {
        $post['addstorage'] = 1;
        $path = 'index.php?act=addstorage';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function storages($post = [], $page = 1, $reslen = 50)
    {
        $path = 'index.php?act=storage&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editstorage($post)
    {
        $post['editstorage'] = 1;
        $path = 'index.php?act=editstorage&stid=' . $post['stid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function orhpaneddisks($post = [])
    {
        $path = 'index.php?act=orphaneddisks';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function adddnsplan($post)
    {
        $post['adddnsplan'] = 1;
        $path = 'index.php?act=adddnsplan';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function listdnsplans($page = 1, $reslen = 50, $post = [])
    {
        if (!isset($post['planname'])) {
            $path = 'index.php?act=dnsplans';
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=dnsplans&planname=' . $post['planname'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function edit_dnsplans($post = [])
    {
        $post['editdnsplan'] = 1;
        $path = 'index.php?act=editdnsplan&dnsplid=' . $post['dnsplid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function delete_dnsplans($post)
    {
        $path = 'index.php?act=dnsplans';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function add_admin_acl($post)
    {
        $path = 'index.php?act=add_admin_acl';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function admin_acl($post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=admin_acl';
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=admin_acl';
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function edit_admin_acl($post = [])
    {
        $path = 'index.php?act=edit_admin_acl&aclid=' . $post['aclid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addmg($post)
    {
        $post['addmg'] = 1;
        $path = 'index.php?act=addmg';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editmg($post)
    {
        $post['editmg'] = 1;
        $path = 'index.php?act=editmg&mgid=' . $post['mgid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function delete_mg($post)
    {
        $path = 'index.php?act=mediagroups&delete=' . $post['delete'];
        $ret = $this->call($path);

        return $ret;
    }

    public function add_distro($post)
    {
        $post['add_distro'] = 1;
        $path = 'index.php?act=add_distro';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function edit_distro($post)
    {
        $post['add_distro'] = 1;
        $path = 'index.php?act=add_distro&edit=' . $post['edit'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function list_distros($post = 0)
    {
        if (empty($post)) {
            $path = 'index.php?act=list_distros';
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=list_distros&delete=' . $post['delete'];
            $ret = $this->call($path);
        }

        return $ret;
    }

    public function list_euiso($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=euiso&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function delete_euiso($post)
    {
        $path = 'index.php?act=euiso';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function list_recipes($page = 1, $reslen = 50, $post = [])
    {
        if (!isset($post['rid'])) {
            $path = 'index.php?act=recipes&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=recipes&rid=' . $post['rid'] . '&rname=' . $post['rname'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function add_recipes($post)
    {
        $post['addrecipe'] = 1;
        $path = 'index.php?act=addrecipe';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editrecipe($post)
    {
        $post['editrecipe'] = 1;
        $path = 'index.php?act=editrecipe&rid=' . $post['rid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    // The recipe function deletes activates and deactivates a recipes
    public function recipes($post)
    {
        $path = 'index.php?act=recipes';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function tasks($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=tasks';
        // $ret = $this->call($path);
        } elseif (isset($post['showlogs'])) {
            $path = 'index.php?act=tasks';
        } else {
            $path = 'index.php?act=tasks&actid=' . $post['actid'] . '&vpsid=' . $post['vpsid'] . '&username=' . $post['username'] . '&action=' . $post['action'] . '&status=' . $post['status'] . '&order=' . $post['order'] . '&page=' . $page . '&reslen=' . $reslen;
        }
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addpdns($post)
    {
        $post['addpdns'] = 1;
        $path = 'index.php?act=addpdns';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function adminindex()
    {
        $path = 'index.php?act=adminindex';
        $res = $this->call($path);

        return $res;
    }

    public function apidoings()
    {
    }

    public function backup($post)
    {
        $path = 'index.php?act=backup';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function bandwidth($post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=bandwidth';
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=bandwidth&show=' . $post['show'];
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    /**
     * Cleaning the POST variables.
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     * @param array $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function clean_post(&$post, $edit = 0)
    {
        $post['serid'] = !isset($post['serid']) ? 0 : (int) $post['serid'];
        $post['uid'] = !isset($post['uid']) ? 0 : (int) $post['uid'];
        $post['plid'] = !isset($post['plid']) ? 0 : (int) $post['plid'];
        $post['osid'] = !isset($post['osid']) ? 0 : (int) $post['osid'];
        $post['iso'] = !isset($post['iso']) ? 0 : (int) $post['iso'];
        $post['space'] = !isset($post['space']) ? 10 : $post['space'];
        $post['ram'] = !isset($post['ram']) ? 512 : (int) $post['ram'];
        $post['swapram'] = !isset($post['swapram']) ? 1024 : (int) $post['swapram'];
        $post['bandwidth'] = !isset($post['bandwidth']) ? 0 : (int) $post['bandwidth'];
        $post['network_speed'] = !isset($post['network_speed']) ? 0 : (int) $post['network_speed'];
        $post['cpu'] = !isset($post['cpu']) ? 1000 : (int) $post['cpu'];
        $post['cores'] = !isset($post['cores']) ? 4 : (int) $post['cores'];
        $post['cpu_percent'] = !isset($post['cpu_percent']) ? 100 : (int) $post['cpu_percent'];
        $post['vnc'] = !isset($post['vnc']) ? 1 : (int) $post['vnc'];
        $post['vncpass'] = !isset($post['vncpass']) ? 'test' : $post['vncpass'];
        $post['sec_iso'] = !isset($post['sec_iso']) ? 0 : $post['sec_iso'];
        $post['kvm_cache'] = !isset($post['kvm_cache']) ? 0 : $post['kvm_cache'];
        $post['io_mode'] = !isset($post['io_mode']) ? 0 : $post['io_mode'];
        $post['vnc_keymap'] = !isset($post['vnc_keymap']) ? 'en-us' : $post['vnc_keymap'];
        $post['nic_type'] = !isset($post['nic_type']) ? 'default' : $post['nic_type'];
        $post['osreinstall_limit'] = !isset($post['osreinstall_limit']) ? 0 : (int) $post['osreinstall_limit'];
        $post['mgs'] = !isset($post['mgs']) ? 0 : $post['mgs'];
        $post['tuntap'] = !isset($post['tuntap']) ? 0 : $post['tuntap'];
        $post['virtio'] = !isset($post['virtio']) ? 0 : $post['virtio'];
        if (isset($post['hvm'])) {
            $post['hvm'] = $post['hvm'];
        }
        $post['noemail'] = !isset($post['noemail']) ? 0 : $post['noemail'];
        $post['boot'] = !isset($post['boot']) ? 'dca' : $post['boot'];
        $post['band_suspend'] = !isset($post['band_suspend']) ? 0 : $post['band_suspend'];
        $post['vif_type'] = !isset($post['vif_type']) ? 'netfront' : $post['vif_type'];
        if ($edit == 0) {
            $post['addvps'] = !isset($post['addvps']) ? 1 : (int) $post['addvps'];
        } else {
            $post['editvps'] = !isset($post['editvps']) ? 1 : $post['editvps'];
            $post['acpi'] = !isset($post['acpi']) ? 1 : $post['acpi'];
            $post['apic'] = !isset($post['apic']) ? 1 : $post['apic'];
            $post['pae'] = !isset($post['pae']) ? 1 : $post['pae'];
            $post['dns'] = !isset($post['dns']) ? ['4.2.2.1', '4.2.2.2'] : $post['dns'];
            $post['editvps'] = !isset($post['editvps']) ? 1 : (int) $post['editvps'];
        }

        return $post;
    }

    public function cluster()
    {
    }

    public function config($post = [])
    {
        $path = 'index.php?act=config';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function config_slave($post = [])
    {
        $path = 'index.php?act=config_slave';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    /**
     * Get CPU usage details.
     *
     * @author       Pulkit Gupta
     *
     * @return array The unserialised array is returned on success or
     *               empty array is returned on failure
     */
    public function cpu($serverid = 0)
    {
        $path = 'index.php?act=manageserver&changeserid=' . $serverid;
        $ret = $this->call($path);

        return $ret['usage']['cpu'];
    }

    public function serverloads($post = [])
    {
        $path = 'index.php?act=serverloads';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function createssl($post)
    {
        $path = 'index.php?act=createssl';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function letsencrypt($post)
    {
        $path = 'index.php?act=letsencrypt';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function createtemplate($post)
    {
        $path = 'index.php?act=createtemplate';
        $post['createtemp'] = 1;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function server_stats($post)
    {
        $path = 'index.php?act=server_stats' . (!empty($post['serid']) ? '&changeserid=' . (int) $post['serid'] : '');
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function vps_stats($post)
    {
        $path = 'index.php?act=vps_stats' . (!empty($post['serid']) ? '&changeserid=' . (int) $post['serid'] : '');
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function databackup($post)
    {
        $path = 'index.php?act=databackup';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function listdbbackfiles()
    {
        $path = 'index.php?act=databackup';
        $ret = $this->call($path);

        return $ret;
    }

    public function createvpsbackup($post)
    {
        $path = 'index.php?act=editbackup_plan';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function vps_backup_list($post)
    {
        $path = 'index.php?act=vpsrestore&op=get_vps&vpsid=' . $post['vpsid'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function vpsrestore($post)
    {
        $post['restore'] = 1;
        $path = 'index.php?act=vpsrestore';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deletevpsbackup($post)
    {
        $path = 'index.php?act=vpsrestore';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function pdns($page, $reslen, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=pdns&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        } elseif (isset($post['test'])) {
            $path = 'index.php?act=pdns&test=' . $post['test'];
            $ret = $this->call($path);
        } elseif (isset($post['delete'])) {
            $path = 'index.php?act=pdns';
            $ret = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=pdns&pdns_name=' . $post['pdns_name'] . '&pdns_ipaddress=' . $post['pdns_ipaddress'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function rdns($post = [])
    {
        $path = 'index.php?act=rdns';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function domains($page = 1, $reslen = 50, $post = [])
    {
        if (!isset($post['del'])) {
            $path = 'index.php?act=domains&pdnsid=' . $post['pdnsid'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=domains&pdnsid=' . $post['pdnsid'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function delete_dnsrecords($post = [])
    {
        $path = 'index.php?act=dnsrecords&pdnsid=' . $post['pdnsid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function dnsrecords($page = 1, $reslen = 50, $post = [])
    {
        if (!isset($post['del'])) {
            $path = 'index.php?act=dnsrecords&pdnsid=' . $post['pdnsid'] . '&domain_id=' . $post['domain_id'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=dnsrecords&pdnsid=' . $post['pdnsid'] . '&domain_id=' . $post['domain_id'];
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function search_dnsrecords($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=dnsrecords&pdnsid=' . $post['pdnsid'] . '&domain_id=' . $post['domain_id'] . '&dns_name=' . $post['dns_name'] . '&dns_domain=' . $post['dns_domain'] . '&record_type=' . $post['record_type'] . '&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function add_dnsrecord($post = [])
    {
        $post['add_dnsrecord'] = 1;
        $path = 'index.php?act=add_dnsrecord&pdnsid=' . $post['pdnsid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function edit_dnsrecord($post = [])
    {
        $post['add_dnsrecord'] = 1;
        $path = 'index.php?act=add_dnsrecord&pdnsid=' . $post['pdnsid'] . '&edit=' . $post['edit'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editpdns($post = [])
    {
        $post['editpdns'] = 1;
        $path = 'index.php?act=editpdns&pdnsid=' . $post['pdnsid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function defaultvsconf($post)
    {
        $path = 'index.php?act=defaultvsconf';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    /**
     * Delete a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     *
     * @return bool 1 on success OR 0 on failure
     */
    public function delete_vs($vid)
    {
        $path = 'index.php?act=vs&delete=' . (int) $vid;
        $res = $this->call($path);

        return $res;
    }

    /**
     * Get Disk usage details.
     *
     * @author       Pulkit Gupta
     *
     * @return array The unserialised array is returned on success or
     *               empty array is returned on failure
     */
    public function disk($serverid = 0)
    {
        $path = 'index.php?act=manageserver&changeserid=' . $serverid;
        $ret = $this->call($path);

        return $ret['usage']['disk'];
    }

    public function webuzo($post = [])
    {
        $post['webuzo'] = 1;
        $path = 'index.php?act=webuzo';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function webuzo_scripts()
    {
        $path = 'index.php?act=webuzo';
        $ret = $this->call($path);

        return $ret;
    }

    public function editemailtemp($post)
    {
        $path = 'index.php?act=editemailtemp&temp=' . $post['temp'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function resetemailtemp($post)
    {
        $path = 'index.php?act=editemailtemp&temp=' . $post['temp'] . '&reset=' . $post['reset'];
        $ret = $this->call($path);

        return $ret;
    }

    public function billingsettings($post = [])
    {
        $post['editsettings'] = 1;
        $path = 'index.php?act=billing';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function resourcepricing($post = [])
    {
        $post['editsettings'] = 1;
        $path = 'index.php?act=resource_pricing';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addinvoice($post = [])
    {
        $post['addinvoice'] = 1;
        $path = 'index.php?act=addinvoice';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editinvoice($post = [])
    {
        $post['editinvoice'] = 1;
        $path = 'index.php?act=editinvoice&invoid=' . $post['invoid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function listinvoice($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=invoices&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deleteinvoice($post = [])
    {
        $path = 'index.php?act=invoices';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function addtransaction($post = [])
    {
        $post['addtransaction'] = 1;
        $path = 'index.php?act=addtransaction';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function edittransaction($post = [])
    {
        $post['edittransaction'] = 1;
        $path = 'index.php?act=edittransaction&trid=' . $post['trid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function listtransaction($page = 1, $reslen = 50, $post = [])
    {
        $path = 'index.php?act=transactions&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function deletetransactions($post = [])
    {
        $path = 'index.php?act=transactions';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function editippool($post)
    {
        $post['editippool'] = 1;
        $path = 'index.php?act=editippool&ippid=' . $post['ippid'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function deleteippool($ippid)
    {
        $path = 'index.php?act=ippool';
        $ret = $this->call($path, [], $ippid);

        return $ret;
    }

    public function editips($post)
    {
        $path = 'index.php?act=editips';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function delete_ips($post)
    {
        $path = 'index.php?act=ips';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function editplan($post)
    {
        $post['editplan'] = 1;
        $path = 'index.php?act=editplan&plid=' . $post['plid'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function editserver($post)
    {
        $post['editserver'] = 1;
        $path = 'index.php?act=editserver&serid=' . $post['serid'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function edittemplate($post)
    {
        $path = 'index.php?act=edittemplate';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function edituser($post)
    {
        $path = 'index.php?act=edituser&uid=' . $post['uid'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    /**
     * Create a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function editvs($post, $cookies = [])
    {
        $path = 'index.php?act=editvs&vpsid=' . $post['vpsid'];
        // $post = $this->clean_post($post, 1);
        $ret = $this->call($path, '', $post, $cookies);

        return [
            'title' => $ret['title'],
            'done' => $ret['done'],
            'error' => @empty($ret['error']) ? [] : $ret['error'],
            'vs_info' => $ret['vps'],
        ];
    }

    public function managevps($post)
    {
        $post['theme_edit'] = 1;
        $post['editvps'] = 1;
        $path = 'index.php?act=managevps&vpsid=' . $post['vpsid'];
        $ret = $this->call($path, [], $post);

        return [
            'title' => $ret['title'],
            'done' => $ret['done'],
            'error' => @empty($ret['error']) ? [] : $ret['error'],
            'vs_info' => $ret['vps'],
        ];
    }

    public function emailconfig($post)
    {
        $path = 'index.php?act=emailconfig';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function emailtemp($post = [])
    {
        $path = 'index.php?act=emailtemp';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function filemanager($post)
    {
        $path = 'index.php?act=filemanager';
        $res = $this->call($path, '', $post);

        return $res;
    }

    public function firewall($post)
    {
        $path = 'index.php?act=firewall';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function giveos()
    {
    }

    public function health()
    {
    }

    public function hostname($post)
    {
        $path = 'index.php?act=hostname';
        $res = $this->call($path, '', $post);

        return $res;
    }

    public function import($page, $reslen, $post)
    {
        $path = 'index.php?act=import';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function ippool($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=ippool&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path);
        } else {
            $path = 'index.php?act=ippool&poolname=' . $post['poolname'] . '&poolgateway=' . $post['poolgateway'] . '&netmask=' . $post['netmask'] . '&nameserver=' . $post['nameserver'] . '&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path);
        }

        return $res;
    }

    /**
     * Get list of IPs.
     *
     * @author       Pulkit Gupta
     *
     * @return array the unserialised array on success
     */
    public function ips($page, $reslen, $post)
    {
        if (empty($post)) {
            $path = 'index.php?act=ips&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=ips&ipsearch=' . $post['ipsearch'] . '&ippoolsearch=' . $post['ippoolsearch'] . '&macsearch=' . $post['macsearch'] . '&vps_search=' . $post['vps_search'] . '&servers_search=' . $post['servers_search'] . '&lockedsearch=' . $post['lockedsearch'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        }

        return $ret;
    }

    public function iso()
    {
        $path = 'index.php?act=iso';
        $ret = $this->call($path);

        return $ret;
    }

    public function kernelconf($post = 0)
    {
        $path = 'index.php?act=kernelconf';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function license()
    {
        $path = 'index.php?act=license';
        $ret = $this->call($path);

        return $ret;
    }

    /**
     * List VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param        int page number, if not specified then only 50 records are returned
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function listvs($page = 1, $reslen = 50, $search = [])
    {
        if (empty($search)) {
            $path = 'index.php?act=vs&page=' . $page . '&reslen=' . $reslen;
        } else {
            $path = 'index.php?act=vs&vpsid=' . $search['vpsid'] . '&vpsname=' . $search['vpsname'] . '&vpsip=' . $search['vpsip'] . '&vpshostname=' . $search['vpshostname'] . '&vsstatus=' . $search['vsstatus'] . '&vstype=' . $search['vstype'] . '&user=' . $search['user'] . '&serid=' . $search['serid'] . '&search=' . $search['search'];
        }

        $result = $this->call($path);
        $ret = $result['vs'];

        return $ret;
    }

    public function login()
    {
    }

    public function loginlogs($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=loginlogs&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=loginlogs&username=' . $post['username'] . '&ip=' . $post['ip'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }
        error_log('loginlogs: ' . print_r($ret, true));

        return $ret;
    }

    public function logs($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=logs&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=logs&id=' . $post['id'] . '&email=' . $post['email'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path, [], $post);
        }

        return $ret;
    }

    public function maintenance($post)
    {
        $path = 'index.php?act=maintenance';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function makeslave()
    {
    }

    public function os($post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=os';
        } else {
            $path = 'index.php?act=os&getos=' . $post['osids'][0];
        }
        $result = $this->call($path, [], $post);

        return $result;
    }

    public function ostemplates($page = 1, $reslen = 50)
    {
        $path = 'index.php?act=ostemplates&page=' . $page . '&reslen=' . $reslen;
        $result = $this->call($path);
        // $ret['title'] = $result['title'];
        // $ret['ostemplates'] = $result['ostemplates'];
        return $result;
    }

    public function delostemplates($post = [])
    {
        $path = 'index.php?act=ostemplates&delete=' . $post['delete'];
        $result = $this->call($path);
        $ret['title'] = $result['title'];
        $ret['done'] = $result['done'];
        $ret['ostemplates'] = $result['ostemplates'];

        return $ret;
    }

    public function performance()
    {
        $path = 'index.php?act=performance';
        $result = $this->call($path);

        return $result;
    }

    public function phpmyadmin()
    {
    }

    public function plans($page = 1, $reslen = 50, $search = [])
    {
        if (empty($search)) {
            $path = 'index.php?act=plans&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        } else {
            $path = 'index.php?act=plans&planname=' . $search['planname'] . '&ptype=' . $search['ptype'] . '&page=' . $page . '&reslen=' . $reslen;
            $ret = $this->call($path);
        }

        return $ret;
    }

    public function sort_plans($page = 1, $reslen = 50, $sort = [])
    {
        $path = 'index.php?act=plans&sortcolumn=' . $sort['sortcolumn'] . '&sortby=' . $sort['sortby'] . '&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path);

        return $ret;
    }

    public function delete_plans($post)
    {
        $path = 'index.php?act=plans&delete=' . $post['delete'];
        $ret = $this->call($path);

        return $ret;
    }

    public function list_user_plans($post = [], $page = 1, $reslen = 50)
    {
        $path = 'index.php?act=user_plans&page=' . $page . '&reslen=' . $reslen;
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function add_user_plans($post = [])
    {
        $post['adduser_plans'] = 1;
        $path = 'index.php?act=adduser_plans';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function edit_user_plans($post)
    {
        $post['edituser_plans'] = 1;
        $path = 'index.php?act=edituser_plans&uplid=' . $post['uplid'];
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    public function delete_user_plans($post = [])
    {
        $path = 'index.php?act=user_plans';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    /**
     * POWER OFF a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function poweroff($vid)
    {
        // Make the Request
        $res = $this->call('index.php?act=vs&action=poweroff&serid=0&vpsid=' . (int) $vid);

        return $res;
    }

    public function processes($post = [])
    {
        $path = 'index.php?act=processes';
        $ret = $this->call($path, [], $post);

        return $ret;
    }

    /**
     * Get RAM details.
     *
     * @author       Pulkit Gupta
     *
     * @return array The unserialised array is returned on success or
     *               empty array is returned on failure
     */
    public function ram($serverid = 0)
    {
        $path = 'index.php?act=manageserver&changeserid=' . $serverid;
        $ret = $this->call($path);

        return $ret['usage']['ram'];
    }

    /**
     * Rebuild a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param array $post An array of DATA that should be posted
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function rebuild($post)
    {
        $post['reos'] = 1;
        $path = 'index.php?act=rebuild' . (!empty($post['serid']) ? '&changeserid=' . (int) $post['serid'] : '');

        return $this->call($path, '', $post);
    }

    /**
     * RESTART a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function restart($vid)
    {
        // Make the Request
        $res = $this->call('index.php?act=vs&action=restart&serid=0&vpsid=' . (int) $vid);

        return $res;
    }

    public function restartservices($post)
    {
        $post['do'] = 1;
        $path = 'index.php?act=restartservices&service=' . $post['service'] . '&do=' . $post['do'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    /**
     * Current server information.
     *
     * @author       Pulkit Gupta
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function serverinfo()
    {
        $path = 'index.php?act=serverinfo';
        $result = $this->call($path);

        $ret = [];
        $ret['title'] = $result['title'];
        $ret['info']['path'] = $result['info']['path'];
        $ret['info']['key'] = $result['info']['key'];
        $ret['info']['pass'] = $result['info']['pass'];
        $ret['info']['kernel'] = $result['info']['kernel'];
        $ret['info']['num_vs'] = $result['info']['num_vs'];
        $ret['info']['version'] = $result['info']['version'];
        $ret['info']['patch'] = $result['info']['patch'];

        return $ret;
    }

    /**
     * List Servers.
     *
     * @author       Pulkit Gupta
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function servers($del_serid = 0)
    {
        if ($del_serid == 0) {
            $path = 'index.php?act=servers';
        } else {
            $path = 'index.php?act=servers&delete=' . $del_serid;
        }

        return $this->call($path);
    }

    public function server_force_delete($del_serid = 0)
    {
        if ($del_serid == 0) {
            $path = 'index.php?act=servers';
        } else {
            $path = 'index.php?act=servers&force=' . $del_serid;
        }

        return $this->call($path);
    }

    public function listservers()
    {
        $path = 'index.php?act=servers';

        return $this->call($path);
    }

    public function services($post = [])
    {
        $path = 'index.php?act=services';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function ssh()
    {
        /*	$path = 'index.php?act=ssh';
            $res = $this->call($path);
            return $res;*/
    }

    public function ssl($post = 0)
    {
        $path = 'index.php?act=ssl';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function sslcert()
    {
        /*	$path = 'index.php?act=sslcert';
            $res = $this->call($path);
            return $res;*/
    }

    /**
     * START a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function start($vid)
    {
        $res = $this->call('index.php?act=vs&action=start&serid=0&vpsid=' . (int) $vid);

        return $res;
    }

    /**
     * STOP a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function stop($vid)
    {
        // Make the Request
        $res = $this->call('index.php?act=vs&action=stop&serid=0&vpsid=' . (int) $vid);

        return $res;
    }

    /**
     * Gives status of a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param array $vids array of IDs of VMs
     *
     * @return array Contains the status info of the VMs
     */
    public function status($vids)
    {
        // Make the Request
        $res = $this->call('index.php?act=vs&vs_status=' . implode(',', $vids));

        return $res['status'];
    }

    /**
     * Suspends a VM of a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return int 1 if the VM is ON, 0 if its OFF
     */
    public function suspend($vid)
    {
        $path = 'index.php?act=vs&suspend=' . (int) $vid;
        $res = $this->call($path);

        return $res;
    }

    /**
     * Unsuspends a VM of a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return int 1 if the VM is ON, 0 if its OFF
     */
    public function unsuspend($vid)
    {
        $path = 'index.php?act=vs&unsuspend=' . (int) $vid;
        $res = $this->call($path);

        return $res;
    }

    public function suspend_net($vid)
    {
        $path = 'index.php?act=vs&suspend_net=' . $vid;
        $res = $this->call($path);

        return $res;
    }

    public function unsuspend_net($vid)
    {
        $path = 'index.php?act=vs&unsuspend_net=' . $vid;
        $res = $this->call($path);

        return $res;
    }

    public function tools()
    {
    }

    public function ubc($post)
    {
        $path = 'index.php?act=ubc';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function updates($post)
    {
        $path = 'index.php?act=updates';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function userlogs($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=userlogs&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path);
        } else {
            $path = 'index.php?act=userlogs&vpsid=' . $post['vpsid'] . '&email=' . $post['email'] . '&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path, [], $post);
        }

        return $res;
    }

    public function iplogs($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=iplogs&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path);
        } else {
            $path = 'index.php?act=iplogs&vpsid=' . $post['vpsid'] . '&ip=' . $post['ip'] . '&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path, [], $post);
        }

        return $res;
    }

    public function deleteiplogs($post)
    {
        if (!empty($post)) {
            $path = 'index.php?act=iplogs';
            $res = $this->call($path, [], $post);
        }

        return $res;
    }

    public function users($page = 1, $reslen = 50, $post = [])
    {
        if (empty($post)) {
            $path = 'index.php?act=users&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path, [], $post);
        } else {
            $path = 'index.php?act=users&uid=' . $post['uid'] . '&email=' . $post['email'] . '&type=' . $post['type'] . '&page=' . $page . '&reslen=' . $reslen;
            $res = $this->call($path, [], $post);
        }

        return $res;
    }

    public function delete_users($del_userid)
    {
        $path = 'index.php?act=users';
        $res = $this->call($path, [], $del_userid);

        return $res;
    }

    public function vnc($post)
    {
        $path = 'index.php?act=vnc&novnc=' . $post['novnc'];
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function vs($page = 1, $reslen = 50)
    {
        $path = 'index.php?act=vs&page=' . $page . '&reslen=' . $reslen;
        $res = $this->call($path);

        return $res;
    }

    public function vsbandwidth()
    {
        $path = 'index.php?act=vsbandwidth';
        $res = $this->call($path);

        return $res;
    }

    public function vscpu()
    {
        $path = 'index.php?act=vscpu';
        $res = $this->call($path);

        return $res;
    }

    public function vsram()
    {
        $path = 'index.php?act=vsram';
        $res = $this->call($path);

        return $res;
    }

    public function clonevps($post)
    {
        $path = 'index.php?act=clone';
        $post['migrate'] = 1;
        $post['migrate_but'] = 1;
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function migrate($post)
    {
        $path = 'index.php?act=migrate';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function haproxy($post)
    {
        $path = 'index.php?act=haproxy';
        $res = $this->call($path, [], $post);

        return $res;
    }

    public function listhaproxy($search = [], $page = 1, $reslen = 50)
    {
        if (empty($search)) {
            $path = 'index.php?act=haproxy&page=' . $page . '&reslen=' . $reslen;
        } else {
            $path = 'index.php?act=haproxy&s_id=' . $search['s_id'] . '&s_serid=' . (empty($search['s_serid']) ? '-1' : $search['s_serid']) . '&s_vpsid=' . $search['s_vpsid'] . '&s_protocol=' . (empty($search['s_protocol']) ? '-1' : $search['s_protocol']) . '&s_src_hostname=' . $search['s_src_hostname'] . '&s_src_port=' . $search['s_src_port'] . '&s_dest_ip=' . $search['s_dest_ip'] . '&s_dest_port=' . $search['s_dest_port'] . '&haproxysearch=' . $search['haproxysearch'];
        }

        $result = $this->call($path);
        $ret = $result['haproxydata'];

        return $ret;
    }
} // Class Ends
