<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TuS Griesheim Handball CMS
 *
 * add categories
 *
 * @category	tool
 * @name		add_cats.php
 * @author		Steffen Vogel <info@steffenvogel.de>
 * @copyright	2006-2008 Steffen Vogel
 * @license		http://creativecommons.org/licenses/by-nc-nd/2.0/de/
 * @link		http://tusgriesheim.de/handball/
 * @since		File available since 3.10.2007 
 */

//TODO Ausnahmen einbauen eventuell noch ein Frontend

$site['path']['web'] = $_SERVER['SCRIPT_NAME'];
for ($i = 0; $i < 2; $i++) $site['path']['web'] = dirname($site['path']['web']);
$site['path']['web'] = (substr($site['path']['web'], -1, 1) == DIRECTORY_SEPARATOR) ? substr($site['path']['web'], 0, -1) : $site['path']['web'];
$site['path']['server'] = $_SERVER['DOCUMENT_ROOT'] . $site['path']['web'];

include($site['path']['server'] . '/include/init.inc.php');

$cats = array('Home',
			'Vorstand',
			'Jugendleiter',
			'Schiedsrichter',
			'Herren 1',
			'Herren 2',
			'Alte Herren',
			'Damen',
			'mA Jugend',
			'mB Jugend',
			'mC1 Jugend',
			'mC2 Jugend',
			'wC Jugend',
			'mD Jugend',
			'mE1 Jugend',
			'mE2 Jugend',
			'Minis');
			
$season = date('Y');

$nav = array('{lang:general:article}'=>'index.php?module=article',
			'{lang:general:ranking}'=>'index.php?module=sis&type=4',
			'{lang:general:schedule}'=>'index.php?module=sis&type=1');

$catswithoutsis = array('Home',
						'Vorstand',
						'Jugendleiter',
						'Schiedrichter',
						'Alte Herren');
			
foreach ($cats as $cat) {
	if (mysql_query('INSERT INTO categories (name, season, ip) VALUES(\'' . $cat . '\', \'' . $season . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\')')) echo 'Kategorie "' . $cat . '" wurde erfolgreich angelegt!<br />';
	else echo mysql_error();
	$cat_id = mysql_insert_id();
	
	echo $cat_id . '<br>';
	
	foreach ($nav as $nav_title => $nav_uri) {
		if (mysql_query('INSERT INTO navigation (cat_id, title, uri) VALUES(' . $cat_id . ', \'' . $nav_title . '\', \'' . $nav_uri . '&cat_id=' . $cat_id . '\')')) echo '.';
		else echo mysql_error($connection);
	}
	
	if (in_array($cat, $catswithoutsis))
		mysql_query('DELETE FROM navigation WHERE cat_id = ' . $cat_id . ' AND (title = \'{lang:general:schedule}\' OR title = \'{lang:general:ranking}\')');
		
	mysql_query('INSERT INTO articles (cat_id, editor_id, date, type, title, text, ip) VALUES(' . $cat_id . ', 1, \'' . date('Y-m-d') . '\', \'article\', \'TuS Griesheim ' . $cat . '\', \'Dies ist die Default Page der neuen Saison. Ich bitte alle Trainer diese kurz zu verändern, bzw. über Neuigkeiten der neuen Saison zu informieren. Gegebenfalls könnt ihr auch den Inhalt der vergangen Saison übernehmen. Bitte auch den Titel anpassen.<br />Mit freudlichen Grüßen Steffen Vogel\', \'' . $_SERVER['REMOTE_ADDR'] . '\')');
}

?>