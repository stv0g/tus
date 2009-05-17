<?php

$site['path']['server'] = dirname(dirname(__FILE__));

switch ($_SERVER[ 'SERVER_NAME']) {
	case 'handball.griesm.de':
		$site['path']['web'] = '';
		break;
	case 'localhost':
		$site['path']['web'] = '/workspace/TuS Handball/trunk';
		break;
}

$site['hostname'] = $_SERVER[ 'SERVER_NAME'];
$site['url'] = 'http://' . $site['hostname'] . $site['path']['web'];

require_once $site['path']['server'] . '/include/locale.inc.php';
require_once $site['path']['server'] . '/include/config.inc.php';
require_once $site['path']['server'] . '/include/session.inc.php';
require_once $site['path']['server'] . '/include/functions.inc.php';
require_once $site['path']['server'] . '/include/db.inc.php';
require_once $site['path']['server'] . '/include/classes/sis.php';
require_once $site['path']['server'] . '/include/classes/mysql.php';
require_once $site['path']['server'] . '/include/classes/listing.php';

include $site['path']['server'] . '/include/head.inc.php';

?>