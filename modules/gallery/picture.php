<?php

$site['path']['server'] = dirname(dirname(dirname(__FILE__)));
include $site['path']['server'] . '/include/init.inc.php';

$result = mysql_query('SELECT
						categories.season AS season,
						categories.name AS cat_name,
						categories.id AS cat_id,
						picture_categories.path AS gal_path,
						pictures.full AS full,
						pictures.thumb AS thumb
					FROM pictures
						LEFT JOIN picture_categories ON pictures.gal_id = picture_categories.id
						LEFT JOIN categories ON picture_categories.cat_id = categories.id
					' . ((empty($_GET['id'])) ? 'ORDER BY RAND()' : '
					WHERE
						pictures.id = ' . (int) $_GET['id']) . '
					LIMIT 1', $site['db']['connection']);

if (mysql_num_rows($result) > 0) {
	$line = mysql_fetch_array($result);
	$rights = access($site['usr']['id'], 'gallery', $line['cat_id']);

	if($rights['show'] == true) {
		$referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		$server_host = $_SERVER['HTTP_HOST'];

		while (substr_count($referer_host, '.') > 1)
		$referer_host = substr($referer_host, strpos($referer_host, '.') + 1);
			
		while (substr_count($server_host, '.') > 1)
		$server_host = substr($server_host, strpos($server_host, '.') + 1);
		
		if (empty($referer_host)) {
			header('Location: ' . $site['url'] . '/images/no_direct_link.png');
			//trigger_error('Missing image referer!', E_USER_WARNING);
		}
		elseif ($referer_host !== $server_host) {
			header('Location: ' . $site['url'] . '/images/no_direct_link.png');
			//trigger_error('Wrong image referer: ' . $_SERVER['HTTP_REFERER'], E_USER_WARNING);
		}
		else {
			$path = '/images/gallery/' . $line['season'] . '/' . str_replace(' ', '_', strtolower($line['cat_name'])) . '/' . $line['gal_path'] . '/';
			mysql_query('UPDATE pictures SET view_count = view_count + 1 WHERE id = '  . $site['id'], $site['db']['connection']);
			header('Location: ' . $site['url'] . $path . ((isset($_GET['thumb']) && $line['thumb'] != '') ? $line['thumb'] : $line['full']));
		}
	}
	else {
		no_rights();
		header('Location: ' . $site['url'] . '/images/no_rights.png');
	}
}
else {
	header('HTTP/1.0 404 Not Found');
	trigger_error('Invalid image id!', E_USER_WARNING);
}
?>