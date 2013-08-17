<?php

session_start();

error_reporting(E_ALL);

set_error_handler(function() {
  throw new Exception('Failed to open authentification file in <code>'. FILE_PASS .'</code>');
});

require 'config.php';
require 'lib/ldap.php';

// logout
if (isset($_GET['logout'])) {
  unset($_SESSION['authentificated']);
  unset($_SESSION['user_information']);
  session_destroy();
}

// check identification
else if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
  try {
    //*
    $db = json_decode(file_get_contents(FILE_PASS));
    $ldapConfig = $db->{'ldap'};
    if ($ldapConfig) {
	$ldap = new Ldap($ldapConfig->{'ldap_host'}, $ldapConfig->{'ldap_port'});
	$ldap->setSearchBase($ldapConfig->{'ldap_searchBase'});
	$ldap->setUserFilter($ldapConfig->{'ldap_userFilter'});
	$ldap->setUserIdAttribute($ldapConfig->{'ldap_userIdAttribute'});
	$ldap->setBaseDn($ldapConfig->{'ldap_baseDn'});
	$ldap->connect();
	$user = $ldap->authenticate($_POST['username'], $_POST['password']);
	if ($user) {
		$_SESSION['authentificated'] = true;
		$_SESSION['user_information'] = $user;
	}
    } else {
	    $username = $db->{'user'};
	    $password = $db->{'password'};
	    /*/
	      $username = 'test';
	      $password = 'test';
	    //*/
	    if ($_POST['username'] == $username && $_POST['password'] == $password)
		    $_SESSION['authentificated'] = true;
	    else
		    $_SESSION['message'] = 'Incorrect username or password.';  
    }
  } catch(Exception $e) {
    $_SESSION['message'] = $e->getMessage();
  }
}

header('Location: '.INDEX);
exit();

?>
