<?php
  // PHP code to perform WIND auth.  This code requires PHP 4.2 or
  // greater, compiled with OpenSSL 0.9.5 or greater and libcurl 7.03 or
  // greater.
  // debian packages: php5-curl

  // This version is compatible with WINDv1.  Some minor adjustments will
  // be required to work with WINDv2.

  // Sample code to include in your application
  // session_start();
  // include_once "wind.php";
  // 

  // NOTE: you MUST run session_start() on your own beforehand.

  function wind_config() {
    return array(
		 "wind_realm" => "cnmtl_full_np",
		 "authorize" => array("valid-user" => true,
				      "users" => array("sbd12"),
				      "groups"=> array(),
				      ),
		 "logout_arg" => "windlogout",
		 "wind_server" => "wind.columbia.edu",
		 "wind_login_uri" => "/login",
		 "wind_logout_uri" => "/logout",
		 "wind_validate_uri" => "/validate",
		 // Path to public cert used to sign $wind_server's cert
		 "verify_ssl_certificate" => false,
		 "ca_public_cert" => "/etc/httpd/htdocs/certs/6b8dc02d.0",
		 // These will usually be set correctly, but possibly not if you're
		 // using a weird port or some other non-default configuration.
		 );
  }
  //CUSTOMIZE 
  function wind_exceptions($conf) {
    return false; //no exceptions
  }
  
  function wind_current_location() {
    $s = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "s" : "";
    $destination = "http$s://" . $_SERVER["SERVER_NAME"] . ":"
      . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    if($_SERVER["QUERY_STRING"]) {
      $destination .= "?" . $_SERVER["QUERY_STRING"];
    }
    return $destination;
  }
         
  ///Called below! -- think of it as main()
  function wind_require_login() {
    $conf = wind_config();
    $ticketid = false;
    if(array_key_exists($conf["logout_arg"],$_GET)) {
      $_SESSION["wind_user"]=false;
      $_SESSION["wind_groups"]=array();
      unset($_GET[$conf["logout_arg"]]);
      wind_logout($conf);
    }
    if(array_key_exists("ticketid",$_GET)) {
      $ticketid = $_GET['ticketid'];
      unset($_GET['ticketid']);
    }
    if(wind_exceptions($conf) 
       || (array_key_exists("wind_user",$_SESSION) 
	   && $_SESSION["wind_user"])) {
      return "OK";
    }
    if($ticketid) {
      if(wind_verify_ticket($conf,$ticketid)) {
	return "OK";
      }
    }    
    wind_redirect($conf);
  }
  
  function wind_redirect($conf) {
    $r = "https://".$conf["wind_server"].$conf["wind_login_uri"]."?service=".$conf["wind_realm"]."&destination="
      . urlencode(wind_current_location());
    header("Location: $r\n");
    exit();
  }
  function wind_logout($conf) {
    $r = "https://".$conf["wind_server"].$conf["wind_logout_uri"];
    header("Location: $r\n");
    exit();
  }
  function wind_verify_ticket($conf, $ticket) {
    $validate_url = "https://" . $conf["wind_server"] . $conf["wind_validate_uri"]
      . "?ticketid=" . $ticket;
    
    $ch = curl_init("$validate_url");
    
    // Tell curl_exec to return the page instead of passing it to stdout
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($conf["verify_ssl_certificate"]) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_CAINFO, $ca_public_cert);
    }
    if($ch) {
      $answer = curl_exec($ch);
      curl_close($ch);
      $results = preg_split("/[\s]/", $answer, 3);
      if ($results[0] == "yes") {
	$_SESSION["wind_user"] = $results[1];
	$_SESSION["wind_groups"] = explode($results[2],"\n");
	if ($conf["authorize"]["valid-user"]
	    || array_search($_SESSION["wind_user"],$conf["authorize"]["users"],true)
	    || array_intersect($_SESSION["wind_groups"], $conf["authorize"]["groups"])
	    ) {
	  return true;
	}
      }
    } 
    return false;
  }

