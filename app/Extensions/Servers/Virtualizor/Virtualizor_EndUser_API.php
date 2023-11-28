<?php
namespace App\Extensions\Servers\Virtualizor;
//////////////////////////////////////////////////////////////
//===========================================================
// enduser.php (API)
//===========================================================
// SOFTACULOUS VIRTUALIZOR
// Version : 1.0
// Inspired by the DESIRE to be the BEST OF ALL
// ----------------------------------------------------------
// Started by: Alons
// Date:       8th Mar 2010
// Time:       23:00 hrs
// Site:       https://www.virtualizor.com/ (SOFTACULOUS VIRTUALIZOR)
// ----------------------------------------------------------
// Please Read the Terms of use at https://www.virtualizor.com
// ----------------------------------------------------------
//===========================================================
// (c)Softaculous Ltd.
//===========================================================
//////////////////////////////////////////////////////////////


class Virtualizor_EndUser_API {
	
	var $key = '';
	var $pass = '';
	var $ip = '';
	var $port = 4083;
	var $protocol = 'https';
	var $error = array();
	var $is_admin = false;
	
	/**
	 * Contructor
	 *
	 * @author       Pulkit Gupta
	 * @param        string $ip IP of the Control Panel
	 * @param        string $key The API KEY of your account
	 * @param        string $pass The API Password of your account
	 * @param        int $port (Optional) The port to connect to. Port 4083 is the default. 4082 is non-SSL
	 * @return       NULL
	 */
	function __construct($ip, $key, $pass, $port = 4083, $is_admin = false){
		$this->key = $key;
		$this->pass = $pass;
		$this->ip = $ip;
		$this->port = $port;
		if(!($port == 4083 || $port == 443)){
			$this->protocol = 'http';
		}
		$this->is_admin = $is_admin;
	}
	
	/**
	 * Dumps a variable
	 *
	 * @author       Pulkit Gupta
	 * @param        array $re The Array or any other variable.
	 * @return       NULL
	 */
	function r($re){
		echo '<pre>';
		print_r($re);
		echo '</pre>';	
	}
	
	/**
	 * Unserializes a string
	 *
	 * @author       Pulkit Gupta
	 * @param        string $str The serialized string
	 * @return       array The unserialized array on success OR false on failure
	 */
	function _unserialize($str){

		$var = @unserialize($str);
		
		if(empty($var)){
		
			preg_match_all('!s:(\d+):"(.*?)";!s', $str, $matches);
			foreach($matches[2] as $mk => $mv){
				$tmp_str = 's:'.strlen($mv).':"'.$mv.'";';
				$str = str_replace($matches[0][$mk], $tmp_str, $str);
			}
			$var = @unserialize($str);
		
		}
		
		//If it is still empty false
		if(empty($var)){
		
			return false;
		
		}else{
		
			return $var;
		
		}
	
	}
	
