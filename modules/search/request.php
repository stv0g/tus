<?php

$site['path']['web'] = $_SERVER['SCRIPT_NAME'];
for ($i = 0; $i < 3; $i++) $site['path']['web'] = dirname($site['path']['web']);
$site['path']['web'] = (substr($site['path']['web'], -1, 1) == DIRECTORY_SEPARATOR) ? substr($site['path']['web'], 0, -1) : $site['path']['web'];
$site['path']['server'] = $_SERVER['DOCUMENT_ROOT'] . $site['path']['web'];

include $site['path']['server'] . '/include/init.inc.php';

$search = new listing(stripslashes($_REQUEST['search_query']), $_REQUEST['season'], $_REQUEST['editor_id'], $_REQUEST['cat_id'], $_REQUEST['type'], 0, 0, $_REQUEST['sort'], $_REQUEST['order']);

$search_result = preg_replace_callback('={lang:(.*?):(.*?)}=si', 'lang', $search->get_html(true, true));
$search_result = preg_replace_callback('={icon:(.*?):(.*?)}=si', 'icon', $search_result);

echo $search_result;
?>