<?php

require_once "lib/render.php";
require_once "lib/session.php";

require_once "lib/render/login.php";
require_once "lib/render/about.php";
require_once "lib/render/trust.php";

require_once "Auth/OpenID/Server.php";
require_once "Auth/OpenID/SReg.php";

function authCancel($info)
{
    if ($info) {
        setRequestInfo();
        $url = $info->getCancelURL();
	//once we finish, logout:ccnmtl policy
	setLoggedInUser(null);
    } else {
        $url = getServerURL();
    }
    
    return redirect_render($url);
}

function doAuth($info, $trusted=null, $fail_cancels=false,
                $idpSelect=null)
{
    if (!$info) {
        // There is no authentication information, so bail
        return authCancel(null);
    }

    if ($info->idSelect()) {
        if ($idpSelect) {
            $claimed_url = idURL($idpSelect);
        } else {
            $trusted = false;
        }
    } else {
        $claimed_url = $info->identity;
    }

    $user = getLoggedInUser();
    setRequestInfo($info);
    if ($trusted && allowedSite($info->trust_root)) {
        if (!verifyURLforUser($user, $claimed_url, $info->trust_root)) {
	    return login_render(array(), $claimed_url, $claimed_url);
        }
        setRequestInfo();
        $server =& getServer();
        $response =& $info->answer(true, null, $claimed_url);

        // Answer with some sample Simple Registration data.

        $sreg_data = getUserInfo($user, $claimed_url);

        // Add the simple registration response values to the OpenID
        // response message.
        $sreg_request = Auth_OpenID_SRegRequest::fromOpenIDRequest(
                                              $info);

        $sreg_response = Auth_OpenID_SRegResponse::extractResponse(
                                              $sreg_request, $sreg_data);

        $sreg_response->toMessage($response->fields);

        // Generate a response to send to the user agent.
        $webresponse =& $server->encodeResponse($response);

        $new_headers = array();

        foreach ($webresponse->headers as $k => $v) {
            $new_headers[] = $k.": ".$v;
        }
	//once we finish, logout:ccnmtl policy
	setLoggedInUser(null);
        return array($new_headers, $webresponse->body);
    } elseif ($fail_cancels) {
        return authCancel($info);
    } else {
        return trust_render($info);
    }
}

?>
