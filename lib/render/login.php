<?php

require_once "lib/session.php";
require_once "lib/render.php";
require_once "wind.php";

function login_render($errors=null, $input=null, $needed=null)
{
    if (getAction()==='action_login') {
        wind_require_login();
	return trust_render(getRequestInfo());
    } else {
	return redirect_render(buildURL('login'));
    }
}
