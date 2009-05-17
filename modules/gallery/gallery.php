<?php

$rights = access($site['usr']['id'], 'gallery', $site['cat']['id']);

echo '<div id="gallery">
		<div class="title">{lang:general:gallery}</div>';

if ($command == 'add_pic' && $rights['add'] && $main_module == 'gallery') {
	if ($_POST) {
		$allowed_ext = array('jpeg', 'gif', 'png');
		$size = getimagesize($_FILES['upload_picture']['tmp_name']);
		
		if (!in_array(image_type_to_extension($size[2], false), $allowed_ext))
			echo '<div class="error">Falscher Dateityp: ' . image_type_to_extension($size[2], false) . '</div>';
		elseif ($_FILES['upload_picture']['size'] > $config['gallery']['max_size'] * 1024)
			echo '<div class="error">Datei zu groß! Maximum: ' . $config['gallery']['max_size'] . 'kB</div>';
		else {
			$url = upload_image($_FILES['upload_picture'], $_POST['title'], $_POST['alt'], $site['cat']['id'], $site['usr']['id']);
			if ($url != false)
				echo '<div class="success">Bild erfolgreich hochgeladen: <a href="' . $url . '">' . $url . '</a><br /><img src="' . $url . '" alt="' . $_POST['alt'] . '" /></div>';
			else
				echo '<div class="error">Unknown Servererror!</div>';
		}
	}
	else {
		echo '<form accept-charset="utf-8" action="' . $site['path']['web'] . '/index.php?cat_id=' . $site['cat']['id'] . '&amp;module=gallery&amp;command=add_pic" enctype="multipart/form-data" method="post">
		<table>
		<tr><td class="row_title">Titel</td><td><input type="text" name="title" /></td></tr>
		<tr><td class="row_title">Beschreibung</td><td><textarea name="alt"></textarea></td></tr>
		<tr><td class="row_title">Datei</td><td><input type="file" name="upload_picture" /></td></tr>
		<tr><td><input type="submit" name="add" value="{lang:general:add}" /></td></tr>
		</table>
		</form>';
	}
}
if ($command == 'add_gal' && $rights['add'] && $main_module == 'gallery') {
	
}
elseif ($command == 'del' && $rights['del'] && $main_module == 'gallery') {
	
}
elseif ($command == 'edit' && $rights['edit'] && $main_module == 'gallery') {
	
}
elseif ($gal_id > 0 && $rigths['show'] && $main_module == 'gallery') {
	
}
elseif ($gal_id == 0 && $rigths['show'] && $main_module == 'gallery') {
	
}
elseif ($id > 0 && $rigths['show'] && $main_module == 'gallery') {
	
}
elseif ($id == 0 && $rights['show'] && $main_module == 'gallery') {
	
}
elseif ($main_module != 'gallery' && $rights['show']) {
	
}
else no_rights();

echo '</div>';


?>