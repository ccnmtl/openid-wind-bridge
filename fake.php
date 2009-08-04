<?php

$path_extra = dirname(dirname(dirname(__FILE__)));
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

$try_include = @include 'config.php';

if (!$try_include) {
    header("Location: setup.php");
}

header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('X-Schuyler: hi');
if (function_exists('getOpenIDStore')) {
    require_once 'lib/session.php';
    require_once 'lib/actions.php';

    init();

    /*******FAKE ***********/
    $_SESSION['wind_user'] = 'XX-FAKEUNI-XX1';
    $_SESSION['wind_groups'] = array('FAKE_CLASS.st.FAKEFAKEFAKE');
    $_POST = array("openid_ns"=>"http://specs.openid.net/auth/2.0",
		   "openid_ns_sreg"=>"http://openid.net/extensions/sreg/1.1",
		   "openid_ns_pape"=>"http://specs.openid.net/extensions/pape/1.0",
		   "openid_sreg_required"=>"nickname,email",
		   "openid_sreg_optional"=>"timezone",
		   "openid_sreg_policy_url"=>"https://www.wikispaces.com/Privacy+Notice",
		   "openid_pape_preferred_auth_policies"=>"",
		   "openid_realm"=>"https://session.wikispaces.com",
		   "openid_mode"=>"checkid_setup",
		   "openid_return_to"=>"https://session.wikispaces.com/session/openidfinish?goto=&janrain_nonce=2009-08-04T22%3A05%3A38Zhx9YXp",
		   "openid_identity"=>"http://specs.openid.net/auth/2.0/identifier_select",
		   "openid_claimed_id"=>"http://specs.openid.net/auth/2.0/identifier_select",
		   "openid_assoc_handle"=>"{HMAC-SHA1}{4a71b088}{RYlrSw==}",
		   );

    $_SESSION['request'] = 'O:26:"Auth_OpenID_CheckIDRequest":11:{s:14:"verifyReturnTo";s:26:"Auth_OpenID_verifyReturnTo";s:4:"mode";s:13:"checkid_setup";s:9:"immediate";b:0;s:10:"trust_root";s:30:"https://session.wikispaces.com";s:9:"namespace";s:32:"http://specs.openid.net/auth/2.0";s:12:"assoc_handle";s:31:"{HMAC-SHA1}{4a71b088}{RYlrSw==}";s:8:"identity";s:50:"http://specs.openid.net/auth/2.0/identifier_select";s:10:"claimed_id";s:50:"http://specs.openid.net/auth/2.0/identifier_select";s:9:"return_to";s:102:"https://session.wikispaces.com/session/openidfinish?goto=&janrain_nonce=2009-08-04T22%3A05%3A38Zhx9YXp";s:6:"server";O:18:"Auth_OpenID_Server":6:{s:5:"store";O:21:"Auth_OpenID_FileStore":6:{s:9:"directory";s:45:"/hmt/sirius1/skv0/lamp_ccnmtl/lamp/openid/tmp";s:6:"active";b:1;s:9:"nonce_dir";s:52:"/hmt/sirius1/skv0/lamp_ccnmtl/lamp/openid/tmp/nonces";s:15:"association_dir";s:58:"/hmt/sirius1/skv0/lamp_ccnmtl/lamp/openid/tmp/associations";s:8:"temp_dir";s:50:"/hmt/sirius1/skv0/lamp_ccnmtl/lamp/openid/tmp/temp";s:13:"max_nonce_age";i:21600;}s:9:"signatory";O:21:"Auth_OpenID_Signatory":4:{s:15:"SECRET_LIFETIME";i:1209600;s:10:"normal_key";s:24:"http://localhost/|normal";s:8:"dumb_key";s:22:"http://localhost/|dumb";s:5:"store";R:12;}s:7:"encoder";O:26:"Auth_OpenID_SigningEncoder":2:{s:15:"responseFactory";s:23:"Auth_OpenID_WebResponse";s:9:"signatory";R:19;}s:7:"decoder";O:19:"Auth_OpenID_Decoder":2:{s:6:"server";r:11;s:8:"handlers";a:4:{s:13:"checkid_setup";s:26:"Auth_OpenID_CheckIDRequest";s:17:"checkid_immediate";s:26:"Auth_OpenID_CheckIDRequest";s:20:"check_authentication";s:28:"Auth_OpenID_CheckAuthRequest";s:9:"associate";s:28:"Auth_OpenID_AssociateRequest";}}s:11:"op_endpoint";s:40:"https://ccnmtl.lamp.columbia.edu/openid/";s:10:"negotiator";O:29:"Auth_OpenID_SessionNegotiator":1:{s:13:"allowed_types";a:4:{i:0;a:2:{i:0;s:9:"HMAC-SHA1";i:1;s:7:"DH-SHA1";}i:1;a:2:{i:0;s:11:"HMAC-SHA256";i:1;s:9:"DH-SHA256";}i:2;a:2:{i:0;s:9:"HMAC-SHA1";i:1;s:13:"no-encryption";}i:3;a:2:{i:0;s:11:"HMAC-SHA256";i:1;s:13:"no-encryption";}}}}s:7:"message";O:19:"Auth_OpenID_Message":4:{s:25:"allowed_openid_namespaces";a:3:{i:0;s:28:"http://openid.net/signon/1.0";i:1;s:28:"http://openid.net/signon/1.1";i:2;s:32:"http://specs.openid.net/auth/2.0";}s:4:"args";O:19:"Auth_OpenID_Mapping":2:{s:4:"keys";a:10:{i:0;a:2:{i:0;s:37:"http://openid.net/extensions/sreg/1.1";i:1;s:8:"required";}i:1;a:2:{i:0;s:37:"http://openid.net/extensions/sreg/1.1";i:1;s:8:"optional";}i:2;a:2:{i:0;s:37:"http://openid.net/extensions/sreg/1.1";i:1;s:10:"policy_url";}i:3;a:2:{i:0;s:43:"http://specs.openid.net/extensions/pape/1.0";i:1;s:23:"preferred_auth_policies";}i:4;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:5:"realm";}i:5;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:4:"mode";}i:6;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:9:"return_to";}i:7;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:8:"identity";}i:8;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:10:"claimed_id";}i:9;a:2:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:12:"assoc_handle";}}s:6:"values";a:10:{i:0;s:14:"nickname,email";i:1;s:8:"timezone";i:2;s:41:"https://www.wikispaces.com/Privacy+Notice";i:3;s:0:"";i:4;s:30:"https://session.wikispaces.com";i:5;s:13:"checkid_setup";i:6;s:102:"https://session.wikispaces.com/session/openidfinish?goto=&janrain_nonce=2009-08-04T22%3A05%3A38Zhx9YXp";i:7;s:50:"http://specs.openid.net/auth/2.0/identifier_select";i:8;s:50:"http://specs.openid.net/auth/2.0/identifier_select";i:9;s:31:"{HMAC-SHA1}{4a71b088}{RYlrSw==}";}}s:10:"namespaces";O:24:"Auth_OpenID_NamespaceMap":3:{s:18:"alias_to_namespace";O:19:"Auth_OpenID_Mapping":2:{s:4:"keys";a:3:{i:0;s:14:"Null namespace";i:1;s:4:"sreg";i:2;s:4:"pape";}s:6:"values";a:3:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:37:"http://openid.net/extensions/sreg/1.1";i:2;s:43:"http://specs.openid.net/extensions/pape/1.0";}}s:18:"namespace_to_alias";O:19:"Auth_OpenID_Mapping":2:{s:4:"keys";a:3:{i:0;s:32:"http://specs.openid.net/auth/2.0";i:1;s:37:"http://openid.net/extensions/sreg/1.1";i:2;s:43:"http://specs.openid.net/extensions/pape/1.0";}s:6:"values";a:3:{i:0;s:14:"Null namespace";i:1;s:4:"sreg";i:2;s:4:"pape";}}s:19:"implicit_namespaces";a:0:{}}s:14:"_openid_ns_uri";s:32:"http://specs.openid.net/auth/2.0";}}';
    /*******END FAKE********/

    $action = getAction();

    if (!function_exists($action)) {
        $action = 'action_default';
    }

    $resp = $action();

    writeResponse($resp);
} else {
?>
<html>
  <head>
    <title>PHP OpenID Server</title>
    <body>
      <h1>PHP OpenID Server</h1>
      <p>
        This server needs to be configured before it can be used. Edit
        <code>config.php</code> to reflect your server's setup, then
        load this page again.
      </p>
    </body>
  </head>
</html>
<?php
}
?>