<?php

require_once "lib/session.php";
require_once "lib/render.php";

define('trust_form_pat',
       '<div class="form">
  <form method="post" action="%s">
  %s
    <input type="submit" name="trust" value="Confirm" />
    <input type="submit" value="Do not confirm" />
  </form>
  <p>
  <br />
  <br />
  <b>Disclaimer:</b> <small>This is for demonstration purposes only.  This page
  is still under development and will probably have clearer language, and
  a better disclaimer about how Columbia has nothing to do with the site, 
  and that this login is only supported while you have a working email
  account at Columbia.  After that, you may not be able to authenticate to outside
  sites with this login.  Prepare your accounts accordingly.  But, who knows,
  maybe there will be openid-forwarding just like email-forwarding.</small>
  </p>

</div>
');

define('normal_pat',
       '<p>You are logging in to <b style="color:#CC2244">%s</b>.</p>
       <p>Do you wish to confirm your identity ' .
       '(<code>%s</code>) with them?
       <br />
       This site has no relationship to Columbia University.
       </p>
');

define('id_select_pat',
       '<p>You are logging in to <b style="color:#CC2244">%s</b>
       <br />
       This site has no relationship to Columbia University.
       </p>
       <style type="text/css">
         .fullopenid {color:#999999;}
       </style>
       <h2>Choose an Identity</h2>
       <p>If you enter nothing, the request will be cancelled.
       </p>

       %s
');
define('user_dd','<dd>Sent with your <b>full name</b> and Columbia <b>email address</b>.</dd>');
define('anon_dd','<dd>An anonymous login that confirms your affiliation, but does not reveal your identity.
                      This identifier is universal across many sites, so, in theory, all sites that you
                      login with this identity could collate the information you provide to each site.
                  </dd>');
define('anon_site_dd','<dd>A site-specific <b>anonymous</b> login, so your login to different sites will
			   be different.  This makes it impossible for separate sites to collate data
			   you share with each of them based on your login.
		       </dd>');
define('radio_select_pat', '<p><dt>
			    <input id="%s" type="radio" name="idSelect" value="%s" />
			    <label for="%s"><b>%s</b> <span class="fullopenid">(OpenID: %s)</span></label>
			  </dt>
			  %s			  	     
			  </p>');

define('no_id_pat',
'
You did not send an identifier with the request,
and it was not an identifier selection request.
Please return to the relying party and try again.
');

function trust_render($info)
{
    $current_user = getLoggedInUser();
    $lnk = link_render(idURL($current_user));
    $trust_root = htmlspecialchars($info->trust_root);
    $trust_url = buildURL('trust', true);

    if ($info->idSelect()) {
        $selects = '';
        foreach (getValidUserIDs($current_user, $trust_root) as $selectable_username=>$details) {
	    $selects .= sprintf(radio_select_pat, 
				$selectable_username, 
				$selectable_username, 
				$selectable_username, 
				$selectable_username,
				idURL($selectable_username),//just the prefix
				constant($details['share'].'_dd')
				);
	}		
        $prompt = sprintf(id_select_pat, $trust_root, $selects);
    } else {
        $prompt = sprintf(normal_pat, $trust_root, $lnk);
    }

    $form = sprintf(trust_form_pat, $trust_url, $prompt);

    return page_render($form, $current_user, 'Do you trust this site?');
}

function noIdentifier_render()
{
    return page_render(no_id_pat, null, 'No Identifier Sent');
}

?>
