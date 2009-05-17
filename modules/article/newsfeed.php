<?php
header ('Content-Type: application/rss+xml; charset=UTF-8');

$site['path']['web'] = $_SERVER['SCRIPT_NAME'];
for ($i = 0; $i < 3; $i++) $site['path']['web'] = dirname($site['path']['web']);
$site['path']['web'] = (substr($site['path']['web'], -1, 1) == DIRECTORY_SEPARATOR) ? substr($site['path']['web'], 0, -1) : $site['path']['web'];
$site['path']['server'] = $_SERVER['DOCUMENT_ROOT'] . $site['path']['web'];

include $site['path']['server'] . '/include/init.inc.php';

if (empty($_GET['search_query']) && empty($_GET['type']) && empty($_GET['editor_id']) && empty($_GET['season']) && empty($_GET['cat_id'])) {
	$type = $config['newsfeed']['types'];
	$order = 'desc';
	$sort = 'last_update';
}
else {
	$type = $_GET['type'];
	$order = $_GET['order'];
	$sort = $_GET['sort'];
}

$newsfeed = new listing($_GET['search_query'], $_GET['season'], $_GET['editor_id'], $_GET['cat_id'], $type, $config['newsfeed']['count'], 0, $sort, $order);
echo $newsfeed->get_newsfeed();

?>