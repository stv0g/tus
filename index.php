<?php
############################################
# TUS Griesheim Handball CMS  (5.5.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)  #
############################################
# index.php - The Main Page                #
############################################

$timer = microtime(true);

// error reporting
if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	@ini_set('display_errors', 'on');
	@ini_set('error_reporting', E_ERROR & E_WARNING & E_PARSE & E_NOTICE & E_STRICT);
}
else {
	@ini_set('display_errors', 'off');
	@ini_set('error_reporting', 0);
}

$site['path']['server'] = dirname(__FILE__);

include $site['path']['server'] . '/include/init.inc.php';

//TODO Compability
$site['command'] = empty($_GET['command']) ? null : $_GET['command'];
$site['module'] = empty($_GET['module']) ? 'article' : $_GET['module'];
$main_module = $site['module'];
$command = $site['command'];

$head['title'] = '<title>' . $config['site']['name'] . ' - {lang:general:' . $site['module'] . '}</title>';

if (isset($_GET['cat_id'])) {
	$result = mysql_query('SELECT season, name, type FROM categories WHERE id = ' . (int) $_GET['cat_id'], $site['db']['connection']);
	$line = mysql_fetch_array($result);
	
	$site['season'] = $line['season'];
	$site['cat']['id'] = (int) $_GET['cat_id'];
	$site['cat']['type'] =  $line['type'];
	$site['cat']['name'] =  stripslashes($line['name']);
	
	$season = $site['season'];
	$cat_id = $site['cat']['id'];
	$cat_name = $site['cat']['name'];
	$cat_type = $site['cat']['type'];
	
}
else {
	if (empty($_GET['season'])) {
		if (!empty($config['site']['season'])) {
			$site['season'] = $config['site']['season'];
		}
		else {
			$result = mysql_query('SELECT MAX(season) AS season FROM categories', $site['db']['connection']);
			$line = mysql_fetch_array($result);
			
			$site['season'] = (int) $line['season'];
		}

		$season = $site['season'];
	}
	else {
		$season = (int) $_GET['season'];
		$site['season'] = (int) $_GET['season'];
	}
	
	$result = mysql_query('SELECT MIN(id) AS cat_id, name AS cat_name, type AS cat_type FROM categories WHERE season = ' . $site['season'] . ' GROUP BY id', $site['db']['connection']);
	$line = mysql_fetch_array($result);
	
	$cat_id = $line['cat_id'];
	$cat_type = $line['cat_type'];
	$cat_name = $line['cat_name'];
	
	$site['cat']['id'] = $line['cat_id'];
	$site['cat']['type'] =  $line['cat_type'];
	$site['cat']['name'] = $line['cat_name'];
}

$site['id'] = (int) $_GET['id'];
$id = $site['id'];

if (file_exists($site['path']['server'] . '/include/template/' . $config['site']['template'] . '/index.tpl'))
	$template = file_get_contents($site['path']['server'] . '/include/template/' . $config['site']['template'] . '/index.tpl');
else
	return $lang['general']['error_file_not_found'] . $site['path']['server'] . '/include/template/' . $config['site']['template'] . '/index.tpl';

$template = str_replace('{content}', module($site['module']), $template);
$template = preg_replace_callback('={module:(.*?)}=si', 'module', $template);
$template = str_replace('{head}', html_headers(), $template);
$template = preg_replace_callback('={lang:(.*?):(.*?)}=si', 'lang', $template);
$template = preg_replace_callback('={icon:(.*?):(.*?)}=si', 'icon', $template);

$template = str_replace('{time}', round((microtime(true) - $timer), 4), $template);

echo $template;

mysql_close($site['db']['connection']);
?>