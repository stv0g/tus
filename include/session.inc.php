<?php
// Session
$cookie_path = '/';
$session_name = 'sess_id';
$session_path = ini_get('session.save_path') . '/tus';
$cookie_timeout = 6000;
$garbage_timeout = 86400;

ini_set('session.gc_maxlifetime', $garbage_timeout);
ini_set('session.cookie_lifetime', $cookie_timeout);
ini_set('session.cookie_path', $cookie_path);
ini_set('session.name', $session_name);
ini_set('session.use_only_cookies', true);

if (!is_dir($session_path)) mkdir($session_path, 0777);
ini_set('session.save_path', $session_path);

session_start();

// refresh / set cookie
setcookie($session_name, session_id(), time() + $cookie_timeout, $cookie_path);

// Against session highjacking
$salt = 'kTp+8Q#g#T';
if(empty($_SESSION['ip']) || empty($_SESSION['ua'])) {
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['ua'] = md5($salt . $_SERVER['HTTP_USER_AGENT']);
}

if(substr($_SESSION['ip'], 0, strrpos($_SESSION['ip'], '.')) != substr($_SERVER['REMOTE_ADDR'], 0, strrpos($_SERVER['REMOTE_ADDR'], '.')) && !empty($_SESSION['usr_id'])) {
	trigger_error('Session cookie hijacked: IP subset changed', E_USER_ERROR);
	exit('Session cookie hijacked: IP subset changed! Killing myself...');
}

if ($_SESSION['ua'] != md5($salt . $_SERVER['HTTP_USER_AGENT']) && !empty($_SESSION['usr_id'])) {
	trigger_error('Session cookie hijacked: Useragent changed', E_USER_ERROR);
	exit('Session cookie hijacked: Useragent changed! Killing myself...');
}

//TODO Compability
$site['usr']['id'] = empty($_SESSION['usr_id']) ? null : $_SESSION['usr_id'];
$site['usr']['name'] = empty($_SESSION['usr_name']) ? null : $_SESSION['usr_name'];
$site['usr']['prename'] = empty($_SESSION['usr_prename']) ? '' : $_SESSION['usr_prename'];
$site['usr']['lastname'] = empty($_SESSION['usr_lastname']) ? '' : $_SESSION['usr_lastname'];
$site['usr']['mail'] = empty($_SESSION['usr_mail']) ? '' : $_SESSION['usr_mail'];
$site['redirect'] = empty($_SESSION['referrer']) ? $_SERVER['PHP_SELF'] : $_SESSION['referrer'];

$usr_id = $site['usr']['id'];
$usr_name = $site['usr']['name'];
$usr_prename = $site['usr']['prename'];
$usr_lastname = $site['usr']['lastname'];
$usr_mail = $site['usr']['mail'];
$redirect = $site['redirect'];


// Referrer
if (strpos($_SERVER['PHP_SELF'], 'index.php') !== false) {
	if (!is_array($_SESSION['referrer']))
		$_SESSION['referrer'] = array(0 => $_SERVER['REQUEST_URI']);
	else {
		array_unshift($_SESSION['referrer'], $_SERVER['REQUEST_URI']);
		array_splice($_SESSION['referrer'], 10);
	}
}
?>