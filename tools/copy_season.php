<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TuS Griesheim Handball CMS
 *
 * copy categories, rights and navigation from previous season
 *
 * @category	tool
 * @name		copy_season.php
 * @author		Steffen Vogel <info@steffenvogel.de>
 * @copyright	2006-2008 Steffen Vogel
 * @license		http://creativecommons.org/licenses/by-nc-nd/2.0/de/
 * @link		http://tusgriesheim.de/handball/
 * @since		File available since 2.5.2009 
 */

$site['path']['web'] = $_SERVER['SCRIPT_NAME'];
for ($i = 0; $i < 2; $i++) $site['path']['web'] = dirname($site['path']['web']);
$site['path']['web'] = (substr($site['path']['web'], -1, 1) == DIRECTORY_SEPARATOR) ? substr($site['path']['web'], 0, -1) : $site['path']['web'];
$site['path']['server'] = $_SERVER['DOCUMENT_ROOT'] . $site['path']['web'];

include($site['path']['server'] . '/include/init.inc.php');

$oldSeason = 2008;

$cats = mysql_query('SELECT * FROM categories WHERE season = ' .$oldSeason);
$access = mysql_query('SELECT * FROM access LEFT JOIN categories ON access.cat_id = categories.id WHERE categories.season = ' . $oldSeason);
$navigation = mysql_query('SELECT * FROM navigation LEFT JOIN categories ON navigation.cat_id = categories.id WHERE categories.season = ' . $oldSeason);

// Kategorien
while ($row = mysql_fetch_assoc($cats)) {
	$sql= 'INSERT INTO categories (name, season, ip, type) VALUES (\'' . $row['name'] . '\', ' . ($oldSeason + 1) . ', \'' . $_SERVER['REMOTE_ADDR'] . '\', ' . $row['type'] . ')';
	mysql_query($sql);
	$old2newId[$row['id']] = mysql_insert_id();
	echo $sql . ' => ' . $old2newId[$row['id']] . '<br />';
}

// Rechte
$sql = 'INSERT INTO access (usr_id, cat_id, module, `add`, `del`, `edit`, `show`) VALUES ';
while ($row = mysql_fetch_assoc($access)) {
	$sql .= '(' . $row['usr_id'] . ', ' . $old2newId[$row['cat_id']] . ', \'' . $row['module'] . '\', ' . $row['add'] . ', ' . $row['del'] . ', ' . $row['edit'] . ', ' . $row['show'] . '), ';
}
mysql_query(substr($sql, 0, -2));
echo $sql . '<br />';

// Navigation
$sql = 'INSERT INTO navigation (cat_id, title, uri) VALUES ';
while ($row = mysql_fetch_assoc($navigation)) {
	$uri = preg_replace('/cat_id=\d+/', 'cat_id=' . $old2newId[$row['cat_id']], $row['uri']);
	$uri = preg_replace('/season=\d+/', 'season=' . ($oldSeason + 1), $uri);
	$sql .= '(' . $old2newId[$row['cat_id']] . ', \'' . $row['title'] . '\', \'' . $uri . '\'), ';
}
mysql_query(substr($sql, 0, -2));
echo $sql . '<br />';

// Default article
$sql = 'INSERT INTO articles (cat_id, editor_id, date, last_update, type, title, text, ip) VALUES ';
foreach ($old2newId as $cat_id) {
	$sql .= '(' . $cat_id . ', 1, \'' . date('Y-m-d') . '\', NOW(), \'article\', \'Bitte anpassen!\', \'<p>Dies ist die Default Page der neuen Saison. Ich bitte alle Trainer diese anzupassen, über Neuigkeiten der neuen Saison zu informieren. Gegebenfalls könnt ihr auch den Inhalt der vergangen Saison übernehmen. Bitte auch den Titel anpassen.</p><p>Mit freudlichen Grüßen <a href="index.php?module=contact&usr_id=1">Steffen Vogel</a></p>\', \'' . $_SERVER['REMOTE_ADDR'] . '\'), ';
}
mysql_query(substr($sql, 0, -2));
echo $sql;

?>