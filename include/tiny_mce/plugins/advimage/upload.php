<?php
$site['path']['server'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

include $site['path']['server'] . '/include/init.inc.php';

$rights = access($site['usr']['id'], 'gallery', $site['cat']['id']);

if ($rights['add']) {
	if ($_POST) {
		$allowed_ext = array('jpeg', 'gif', 'png');
		$size = getimagesize($_FILES['upload_picture']['tmp_name']);
		
		if (!in_array(image_type_to_extension($size[2], false), $allowed_ext))
			die('<div id="error">Falscher Dateityp: ' . image_type_to_extension($size[2], false) . '</div>');
		elseif ($_FILES['upload_picture']['size'] > $config['gallery']['max_size'] * 1024)
			die('<div id="error">Datei zu groß! Maximum: ' . $config['gallery']['max_size'] . 'kB</div>');
		elseif (!is_writable($site['path']['server'] . '/images/gallery/')) {
			die('<div id="error">Upload Ordner besitzt keine Schrreibrechte</div>');
		}
		else {
			$id = upload_image($_FILES['upload_picture'], stripslashes($_POST['title']), stripslashes($_POST['alt']), (int) $_GET['cat_id'], $site['usr']['id']);
			if ($id != false) {
				$url = $site['url'] . '/modules/gallery/picture.php?id=' . $id . '&thumb=1';
				echo '<div id="success">' . $url . '</div>';
			}
			else {
				die('<div id="error">Unbekannter Fehler!</div>');
			}
		}
	}
	else {
		die('<div id="error">Keine Datei ausgewählt!</div>');
	}
}
else {
	die('<div id="error">Keine Berechtigung!</div>');
}
?>