	/**
	 * Makes an API request to the server to do a particular task
	 *
	 * @author       Pulkit Gupta
	 * @param        string $path The action you want to do
	 * @param        array $post An array of DATA that should be posted
	 * @param        array $cookies An array FOR SENDING COOKIES
	 * @return       array The unserialized array on success OR false on failure
	 */
	function call($path, $post = array(), $cookies = array()){
				
		$url = ($this->protocol).'://'.$this->ip.':'.$this->port.'/'.$path;	
		$url .= (strstr($url, '?') ? '' : '?');	
		if(!empty($this->is_admin)) {
			$key = $this->generateRandStr(8);
			$apikey = $this->make_apikey($key, $this->pass);
			$url .= '&api=serialize&apikey='.rawurlencode($apikey);
		} else {
			$url .= '&api=serialize&apikey='.rawurlencode($this->key).'&apipass='.rawurlencode($this->pass);
		}
		
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
			
		// Time OUT
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			
		// UserAgent
		curl_setopt($ch, CURLOPT_USERAGENT, 'Virtualizor');
		
		// Cookies
		if(!empty($cookies)){
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
		}
		
		if(!empty($post)){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// Get response from the server.
		$resp = curl_exec($ch);		
		curl_close($ch);
		
		// The following line is a method to test
		//if(preg_match('/sync/is', $url)) 
		
		if(empty($resp)){
			return false;
		}
		
		$r = $this->_unserialize($resp);
		
		if(empty($r)){
			return false;
		}
		
		return $r;
		
	}
	
	/**
	 * Make an API Key
	 *
	 * @author       Pulkit Gupta
	 * @param        string $key An 8 bit random string
	 * @param        string $pass The API Password of your NODE
	 * @return       string The new APIKEY which will be used to query
	 */
	function make_apikey($key, $pass){
		return $key.md5($pass.$key);
	}
	
	/**
	 * Generates a random string for the given length
	 *
	 * @author       Pulkit Gupta
	 * @param        int $length The length of the random string to be generated
	 * @return       string The generated random string
	 */
	function generateRandStr($length){	
		$randstr = "";	
		for($i = 0; $i < $length; $i++){	
			$randnum = mt_rand(0,61);		
			if($randnum < 10){		
				$randstr .= chr($randnum+48);			
			}elseif($randnum < 36){		
				$randstr .= chr($randnum+55);			
			}else{		
				$randstr .= chr($randnum+61);			
			}		
		}	
		return strtolower($randstr);	
	}
	
	/**
	 * List the Virtual Servers in your account
	 *
	 * @author       Pulkit Gupta
	 * @return       array The array containing a list of Virtual Servers one has in their account
	 */
	function listvs($del_vpsid=0, $search = array()){
		
		$path = 'index.php?act=listvs';
		if(!empty($del_vpsid)){
			$path .= '&delvs='.$del_vpsid;
		}else if(!empty($search)){
			$path .= '&search=1';
			
			foreach($search as $k => $v){
				$path.='&'.$k.'='.$v;
			}
		}

		$result = $this->call($path);
		return $result;
	
	}
	
	function suspend($vpsid){
		$ret = $this->call('index.php?act=listvs&suspend='.$vpsid);
		return $ret;
	}
	
	function unsuspend($vpsid){
		$ret = $this->call('index.php?act=listvs&unsuspend='.$vpsid);
		return $ret;
	}
	
	function suspend_net($vpsid){
		$ret = $this->call('index.php?act=listvs&suspend_net='.$vpsid);
		return $ret;
	}
	
	function unsuspend_net($vpsid){
		$ret = $this->call('index.php?act=listvs&unsuspend_net='.$vpsid);
		return $ret;
	}
	/**
	 * MANAGE a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	 
	function vpsmanage($vid){
	
		//Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=vpsmanage');
		//Did it finish?
		return $res;	
	}
	
	//since V3.1.3
	function save_vertical_data($vid, $post){
		$post['save_ver_data'] = 1;
		$res = $this->call('index.php?svs='.$vid.'&act=vpsmanage', $post);
		return $res;
	}
	 
	 function vpsinfo($vid){
		return $this->vpsmanage($vid);
	 }
	 
	 function create($post){
		 
		 $res = $this->call('index.php?act=create', $post);
		 
		 if(empty($res['done'])){
			$error = $res['error'];
		}
		
		return $res;
	 }
	 
	/**
	 * EDIT a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	 
	 function editvm($vid,$post=array()){
		 
		 //Make the Request
		 $res = $this->call('index.php?vid='.$vid.'&act=editvm', $post);
		 
		//print_r($res['done']);
		 $ret['uid'] = $res['uid'];
		 $ret['act'] = $res['act'];
		 $ret['timezone'] = $res['timezone'];
		 $ret['timenow'] = $res['timenow'];
		 $ret['resources'] = $res['resources'];
		 $ret['usage'] = $res['usage'];
		 $ret['res_limit'] = $res['res_limit'];
		 $ret['username'] = $res['username'];
		 $ret['vps'] = $res['vps'];
		 $ret['done'] = $res['done'];
		
		 //Did it finish?
		if(!empty($ret['done'])){
			return $ret['vps'];
		}
		
		return $res['error'];
	 }
	 
	/**
	 * BACKING UP a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */

	function backup($vid, $post = array()){
		
		//MAKE the Request
		$res = $this->call('index.php?svs='.$vid.'&act=backup2', $post);
		
		$out['backups_list'] = $res['backups_list'];
		$out['backup_limit'] = $res['backup_limit'];
		$out['restore_limit'] = $res['restore_limit'];
		$out['backup_used'] = $res['backup_used'];
		$out['restore_used'] = $res['restore_used'];
		$out['service_period'] = $res['service_period'];

		//DID it finish?
		if (!empty($res['done'])){
			$out['done'] = $res['done'];
			return $out;
		}else{
			return $res['error'];
		}
	}
	
	function list_backup($vid){
		
		//MAKE the Request
		$res = $this->call('index.php?svs='.$vid.'&act=backup2', $post);
		
		$out['backups_list'] = $res['backups_list'];
		$out['backup_limit'] = $res['backup_limit'];
		$out['restore_limit'] = $res['restore_limit'];
		$out['backup_used'] = $res['backup_used'];
		$out['restore_used'] = $res['restore_used'];
		$out['service_period'] = $res['service_period'];
		
		return $out;
	}
	
	function restore_backup($post, $vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=backup2', $post);
		
		return $res;
	}
	
	function delete_backup($post,$vid){

		$res =$this->call('index.php?svs='.$vid.'&act=backup2', $post);
		
		return $res;
	}
	
	/**
	 * RESCUE a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */	
	 
	 function rescue($vid, $pass){
		 
		 $post = array('password' => $pass,
					  'conf_password' => $pass,
					  'enablerescue' => 'Enable Rescue'
					);
		 
		 //MAKE the Request
		 $res = $this->call('index.php?svs='.$vid.'&act=rescue', $post);
		 
		 //DID it finish?
		 if(!empty($res['done'])){
			 return true;
		 }else{
			 return false;
		 }
	 }
	
	/**
	 * Disable rescue mode for a VPS
	 *
	 * @author       Chirag Nagda
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */	
	 
	 function disable_rescue($vid){
		 
		 $post = array('disablerescue' => 1);
		 
		 //MAKE the Request
		 $res = $this->call('index.php?svs='.$vid.'&act=rescue', $post);
		 
		 //DID it finish?
		 if(!empty($res['done'])){
			 return true;
		 }else{
			 return false;
		 }
	 }
	 
	/**
	 * Monitors a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */	
	 
	 function monitor($vid = 0, $post = array()){
		 
		 //MAKE the Request
		if(!empty($vid)){
			 $res = $this->call('index.php?act=monitor&svs='.$vid, $post);
		}else{
			$res = $this->call('index.php?act=monitor');
		}
		 
		 //Did it finish ?
		$ret['cpu'] = $res['cpu'];
		$ret['disk'] = $res['disk'];
		$ret['ram'] = $res['ram'];
		
		return $ret;
	 }
	
	/**
	 * START a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	function start($vid){
		
		// Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=start&do=1');
		
		// Did it finish ?
		if(!empty($res['done'])){
			return true;
		}else{
			return false;	
		}		
	}
		
	
	/**
	 * STOP a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	function stop($vid){
		
		// Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=stop&do=1');
		
		// Did it finish ?
		if(!empty($res['done'])){
			return true;
		}else{
			return false;	
		}
		
	}
		
	
	/**
	 * RESTART a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	function restart($vid){
		
		// Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=restart&do=1');
		
		// Did it finish ?
		if(!empty($res['done'])){
			return true;
		}else{
			return false;	
		}
		
	}
	
		
	
	/**
	 * POWER OFF a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       bool TRUE on success or FALSE on failure
	 */
	function poweroff($vid){
		
		// Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=poweroff&do=1');
		
		// Did it finish ?
		if(!empty($res['done'])){
			return true;
		}else{
			return false;	
		}
		
	}		
	
	/**
	 * STOP a Virtual Server
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       int 1 if the VM is ON, 0 if its OFF
	 */
	function status($vid){
		
		// Make the Request
		$res = $this->call('index.php?svs='.$vid.'&act=start');
		
		return $res['status'];
		
	}
	
	/**
	 * GET or SET the hostname of a VM. To get the current hostname dont pass the $newhostname parameter
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        string $newhostname The new HOSTNAME of the virtual server.
	 * @return       string The CURRENT hostname is returned if $newhostname is NULL. 
	 *						FALSE is returned if there was an error while setting the new hostname
	 *						'onboot' is returned if the new hostname will be set when the VPS is STOPPED and STARTED
	 *						'done' is returned if the new hostname has been set right now - Mainly OpenVZ
	 */
	function hostname($vid, $newhostname = NULL){
		
		// Are we to change ?
		if(!empty($newhostname)){
			
			$post = array('newhost' => $newhostname,
							'changehost' => 'Change Hostname');
				
			$resp = $this->call('index.php?svs='.$vid.'&act=hostname', $post);
			
			// Was there an error
			if(!empty($resp['error'])){
				
				$this->error = $resp['error'];
				return false;
			
			// Will it be done when the VPS is STOPPED and STARTED ?
			}elseif(!empty($resp['onboot'])){
				
				return 'onboot';
			
			// It was done successfully
			}elseif(!empty($resp['done'])){
				
				return 'done';
				
			}
		
		// Just return the CURRENT HOSTNAME
		}else{
			$resp = $this->call('index.php?svs='.$vid.'&act=hostname');
			return $resp['current'];
		}
			
	}
	
	/**
	 * GET the available IPs of a VM.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array Returns an array of Available IPs. 
	 * 		
	 */	
	function ips($vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=ips');
		
		return $res['ips'];
	}
	
	// Just return the system alerts
	
	function system_alerts($vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=system_alerts');

		return $res;
	}
		
	
	/**
	 * GET the CPU details of a VM.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing the details is returned. Usage details is available only in case of OpenVZ.
	 */
	function cpu($vid){
		
		$resp = $this->call('index.php?svs='.$vid.'&act=cpu');
		
		return $resp['cpu'];
		
	}
	
	/**
	 * GET the RAM details of a VM. Incase of Xen / KVM, only information is available as usage cannot be sensed.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing the details is returned. Usage details is available only in case of OpenVZ.
	 */
	function ram($vid){
		
		$resp = $this->call('index.php?svs='.$vid.'&act=ram');
		
		return $resp['ram'];
		
	}
	
	
	/**
	 * GET the Disk details of a VM. Incase of Xen / KVM, only information is available as usage cannot be sensed.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing the details is returned. Usage details is available only in case of OpenVZ.
	 */
	function disk($vid){
		
		$resp = $this->call('index.php?svs='.$vid.'&act=disk');
		
		$ret['disk'] = $resp['disk'];
		$ret['inodes'] = $resp['inodes'];
		
		return $ret;
		
	}
	
	/**
	 * GET the Bandwidth Usage of a VM.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        int $month The month in the format YYYYMM e.g. 201205 is for the Month of May, 2012
	 * @return       array Returns an array of Bandwidth Information for the Month GIVEN. 
	 * 						By Default the CURRENT MONTH details are returned
	 */	
	function bandwidth($vid, $month = 0){
		
		$resp = $this->call('index.php?svs='.$vid.'&act=bandwidth'.(!empty($month) ? '&show='.$month : ''));
		
		return $resp['bandwidth'];
		
	}
	
	/**
	 * List the User Logs
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing all the logs is returned
	 */	
	function logs($vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=logs');
		
		return $res['logs'];
	}

	/**
	 * List the Server Status Logs
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing all the status logs is returned
	 */
	 function statuslogs($vid){
		 
		 $res = $this->call('index.php?svs='.$vid.'&act=statuslogs');
		 
		 return $res;
	 }
	 
	 function self_shutdown($vid, $post){
		 
		 $res = $this->call('index.php?svs='.$vid.'&act=self_shutdown', $post);
		 
		 return $res;
	 }
	 
	 function apikey(){
		 
		 $res = $this->call('index.php?act=apikey');
		 
		 return $res;
		 
	 }

	 function addapikey(){
		
		$res = $this->call('index.php?act=apikey&do=add');
		 
		return $res;
	 }

	 function delapikey($id){

		$res = $this->call('index.php?act=apikey&del='.$id);
		 
		return $res;
	 }
	 
	 function adduser($post=array()){		 

		$res = $this->call('index.php?act=adduser', $post);
		
		return $res;
	 }
	 
	 function edituser($uid, $post=array()){
		 
		 $res = $this->call('index.php?act=edituser&uid='.$uid, $post);
		 
		 return $res;
	 }
	 
	/**
	 * List the all the USERS
	 *
	 * @author       Pulkit Gupta
	 * @return       array An array containing all the user information is returned
	 */	 
	 
	 function users($uid, $post = array()){
		 
		 $res = $this->call('index.php?act=users&suid='.$uid, $post);
		 
		 return $res;
	 }	 
	
	function delete_users($post){
		$res = $this->call('index.php?act=users&delete='.$post);
		return $res;
	}
	/**
	 * List the Recipes
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing all the recipes is returned
	 */	
	function listrecipes($vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=listrecipes');
		
		return $res['recipes'];
	}
	
	function execrecipe($vid, $rid){
		
		if(!empty($rid)){
			$post = array('rid' => $rid,
					 'exec_recipe' => 'Execute Recipe');
		}
		
		$res = $this->call('index.php?svs='.$vid.'&act=listrecipes', $post);
		
		return $res;
	}
	
	/**
	 * List the Processes in a VPS - Only OpenVZ
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
					$post will be used to send the list of processes to be killed. If empty then processes will be listed
	 * @return       array An array containing all the processes is returned
	 */
	function processes($post=array(),$vid){
		
		if(empty($post)){
			$resp = $this->call('index.php?svs='.$vid.'&act=processes');
		}else{
			$resp = $this->call('index.php?svs='.$vid.'&act=processes', $post);
		}
		
		return $resp;
		
	}
	
	function cloudres(){
		
		$res = $this->call('index.php?act=cloudres');
		
		return $res;
	}
	
	function profile($post=array()){
				
		$res = $this->call('index.php?act=profile', $post);
		
		return $res;
	}
	
	
	/**
	 * List the Services in a VPS - Only OpenVZ
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing all the services is returned
	 */
	function services($vid, $post = array()){
		
		if(empty($post)){
			$resp = $this->call('index.php?svs='.$vid.'&act=services');
		
			$ret['services'] = $resp['services'];
			$ret['autostart'] = $resp['autostart'];
			$ret['running'] = $resp['running'];
		}else{
			$resp = $this->call('index.php?svs='.$vid.'&act=services', $post);
			
			$ret['services'] = $resp['services'];
			$ret['autostart'] = $resp['autostart'];
			$ret['running'] = $resp['running'];
		}
		
		return $ret;
		
	}
	
	function hvmsettings($vid, $post = array()){
		
		$ret = $this->call('index.php?svs='.$vid.'&act=hvmsettings', $post);
		
		$res['nictypes'] = $ret['nictypes'];
		$res['vnckeymaps'] = $ret['vnckeymaps'];
		$res['boot'] = $ret['boot'];
		$res['vps'] = $ret['vps'];
		$res['done'] = $ret['done'];
		
		return $res;
	}
	
	function managesubnets($vid){
		
		$res = $this->call('index.php?svs='.$vid.'&act=managesubnets');
		
		return $res;
		
	}
	
	/*function managezone($did){
		
		$res = $this->call('index.php?#act=managezone&domainid='.$did);
		print_r($res);
		return $res;
	}*/
	
	function pdns($post=array(), $id=0){
		
		if(!empty($post)){
			//print_r($post);
			$res = $this->call('index.php?act=pdns',$post);
		}else if(!empty($id)){
			$res = $this->call('index.php?act=pdns&del='.$id);
		}else{
			$res = $this->call('index.php?act=pdns');
		}
		
		return $res;
	}
	
	function managezone($did, $post=array()){
		
		$path = '';
		
		if(!empty($did)){
			$path = '&domainid='.$did;
		}
		
		if(!empty($post['delete'])){
			$path .= '&delete='.$post['delete'];
		}
		
		$res = $this->call('index.php?act=managezone'.$path, $post);			
		
		return $res;
		
	}
	
	function rdns($post=array()){
		
		$path = '';
		
		if(!empty($post['delete'])){
			$path = '&delete='.$post['delete'];
		}
		
		$res = $this->call('index.php?act=rdns&page='.$post['page'].'&reslen='.$post['reslen'].$path, $post);
		
		return $res;
	}
		
	
	/**
	 * Changes the root password of a VPS
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        string $pass The new password to set
	 * @return       string FALSE is returned if there was an error while setting the new password
	 *						'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
	 *						'done' is returned if the new password has been set right now - Mainly OpenVZ
	 */
	function changepassword($vid, $pass) {	
		
		$post = array('newpass' => $pass,
					'conf' => $pass,
					'changepass' => 'Change Password'
					);
				
		$resp = $this->call('index.php?svs='.$vid.'&act=changepassword', $post);
		
		// Was there an error
		if(!empty($resp['error'])){
			
			$this->error = $resp['error'];
			return false;
		
		// Will it be done when the VPS is STOPPED and STARTED ?
		}elseif(!empty($resp['onboot'])){
			
			return 'onboot';
		
		// It was done successfully
		}elseif(!empty($resp['done'])){
			
			return 'done';
			
		}
		
	}
	
	function userpassword($post){
		
		$res = $this->call('index.php?act=userpassword', $post);
		
		return $res;
	}
	
	function usersettings($post=array()){
		
		$res = $this->call('index.php?act=usersettings', $post);
		
		return $res;
	}
	
	function tasks($post){
		$res = $this->call('index.php?act=tasks&svs='.$post['svs'].'&page='.$post['page'].'&reslen='.$post['reslen']);
		
		return $res;
	}
	
	/**
	 * Get the VNC Details like PORT, IP, VNC Password. Only available in case of Xen and KVM VPS if VNC is enabled.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @return       array An array containing all the VNC Details
	 */
	function vnc($vid){
		
		$resp = $this->call('index.php?svs='.$vid.'&act=vnc&novnc='.$vid);
		
		return $resp['info'];
		
	}
	
	/**
	 * Change the VNC Password. Only available in case of Xen and KVM VPS if VNC is enabled.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        string $pass The new password to set
	 * @return       string FALSE is returned if there was an error while setting the new password
	 *						'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
	 */
	function vncpass($vid, $pass) {
		
		$post = array('newpass' => $pass,
					'conf' => $pass,
					'vncpass' => 'Change Password'
					);
				
		$resp = $this->call('index.php?svs='.$vid.'&act=vncpass', $post);
		
		// Was there an error
		if(!empty($resp['error'])){
			
			$this->error = $resp['error'];
			return false;
		
		// Will it be done when the VPS is STOPPED and STARTED ?
		}elseif(!empty($resp['onboot']) || !empty($resp['done'])){
			
			return 'onboot';
			
		}
		
	}
	
	/**
	 * Re-installs a VPS if the $newosid is specified. If the $newosid is not passed, 
	 * then this function will return an array of available templates.
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        int $newosid The Operating System ID (you got from the list) that will be installed on the VPS.
	 * @param        string $newpass The new root password to set
	 * @return       string FALSE is returned if there was an error while setting the new password
	 *				 string 'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
	 *				 string 'done' is returned if the new password has been set right now - Mainly OpenVZ
	 *				 array An array of the list of avvailable OS Templates is returned if $newosid is NULL
	 */
	function ostemplate($vid, $newosid = NULL, $newpass = NULL){
		
		// Get the list of OS Templates
		$resp = $this->call('index.php?svs='.$vid.'&act=ostemplate');
		
		// Get a list of Virtual Servers
		$listvs = $this->listvs();
		
		// Is there such a VPS ?
		if(!empty($listvs[$vid])){
			
			$resp = $resp['oslist'][$listvs[$vid]['virt']];
		
		// No such VPS. Return an EMPTY ARRAY
		}else{
			
			return array();	
			
		}
		
		if(!empty($newosid)){
		
			// The POST Vars
			$post = array('newos' => $newosid,
						'newpass' => $newpass,
						'conf' => $newpass,
						'reinsos' => 'Reinstall');
					
			$resp = $this->call('index.php?svs='.$vid.'&act=ostemplate', $post);
			
			// Was there an error
			if(!empty($resp['error'])){
				
				$this->error = $resp['error'];
				return $resp['error'];
			
			// Will it be done when the VPS is STOPPED and STARTED ?
			}elseif(!empty($resp['onboot'])){
				
				return 'onboot';
			
			// It was done successfully
			}elseif(!empty($resp['done'])){
				
				return 'done';
				
			}
		
		// Just return the OS List
		}else{
			return $resp;	
		}
		
	}
	
	function os($post, $vid){
		
		$res = $this->call('index.php?act=ostemplate&svs='.$vid, $post);
		return $res;
	}
	
	/**
	 * Install a Control Panel
	 *
	 * @author       Pulkit Gupta
	 * @param        int $vid The VMs ID
	 * @param        string $panel The Name of the Panel you want to install. Options - cpanel, plesk, webuzo, kloxo, webmin, vestacp
	 * @return       string FALSE is returned if there was an error while installing the control panel
	 *						'onboot' is returned if the control panel will be installed when the VPS is STOPPED and STARTED
	 *						'done' is returned if the control panel has been installed right now - Mainly OpenVZ
	 */
	function controlpanel($vid, $panel){
		
		$post['ins'][$panel] = 1;
			
		$resp = $this->call('index.php?svs='.$vid.'&act=controlpanel', $post);
		
		// Was there an error
		if(!empty($resp['error'])){
			
			$this->error = $resp['error'];
			return false;
		
		// Will it be done when the VPS is STOPPED and STARTED ?
		}elseif(!empty($resp['onboot'])){
			
			return 'onboot';
		
		// It was done successfully
		}elseif(!empty($resp['done'])){
			
			return 'done';
			
		}
		
	}	
	
	/**
	 * Add Enduser ISO
	 *
	 * @author       Shreedhar Tiadi
	 * @param        int $vid The VMs ID
	 * @param        string $isourl The url of the iso from where the iso to be downloaded
	 * @param        string $isoname Name of the iso file
	 * @return       string Error is returned if unsuccessful
	 *					'done' is returned if iso is added.
	 */
	
	function addiso($vid, $isourl, $isoname){
		$post = array('filename' => $isoname,
					'iso_url' => $isourl,
					'addiso' => '1'
					);

		$resp = $this->call('index.php?svs='.$vid.'&act=addiso', $post);
		
		// Was there an error
		if(!empty($resp['error'])){
			$this->error = $resp['error'];
			return $this->error;
		// It was done successfully
		}elseif(!empty($resp['done'])){
			return 'done';	
		}
	}
	
	/**
	 * List Enduser ISO
	 *
	 * @author       Shreedhar Tiadi
	 * @param        int $vid The VMs ID	 
	 * @return       Array is returned if successful
	 *					'done' is returned if iso is added.

	 */
	
	function listiso($vid = 0){
		$resp = $this->call('index.php?svs='.$vid.'&act=euiso');
		if(!empty($resp['isos'])){
			return $resp['isos'];
		}else{
			return 'No ISOs Found';
		}	
	}
	
	/**
	 * Delete Enduser ISO
	 *
	 * @author       Shreedhar Tiadi
	 * @param        int $vid The VMs ID
	 * @param        string $isouuid UUID of the Enduser ISOs.(comma-separated if multiple)	 
	 * @return       string Error is returned if unsuccessful
	 *					'done' is returned if iso is deleted.
	 */
	 
	function deliso($vid, $isouuid){
		$post = array('del' => $isouuid);
		$resp = $this->call('index.php?svs='.$vid.'&act=euiso', $post);
		// Was there an error
		if(!empty($resp['error'])){
			$this->error = $resp['error'];
			return $this->error;
		// It was done successfully
		}elseif(!empty($resp['done'])){
			return 'done';
		}
	}
	
	/**
	 * Single Sign On
	 *
	 * @author       Chirag
	 * @param        int $vid The VMs ID	 
	 * @return       String login url
	 */	
	function sso($vid){
		$resp = $this->call('index.php?svs='.$vid.'&act=sso');
		$url = 'https://'.$this->ip.':'.$this->port.'/'.$resp['token_key'].'/?as='.$resp['sid'].'&svs='.$vid;
		
		if(!empty($resp['token_key']) && !empty($resp['sid'])){
			return $url;
		}else{
			return false;
		}	
	}

	/** 
	 * VPS Domain Forwarding 
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param int $vid: The VPS ID 
	 * @param array $post : Array of VDF record id to be deleted
	 * @return array of domain forwarding records
	*/
	function vdf($post = array()){

		$resp = $this->call('index.php?act=managevdf&svs='.$post['svs'],$post);

		return $resp;
	}

	function generate_keys(){
		$res = $this->call('index.php?act=create&generate_keys=1');
		return $res['new_keys'];
	}

	/** 
	 * Create SSH Key
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param array $post : Array of name(SSH_KEY) and value(SSH KEY VALUE) 
	 * @return array containing the message  "The SSH key has been added successfully"
	*/

	function addsshkey($post = array()){
		$post['add'] = 1;
		$resp = $this->call('index.php?act=addsshkey', $post);

		return $resp;
	}

	/** 
	 * Edit SSH Key
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param int $keyid
	 * @param array $post : Array of name(SSH_KEY) and value(SSH KEY VALUE) 
	 * @return array containing the message "The SSH key has been saved successfully"
	*/

	function editsshkey($keyid, $post = array()){
		$post['edit'] = 1;
		$resp = $this->call('index.php?act=editsshkey&keyid='.$keyid, $post);

		return $resp;
	}

	/** 
	 * SSH Key
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param array $post : Array of ssh key id to be deleted (Optional)
	 * @return array 
	*/

	function sshkeys($post = array()){

		if(empty($post)){
			$resps = $this->call('index.php?act=sshkeys');
			$resp['ssh_keys'] = $resps['ssh_keys'];
		}else{
			$resp = $this->call('index.php?act=sshkeys', $post);
		}
		return $resp;
	}

	/** 
	 * Apply SSH Key
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param int $vid	The VPS id
	 * @param array $post : Array of ssh key id to be Applied
	 * @return array 
	*/

	function applysshkeys($vid, $post = array()){
		$post['addkeyvps'] = 1;
		$resp = $this->call('index.php?act=sshkeys&svs='.$vid, $post);
	
		return $resp;
	}

	function webuzo_scripts($vid){
		$path = 'index.php?act=webuzo&svs='.$vid;		
		$ret = $this->call($path);	
		return $ret;
	}

	/** 
	 * Install Webuzo Script
	 * 
	 * @author Ali <ali@virtualizor.com>
	 * @param int $vid	The VPS id
	 * @param array $post 
	 * @return array 
	*/

	function installwebuzo($vid, $post = array()){

		$resp = $this->call('index.php?act=webuzo&svs='.$vid, $post);
	
		return $resp;
	}

	function listvolumes($search = array()){

		$path = 'index.php?act=volume';
		if(!empty($search)){
				$path .= '&search=1';

				foreach($search as $k => $v){
						$path.='&'.$k.'='.$v;
				}
		}

		$result = $this->call($path);
		return $result['storage_disk'];

	}

	function delete_volumes($post){

		$resp = $this->call('index.php?act=volume', $post);

		if(!empty($resp['error'])){
			$return = $resp['error'];
		}

		if(!empty($resp['done'])){
			$return = $resp['done'];
		}

		return $return;
		
	}

	function add_volume($post){

		$resp = $this->call('index.php?act=volume&addvolume=1', $post);

		if(!empty($resp['error'])){
			$return = $resp['error'];
		}

		if(!empty($resp['done'])){
			$return = $resp['done'];
		}

		return $return;
	}

	function perform_action_volume($post){

		$resp = $this->call('index.php?act=volume&listvs=1', $post);

		if(!empty($resp['error'])){
				$return = $resp['error'];
		}

		if(!empty($resp['done'])){
				$return = $resp['done'];
		}
		
		return $return;
	}

}

//////////////
// Examples
//////////////

//$v = new Virtualizor_Enduser_API('127.0.0.1', '16_BIT_API_KEY', '32_BIT_API_PASS');

// Get the list of the VPS
//$v->r($v->listvs());

// Start a VPS
//echo $v->start(3);

// Stop a VPS
//echo $v->stop(3);

// Restart a VPS
//echo $v->restart(3);

// Poweroff a VPS
//echo $v->poweroff(3);

// Get the Status of a VPS
//echo $v->status(3);

// Get the Hostname
//echo $v->hostname(4);

// Change the Hostname
//$v->r($v->hostname(4, 'NEWHOSTNAME'));

// CPU Details
//$v->r($v->cpu(4));

// Ram Details
//$v->r($v->ram(4));

// Disk Details
//$v->r($v->disk(4));

// Bandwidth Details for the Current Month
//$v->r($v->bandwidth(4));

// Bandwidth Details for the Month of May in 2012
//$v->r($v->bandwidth(4, 201205));

// List the processes - OpenVZ only
//$v->r($v->processes(4));

// List the services - OpenVZ only
//$v->r($v->services(4));

// Change the Root Password of a Virtual Server ?
//$v->r($v->changepassword(4, 'test'));

// Give the VNC Details - VNC must be enabled - Xen / KVM
//$v->r($v->vnc(4));

// Change the VNC Password - VNC must be enabled - Xen / KVM
//$v->r($v->vncpass(4, 'NEWpass'));

// List available OS Templates
//$v->r($v->ostemplate(2));

// Reinstall the OS
//$v->r($v->ostemplate(4, 1, 'test'));

// Install a Control Panel
//$v->r($v->controlpanel(4, 'cpanel'));

// Add Enduser ISO
// $v->r($v->addiso('1', 'http.kali.org/dists/kali-rolling/main/installer-amd64/current/images/netboot/mini.iso', 'mini.iso'));

// List Enduser ISO
// $v->r($v->listiso('1'));

// Delete Enduser ISOs
// $v->r($v->deliso(1,'dmqoknjv4lovwhif'));
?>