<?php

require_once "lib/session.php";
require_once "lib/render.php";

define('trust_form_pat',
       '<div class="form">
<!-- Logs user out from WIND -->
<iframe style="display:none;" height="1" width="1" src="https://wind.columbia.edu/logout"></iframe>

  <form name="identity" method="post" action="%s">
  %s
  <br />
  <small>
    <p>
    Students: By continuing, you agree to waive the confidentiality provisions of the Federal Family Educational and Privacy Rights Act of 1974 with respect to your work on <b>%s</b>, including your name and status as a student at Columbia University.
    </p><p>
Please check with the administrator of the site you are joining to determine its copyright restrictions and rights. 
    </p>
    </small>
    <input type="submit" name="trust" value="Login" />
    <input type="submit" value="Cancel" />
    <script type="text/javascript">
      var trust = document.forms["identity"].trust;
      var sel = document.forms["identity"].idSelect;
      if (sel) {
         trust.disabled = true;

      	 for (var i=0;i<sel.length;i++) {
             sel[i].onclick = function() {
                trust.disabled = false;
             };
             if (sel[i].checked) { trust.disabled = false; }
         }
      }
    </script>
  </form>
</div>
');

define('normal_pat',
       '<p>
       %s
       <br />
       To complete your login to <b style="color:#CC2244">%s</b></p>
       <p>Do you wish to confirm your identity 
       (<code>%s</code>) with them?
       <br />
       </p>
');

define('id_select_pat',
       '<p>
       %s
       <br />
       To complete your login to <b style="color:#CC2244">%s</b>...</p>
       </p>
       <style type="text/css">
         .fullopenid {color:#999999;}
       </style>
       <h2>Choose an Identity</h2>
       <p>Any information sent to this site may be made public.  Please specify the identity information that you wish to share.</p>
       %s
');
define('user_dd','<dd>Sent with your <b>full name</b> and Columbia <b>email address</b> as listed in the Columbia Directory.
       </dd>');

define('anon_dd','<dd>An anonymous login that confirms your affiliation, but does not reveal your identity.
                      This identifier is universal across many sites, so, in theory, all sites that you
                      login with this identity could collate the information you provide to each site.
                  </dd>');

define('anon_site_dd','<dd>This is a site-specific <b>anonymous</b> login.  
                       You may be asked to provide an email address to register with the site.
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
    $trust_name = $trust_root;

    $affiliation_info = '<b>Wikischolars</b> accepts Columbia University Library OpenID logins and is a known, trusted site.';

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
        $prompt = sprintf(id_select_pat, $affiliation_info, $trust_name, $selects);
    } else {
      $prompt = sprintf(normal_pat, $affiliation_info, $trust_name, $lnk);
    }

    $form = sprintf(trust_form_pat, $trust_url, $prompt, $trust_name);

    return page_render($form, $current_user, 
		       "CUL OpenID Authorization",
		       "Do you trust $trust_root?",//h1
		       true,true //login info
		       );
}

function noIdentifier_render()
{
    return page_render(no_id_pat, null, 'No Identifier Sent');
}

?>
