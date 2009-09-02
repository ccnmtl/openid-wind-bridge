<?php

define('page_template',
'<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<title>%s</title>
	<style type="text/css">
		a {text-decoration: none;}
		a:hover {text-decoration: underline;}

		.searchform {display:none;}
	</style>
	%s
</head>
<body><center>
	<div style="text-align: left; height: 100px; max-width: 800px; min-width: 400px; margin-left: auto; margin-right: auto; font-family: Verdana, Arial, Helvetica, sans-serif;">
	
		<div style="color: #fff; font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; padding: 0.35em 0.95em; background: #002b7f; height: 17px; position: relative">
			<div style="float: left"><img src="http://www.columbia.edu/cu/lweb/img/assets/3817/crown.w18h14.white.gif" valign="top"/>
			</div>
			<div style="float: left; margin: 2 0 0 5">Columbia University Libraries OpenID</div>
			<div style="float: right;  margin: 2 0 0 5; text-align: right">
				<!--a href="http://directory.columbia.edu" style="color: #fff;">Directory</a> | <a href="http://www.columbia.edu/help/index.html" style="color: #fff;">Help</a -->
			</div>
		</div>
		<div>
			<div style="float: left;"><!--<img src="http://www.columbia.edu/cu/images/cu_logo.gif" style="margin: 10px 0px" border=0 />--></div>
			<div class="searchform" style="float: right; margin-top: 10px">
				<form method=get action="/cgi-bin/switchaction-2008.pl">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><img src="http://www.columbia.edu/images/spacer.gif" width="1" height="3" alt="" border="0"></td>
						</tr>
						<tr>
							<td valign="top"><input type="text" name="fullname" value="" id="searchbox" maxlength="2033"></td>
							<td width="5"><img src="http://www.columbia.edu/images/spacer.gif" width="1" height="1" alt="" border="0"></td>
							<td><input type="Image" value="search" src="http://www.columbia.edu/images/cu_btn_search.gif" width="48" height="17" alt="Search" title="Search" border="0"></td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td valign="top"><input name="type" type="radio" value="web" id="type" checked></td>
							<td><label for="web"><span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px">Web Sites</span></label></td>
							<td width="5"><img src="http://www.columbia.edu/images/spacer.gif" width="1" height="1" alt="" border="0"></td>
							<td valign="top"><input name="type" type="radio" id="type" value="people"></td>
							<td><label for="people"><span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px">People</span></label></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<div class="mainbody" style="clear: both; padding: 30px; font-family: Verdana; font-size: 12px; height: 500px">
		         <div class="statusmessage">%s</div>
			 <h1>%s</h1>
			 <div class="maincontent">
			   %s
			 </div>
		</div>
		<div style="color: #fff; font-size: 11px; height: 17px; padding: 0.35em 0.95em; background: #002b7f">
			<div style="margin-top: 2px; float: left"><!-- something here --> </div>
			<div style="margin-top: 2px; text-align: right">
			  <a href="mailto:ccnmtl+openid@columbia.edu?subject=OpenID question" 
			     style="color: #fff; font-family: Verdana, Arial, Helvetica, sans-serif;">ccnmtl+openid@columbia.edu</a>
			</div>
		</div>
		<br/>
		
		
	</div></center>
</body>
</html>');

define('logged_in_pat', 'You are logged in as %s');

/**
 * HTTP response line contstants
 */
define('http_bad_request', 'HTTP/1.1 400 Bad Request');
define('http_found', 'HTTP/1.1 302 Found');
define('http_ok', 'HTTP/1.1 200 OK');
define('http_internal_error', 'HTTP/1.1 500 Internal Error');

/**
 * HTTP header constants
 */
define('header_connection_close', 'Connection: close');
define('header_content_text', 'Content-Type: text/plain; charset=us-ascii');

define('redirect_message',
       'Please wait; you are being redirected to <%s>');


/**
 * Return a string containing an anchor tag containing the given URL
 *
 * The URL does not need to be quoted, but if text is passed in, then
 * it does.
 */
function link_render($url, $text=null) {
    $esc_url = htmlspecialchars($url, ENT_QUOTES);
    $text = ($text === null) ? $esc_url : $text;
    return sprintf('<a href="%s">%s</a>', $esc_url, $text);
}

/**
 * Return an HTTP redirect response
 */
function redirect_render($redir_url)
{
    $headers = array(http_found,
                     header_content_text,
                     header_connection_close,
                     'Location: ' . $redir_url,
                     );
    $body = sprintf(redirect_message, $redir_url);
    return array($headers, $body);
}

function navigation_render($msg, $items)
{
    $what = '';//link_render(buildURL(), 'PHP OpenID Server');
    if ($msg) {
        $what .= $msg;
    }
    if ($items) {
        $s = '<p>' . $what . '</p><ul class="bottom">';
        foreach ($items as $action => $text) {
            $url = buildURL($action);
            $s .= sprintf('<li>%s</li>', link_render($url, $text));
        }
        $s .= '</ul>';
    } else {
        $s = '<p class="bottom">' . $what . '</p>';
    }
    return sprintf('<div class="navigation">%s</div>', $s);
}

/**
 * Render an HTML page
 */
function page_render($body, $user, $title, $h1=null, $login=false, $nologin_nav=false)
{
    $h1 = $h1 ? $h1 : $title;

    if (!$nologin_nav && $user) {
        $msg = sprintf(logged_in_pat, link_render(idURL($user), $user)
		       /*,link_render(idURL($user))*/);
        $nav = array('logout' => 'Log Out');

        $navigation = navigation_render($msg, $nav);
    } else {
        if (!$login) {
            $msg = link_render(buildURL('login'), 'Log In');
            $navigation = navigation_render($msg, array());
        } else {
            $navigation = '';
        }
    }

    $style = getStyle();
    $text = sprintf(page_template, $title, $style, $navigation, $h1, $body);
    // No special headers here
    $headers = array();
    return array($headers, $text);
}

