<?php

require_once "config.php";
require_once "lib/render.php";
require_once "Auth/OpenID/Server.php";

/**
 * Set up the session
 */
function init()
{
    session_name('openid_server');
    session_start();
}

/**
 * Get the style markup
 */
function getStyle()
{
    $parent = rtrim(dirname(getServerURL()), '/');
    $url = htmlspecialchars($parent . '/openid-server.css', ENT_QUOTES);
    return sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $url);
}

/**
 * Get the URL of the current script
 */
function getServerURL()
{
	global $server_url;
	return $server_url;
    $path = $_SERVER['SCRIPT_NAME'];
    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'];
    $s = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '';
    if (($s && $port == "443") || (!$s && $port == "80")) {
        $p = '';
    } else {
        $p = ':' . $port;
    }

    return "http$s://$host$p$path";
}

/**
 * Build a URL to a server action
 */
function buildURL($action=null, $escaped=true)
{
    $url = getServerURL();
    if ($action) {
        $url .= $action;
    }
    return $escaped ? htmlspecialchars($url, ENT_QUOTES) : $url;
}

/**
 * Extract the current action from the request
 */
function getAction()
{
    $path_info = @$_SERVER['PATH_INFO'];
    $action = ($path_info) ? substr($path_info, 1) : '';
    $function_name = 'action_' . $action;
    return $function_name;
}

/**
 * Write the response to the request
 */
function writeResponse($resp)
{
    list ($headers, $body) = $resp;
    array_walk($headers, 'header');
    header(header_connection_close);
    print $body;
}

/**
 * Instantiate a new OpenID server object
 */
function getServer()
{
    static $server = null;
    if (!isset($server)) {
        $server =& new Auth_OpenID_Server(getOpenIDStore(),
                                          buildURL());
    }
    return $server;
}

/**
 * Get the openid_url out of the cookie
 *
 * @return mixed $openid_url The URL that was stored in the cookie or
 * false if there is none present or if the cookie is bad.
 */
function getLoggedInUser()
{
    return isset($_SESSION['wind_user'])
        ? $_SESSION['wind_user']
        : false;
}


function getValidUserIDs($user=null, $site=null) {
    if (!$user) $user = getLoggedInUser();
    $rv = array($user=>array('share'=>'user'));

    if ($site) $rv[friendlyAnonymousID($user,'cu',$site)] = array('share'=>'anon_site');
    /*
    if (in_array("tlc.cunix.local:columbia.edu",$_SESSION['wind_groups'])) {
	$rv[friendlyAnonymousID($user,'ccnmtl')] = array('share'=>'anon');
	if ($site) $rv[friendlyAnonymousID($user,'ccnmtl-site',$site)] = array('share'=>'anon_site');
    }
    if (preg_match("/\.st\./", implode($_SESSION['wind_groups']))) {
	$rv[friendlyAnonymousID($user,'columbiastudent')] = array('share'=>'anon');
	if ($site) $rv[friendlyAnonymousID($user,'columbiastudent-site',$site)] = array('share'=>'anon_site');
    }
    if (preg_match("/\.fc\./", implode($_SESSION['wind_groups']))) {
	$rv[friendlyAnonymousID($user,'columbiafaculty')] = array('share'=>'anon');
	if ($site) $rv[friendlyAnonymousID($user,'columbiafaculty-site',$site)] = array('share'=>'anon_site');
    }
    */
    return $rv;
}

$CONSONANTS = "bcdfghjklmnpqrstvwxyz"; //21
$VOWELS = "aeiou"; //5
function friendlyAnonymousID($user,$affil,$site='') {
    global $CONSONANTS, $VOWELS;
    //affil-cvcvcDD randomness: 21**3 * 5**2 * 100 = 23152500; log16 ~= 7
    $friendly = '';
    $hmac = hash_hmac("sha256","$affil-$user-$site",$ccnmtl_secret);
    $remainder = hexdec(substr($hmac,0,7)); //first 7 hex digits
    $place = 21*5*21*5*21*10*10;
    $friendly .= $CONSONANTS[$remainder/$place % 21]; $remainder %= $place; $place /= 21;
    $friendly .= $VOWELS[$remainder/$place % 5]; $remainder %= $place; $place /= 5;
    $friendly .= $CONSONANTS[$remainder/$place % 21]; $remainder %= $place; $place /= 21;
    $friendly .= $VOWELS[$remainder/$place % 5]; $remainder %= $place; $place /= 5;
    $friendly .= $CONSONANTS[$remainder/$place % 21]; $remainder %= $place; $place /= 21;
    $friendly .= $remainder/$place % 10; $remainder %= $place; $place /= 10;
    $friendly .= $remainder/$place % 10; $remainder %= $place; $place /= 10;
    return "$affil-$friendly";
}

/**
 * Set the openid_url in the cookie
 *
 * @param mixed $identity_url The URL to set. If set to null, the
 * value will be unset.
 */
function setLoggedInUser($identity_url=null)
{
    if (!isset($identity_url)) {
        unset($_SESSION['openid_url']);
        unset($_SESSION['wind_user']);
    } else {
        $_SESSION['openid_url'] = $identity_url;
    }
}

/**
 * Returns whether a URL is valid for the user
 *
 * @param mixed $user The value returned by getLoggedInUser()
 * @param mixed $claimed_identity_url The URL the user claims to own.
 */
function verifyURLforUser($user, $claimed_identity_url=null, $site=null)
{
    return in_array($claimed_identity_url, array_map("idURL", array_keys(getValidUserIDs($user,$site))));
}

/**
 * Returns registration data for the user/identity_url
 *
 * @param mixed $user The value returned by getLoggedInUser()
 * @param mixed $identity_url The URL the user is logging in with.
 */
function getUserInfo($user, $identity_url=null)
{
    if ($identity_url === idURL($user)) {
	$ldapoutput = null; $matches = null;
	$rv = array(
	       'nickname' => $user,
	       //'email' => $user ."@columbia.edu",
	       //'dob' => '1970-01-01','gender' => 'F','postcode' => '12345',
	       //'country' => 'ES','language' => 'eu',
	       'timezone' => 'America/New_York');

	///host:ldap.columbia.edu,time limit:1 second,format: minimal,auth:simple 
	exec("/usr/bin/ldapsearch -h ldap -l 1 -LLL -x uni=$user cn mail", $ldapoutput);
	if (count($output) > 1) {
	    if (preg_match('/^cn: (.*)$/', $output[1], $matches)) {
		$rv['nickname'] = $matches[1];
		$rv['fullname'] = $matches[1];
	    }
	    if (preg_match('/^mail: (.*)$/', $output[2], $matches)) {
	        $rv['email'] = $matches[1];
	    }
	}
	return $rv;
    } else {
	return array('nickname'=>idFromURL($identity_url));
    }
}

function getRequestInfo()
{
    return isset($_SESSION['request'])
        ? unserialize($_SESSION['request'])
        : false;
}

function setRequestInfo($info=null)
{
    if (!isset($info)) {
        unset($_SESSION['request']);
    } else {
        $_SESSION['request'] = serialize($info);
    }
}

function idURL($identity)
{
    return "http://openid.ccnmtl.columbia.edu/". $identity;
}

function idFromURL($url)
{
	$matches = null;
    if (preg_match('/([\w-]+\d+)$/', $url, $matches)) {
		return $matches[1];
    }
	return null;
}

function getServerConfig() {
  global $server_config, $current_server;
  if (!$current_server && $_SESSION['current_server']) {
    $current_server = $_SESSION['current_server'];
  }
  return $server_config[$current_server];
}


