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
</div>
');

define('normal_pat',
       '<p>Do you wish to confirm your identity ' .
       '(<code>%s</code>) with <code>%s</code>?</p>');

define('id_select_pat',
       '<p>You entered the server URL at the RP, %s.
       Please choose the name you wish to use.  If you enter nothing, the request will be cancelled.<br/>
       <!--input type="text" name="idSelect" /-->
       </p>
       <h2>Choose an Identity</h2>
       %s
');
define('user_dd','<dd>Sent with your full name and Columbia email address.</dd>');
define('anon_dd','<dd>An anonymous login that confirms your affiliation, but does not reveal your identity.
                      This identifier is universal across many sites, so, in theory, all sites that you
                      login to with this identity could share any information you provide to them with each other.
                  </dd>');
define('anon_site_dd','<dd>A site-specific anonymous login, so your login to different sites will
			   be different.  This makes it impossible for separate sites to aggregate data
			   you share with each of them.
		       </dd>');
define('radio_select_pat', '<p><dt>
			    <input id="%s" type="radio" name="idSelect" value="%s" />
			    <label for="%s">%s</label>
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
				constant($details['share'].'_dd')
				);
	}		
        $prompt = sprintf(id_select_pat, $trust_root, $selects);
    } else {
        $prompt = sprintf(normal_pat, $lnk, $trust_root);
    }

    $form = sprintf(trust_form_pat, $trust_url, $prompt);

    return page_render($form, $current_user, 'Trust This Site');
}

function noIdentifier_render()
{
    return page_render(no_id_pat, null, 'No Identifier Sent');
}

?>
