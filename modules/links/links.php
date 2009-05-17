<?php
############################################
# TUS Griesheim Handball CMS  (18.5.2007)  #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)        #
############################################
# modules/links.php - The Link Module      #
############################################

$rights = access($usr_id, 'links', 0);

echo '<div id="links">';

if ($command == 'add' and $rights['add']) {
	if ($_POST) {
		redirect($_SERVER['PHP_SELF'] . '?module=links');
		if (mysql_query('INSERT INTO links (uri, description, ip) VALUES( \'' . mysql_real_escape_string(stripslashes($_POST['uri'])) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['description'])) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\')', $connection)) echo '<tr><td>{lang:links:success_add}</tr></td>';
		else echo '<div class="error">{lang:links:error_add}</div>';
	}
	else {
		echo '<form accept-charset="utf-8" onsubmit="return check_links(this)" action="' . $_SERVER['PHP_SELF'] . '?module=links&amp;command=add" method="post">
				<table>
					<tr><td class="row_title">{lang:general:uri}</td><td><input onblur="check_uri(this)" type="text" name="uri" value="http://"><br /><span style="display: none" class="error">{lang:general:invalid_uri}</span></td></tr>
					<tr><td class="row_title">{lang:general:description}</td><td><textarea onblur="check_string(this)" name="description">' . $line['description'] . '</textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td><input type="submit" value="{lang:general:add}" /></td></tr>
				</table>
			</form>';
	}
}
elseif ($command == 'edit' and $rights['edit']) {
	if ($_POST) {
		redirect($_SERVER['PHP_SELF'] . '?module=links');
		if (mysql_query('UPDATE links SET uri = \'' . mysql_real_escape_string(stripslashes($_POST['uri'])) . '\', description = \'' . mysql_real_escape_string(stripslashes($_POST['description'])) . '\', ip = \'' . $_SERVER['REMOTE_ADDR'] . '\' WHERE id = \'' . $id . '\'', $connection)) echo '<tr><td>{lang:links:success_edit}</tr></td>';
		else echo '<div class="error">{lang:links:error_edit}</div>';
	}
	else {
		$result = mysql_query('SELECT uri, description FROM links WHERE id = \'' . $id . '\'', $connection);
		$line = mysql_fetch_array($result);
		echo '<form accept-charset="utf-8" onsubmit="return check_links(this)" action="' . $_SERVER['PHP_SELF'] . '?module=links&amp;command=edit&amp;id=' . $id . '" method="post">
				<table>
					<tr><td class="row_title">{lang:general:uri}</td><td><input onblur="check_string(this)" type="text" name="uri" value="' . $line['uri'] . '"><br /><span style="display: none" class="error">{lang:general:invalid_uri}</span></td></tr>
					<tr><td class="row_title">{lang:general:description}</td><td><textarea onblur="check_string(this)" name="description">' . $line['description'] . '</textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td><input type="submit" value="{lang:general:edit}" /></td></tr>
				</table>
			</form>';
	}
}
elseif ($command == 'del' and $rights['del']) {
	redirect($_SERVER['PHP_SELF'] . '?module=links');
	if (mysql_query('DELETE FROM links WHERE id = \'' . $id . '\'', $connection))
		echo '<tr><td>{lang:links:success_del}</td></tr>';
	else
		echo '<div class="error">{lang:links:error_del}</div>';
}
elseif ($rights['show']) {
	$result = mysql_query('SELECT id, uri, description FROM links');
	$header = '<table>
				<tr><td class="column_title">{lang:general:uri}</td><td class="column_title">{lang:general:description}</td>';
	$footer = '</table>';
	
	while ($line = mysql_fetch_array($result)) {
		$links .=  '<tr><td><a href="' . htmlentities($line['uri']) . '">' . $line['uri'] . '</a></td><td>' . $line['description'] . '</td>';
		if($rights['del'] || $rights['edit']) {
			$show_extras = true;
			$links .= '<td>';
			if ($rights['del'])
				$links .=  '<a onclick="return confirm(\'{lang:links:confirm_del}\')" href="' . $_SERVER['PHP_SELF'] . '?module=links&amp;command=del&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}}</a>';
			if ($rights['edit'])
				$links .=  '<a href="' . $_SERVER['PHP_SELF'] . '?module=links&amp;command=edit&amp;id=' . $line['id'] . '">{icon:pencil:{lang:general:edit}}</a>';
			$links .= '</td>';
		}			
		$links .= '</tr>';	
	}
	
	if($show_extras)
		$header .= '<td class="column_title">{lang:general:extras}</td>';
	$header .= '</tr>';	
	
	echo $header . $links . $footer;
	
	if ($rights['add']) echo '<div id="links_add"><a href="' . $_SERVER['PHP_SELF'] . '?module=links&amp;command=add">{icon:add:{lang:general:add}} {lang:general:add}</a></div>';
	mysql_free_result($result);
}
else no_rights();

echo '</div>';
?>