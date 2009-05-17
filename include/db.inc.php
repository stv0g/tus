<?php
// mySQL
//TODO Compability
$site['db']['connection'] = mysql_connect($config['mysql']['host'], $config['mysql']['user'], $config['mysql']['pw']);
$connection = $site['db']['connection'];
mysql_select_db($config['mysql']['db'], $site['db']['connection']);
mysql_set_charset('UTF8', $site['db']['connection']);
?>