<?php
$authenticate_settings = get_authentication_settings(); $authenticate_error = NULL;
function /*bool*/ authenticate($username, $password, $method=TRUE){
	global $authenticate_error;
	//$auth_settings = array('methods' => array());
	global $authenticate_settings; // = get_authentication_settings();

	//check if already is authenticated
	if(isset($authenticate_settings['keys']) && is_array($authenticate_settings['keys']) && isset($authenticate_settings['hash']) && $authenticate_settings['hash'] == authenticate_key() && in_array(authenticate_key($username, $password, $method), $authenticate_settings['keys'])){ return TRUE; }

	//authenticate
	if($method === TRUE){ //try all methods until authentication succeeds
		$b = TRUE;
		foreach($authenticate_settings['methods'] as $m){
			if(isset($authenticate_settings['explicite']) && $authenticate_settings['explicite'] == TRUE){
				$b = ($b && authenticate($username, $password, $m));
			} else {
				if(authenticate($username, $password, $m)){ return TRUE; }
			}
		}
		if(isset($authenticate_settings['explicite']) && $authenticate_settings['explicite'] == TRUE){ return $b; }
	}
	else{
		if(in_array(strtolower($method), $authenticate_settings['methods'])){ //checks if $method is allowed
			switch(strtolower($method)){
				case 'anonymous': return TRUE; break;
				case 'http':
					$_SERVER['PHP_AUTH_USER'] = $username; $_SERVER['PHP_AUTH_PW'] = $password;
					//authenticate_key($username, $password, $method, TRUE);
					break;
				case 'pam':
					if(function_exists("pam_auth")){
						if( pam_auth($username, $password) ){
							authenticate_key($username, $password, $method, TRUE);
							return TRUE;
						}
					}
					break;
				case 'unix':
					
					break;
				default:
					return FALSE;
			}
		}
	}
	return FALSE; //if no return value is given, then return FALSE
}
function authenticate_key($username=TRUE, $password=NULL, $method=TRUE, $add=FALSE){
	global $authenticate_settings;
	if(($username === TRUE && $password===NULL && $method === TRUE ) && !is_array($add)){ return md5(implode("\n", $authenticate_settings['keys'])); }
	$str = md5($username.':'.$password.':'.strtolower($method));
	if($add === TRUE && $authenticate_settings['hash'] == authenticate_key() /* && authenticate($username, $password, $method) */){
		$authenticate_settings['keys'][] = $str;
		$authenticate_settings['hash'] = md5(implode("\n", $authenticate_settings['keys']));
	}
	if(is_array($add)){
		if($method === TRUE){ return md5(implode("\n", $add)); }
		else{ return in_array($str, $add); }
	}
	return $str;
}
function change_password($username, $password, $newpassword, $method=TRUE){
	return FALSE;
}
function signout(){
	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);
}
function get_authentication_settings(){
	$set = array();
	$set = json_decode(file_get_contents(dirname(__FILE__)."/authenticate.json"), TRUE);
	if(!isset($set['methods']) || !is_array($set['methods'])){ $set['methods'] = array(); } //http,pam,unix,mysql,mysql-table
	if(!isset($set['keys']) || !is_array($set['keys']) || !isset($set['hash'])){
		$set['keys'] = array();
		$set['hash'] = md5(implode("\n", $set['keys']));
	}
	//*debug*/ $set['HTTP authentication'] = TRUE;
	//*debug*/ $set['debug'] = TRUE;
	return $set;
}
function authentication_form($username, $password=NULL, $action=NULL){
	$str = NULL;
	$str .= '<table class="authentication-form"><form method="POST"'.($action!=NULL ? ' action="'.$action.'"' : NULL).'>'."\n\t";
	$str .= '<tr><td>Username:</td><td><input type="text" class="username" name="username" value="'.$username.'" /></td></tr>'."\n\t";
	$str .= '<tr><td>Password:</td><td><input type="password" class="password" name="password" value="'.$password.'" /></td></tr>'."\n\t";
	$str .= '<tr><td rowspan="2" class="submit right"><input type="submit" class="button" value="Authenticate" /><a href="'.$action.'?sign-out">Sign Out<a/></td></tr>'."\n";
	$str .= '</form></table>'."\n";
	return $str;
}

/*### ?sign-out ###*/
if(preg_match("#^sign-out$#i", $_SERVER['QUERY_STRING'])){
	signout();
	$target = preg_replace("#\?".$_SERVER['QUERY_STRING']."$#", "", $_SERVER['REQUEST_URI']);
	header("Location: ".$target);
	print '<a href="'.$target.'">You are now signed out.</a>';
	//exit;
}

/*### HTTP authentication ###*/
if(isset($authenticate_settings["HTTP authentication"]) && $authenticate_settings["HTTP authentication"] === TRUE){
	//*fix*/ if(isset($_SERVER['PHP_AUTH_USER'])){ $_POST['username'] = (isset($_POST['username']) ? $_POST['username'] : $_SERVER['PHP_AUTH_USER']); $_POST['password'] = (isset($_POST['password']) ? $_POST['password'] : $_SERVER['PHP_AUTH_PW']); }
	/*fix*/ if(isset($_POST['username']) && strlen($_POST['username']) > 1){ authenticate($_POST['username'], $_POST['password'], 'http'); }

        /*warning-fix*/ if(!isset($_POST) || !isset($_POST["username"])){ $_POST = array('username'=>NULL,'password'=>NULL); }
	if (!isset($_SERVER['PHP_AUTH_USER']) && !authenticate($_POST['username'], $_POST['password'])) {
		$realm = "Test Authentication System";
		header('HTTP/1.0 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="'.$realm.'"');
		//header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
		print authentication_form((isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : NULL), (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : NULL));
		//exit;
	} else {
		echo "<p>Hello <em>{$_SERVER['PHP_AUTH_USER']}<em>.</p>";
		echo "<p>You entered <em>{$_SERVER['PHP_AUTH_PW']}</em> as your password.</p>";
	}
	echo "<hr/>";
}

/*### debug ###*/
if(isset($authenticate_settings["debug"]) && $authenticate_settings["debug"] == TRUE){
	/*warning-fix*/ if(!isset($_POST) || !isset($_POST["username"])){ $_POST = array('username'=>NULL,'password'=>NULL); }
	print authentication_form($_POST["username"], $_POST["password"]);

	print '<pre>';
	if(isset($_POST)) print '$_POST = '; print_r($_POST);
	print '$_SERVER = Array'."\n(\n    ...\n"; foreach($_SERVER as $key=>$value){ if(preg_match("#^PHP_#", $key)){ print "    [".$key."] => ".print_r($value, TRUE)."\n"; } } print ")\n";
	print '$auth = '; print_r($authenticate_settings);
	authenticate_key($_POST["username"], $_POST["password"], 'anonymous', TRUE);
        print '$auth = '; print_r($authenticate_settings);
	print '</pre>';
}
?>
