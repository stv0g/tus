<?php
############################################
# TUS Griesheim Handball CMS  (5.5.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)  #
############################################
# modules/gbook.php - The Guestbook Module #
############################################

echo '<div id="gbook"><span class="title">{lang:general:gbook}</span>';

$rights = access($usr_id, 'gbook', 0);

if ($command == 'add' and $rights['add']) {
	if ($_POST) {
		if ($_POST['web'] == 'http://') unset($_POST['web']);
		if ($_POST['captcha'] !== $_SESSION['captcha']) {
			echo '<div class="error">{lang:general:invalid_captcha}</div>';
			trigger_error('Wrong Captcha Code!', E_USER_ERROR);
		}
		elseif (mysql_query('INSERT INTO gbook (name, mail, web, city, text, ip, date) VALUES( \'' . mysql_real_escape_string(stripslashes($_POST['name'])) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['mail'])) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['web'])) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['city'])) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['text'])) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\', NOW())', $connection)) {
			echo '<div>{lang:gbook:success_add}</div>';
			trigger_error('New gbook entry!', E_USER_ERROR);
			redirect($site['path']['web'] . '/index.php?module=gbook#' . mysql_insert_id());
		}
		else {
			echo '<div class="error">{lang:gbook:error_add}</div>';
			redirect($site['path']['web'] . '/index.php?module=gbook&command=add');
		}
		
	}
	else {
		echo '<form accept-charset="utf-8" onsubmit="return check_gbook(this)" action="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=add" method="post">
				<table>
					<tr><td class="row_title">{lang:general:name}</td><td><input onblur="check_string(this)" type="text" name="name" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:mail}</td><td><input onblur="check_mail(this)" type="text" name="mail" /><br /><span style="display: none" class="error">{lang:general:invalid_mail}</span></td></tr>
					<tr><td class="row_title">{lang:general:web}</td><td><input onblur="check_uri(this, true)" type="text" value="http://" name="web" /><br /><span style="display: none" class="error">{lang:general:invalid_uri}</span></td></tr>
					<tr><td class="row_title">{lang:general:city}/{lang:general:club}</td><td><input onblur="check_string(this)" type="text" name="city" value="' . $config['site']['club'] . '" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:message}</td><td><textarea onblur="check_string(this)" name="text"></textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:captcha}</td><td><img id="captcha" alt="Captcha" src="' . $site['path']['web'] . '/include/captcha.php" /><a href="#" onclick="document.getElementById(\'captcha\').src = \'images/loading_small.gif\'; window.setTimeout(function() {document.getElementById(\'captcha\').src = \'include/captcha.php\'},500); return false;">{icon:arrow_rotate_clockwise:{lang:general:reload}}</a><br /><input onblur="check_string(this)" name="captcha" type="text" /><span style="display: none" class="error">{lang:general:invalid_captcha}</span><br />{lang:general:captcha_hint}</td></tr>
					<tr><td class="row_title"><input type="submit" name="add" value="{lang:general:submit}" /></td></tr>
				</table>
			</form>';
	}
}

elseif ($command == 'del' and $rights['del']) {
	redirect($site['path']['web'] . '/index.php?module=gbook');
	if (mysql_query('DELETE FROM gbook WHERE id = ' . $site['id'], $connection))
		echo '<div>{lang:gbook:success_del}</div>';
	else
		echo '<div> class="error"{lang:gbook:error_del}</div>';
}
elseif ($command == 'edit' and $rights['edit']) {
	if ($_POST['edit']) {
		if ($_POST['web'] == 'http://') unset($_POST['web']);
		
		$sql = 'UPDATE gbook SET name = \'' . mysql_real_escape_string(stripslashes($_POST['name'])) . '\', mail = \'' . mysql_real_escape_string(stripslashes($_POST['mail'])) . '\', web = \'' . mysql_real_escape_string(stripslashes($_POST['web'])) . '\', city = \'' . mysql_real_escape_string(stripslashes($_POST['city'])) . '\', text = \'' . mysql_real_escape_string(stripslashes($_POST['text'])) . '\', date = \'' . (int) $_POST['date_year'] . '-' . (int) $_POST['date_month'] . '-' . (int) $_POST['date_day'] . ' ' . (int) $_POST['time_hour'] . ':' . (int) $_POST['time_minute'] . ':00\' WHERE id = ' . $id;
		if (mysql_query($sql, $connection)) {
			echo '<div>{lang:gbook:success_edit}</div>';
			redirect($site['path']['web'] . '/index.php?module=gbook#' . mysql_insert_id());
		}
		else {
			echo '<div class="error">{lang:gbook:error_edit}</div>';
			redirect($site['path']['web'] . '/index.php?module=gbook&command=edit&id=' . $site['id']);
		}
	}
	else {
		$result = mysql_query('SELECT id, name, mail, web, city, text, UNIX_TIMESTAMP(date) AS date FROM gbook WHERE id = \'' . $id . '\'', $connection);
		$line = mysql_fetch_array($result);
		echo '<form accept-charset="utf-8" onsubmit="return check_gbook(this)" action="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=edit&amp;id=' . $line['id'] . '" method="post">
				<table>
					<tr><td class="row_title">{lang:general:name}</td><td><input onblur="check_string(this)" type="text" name="name" value="' . htmlspecialchars($line['name']) . '" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:mail}</td><td><input onblur="check_mail(this)" type="text" name="mail" value="' . htmlspecialchars($line['mail']) . '" /><br /><span style="display: none" class="error">{lang:general:invalid_mail}</span></td></tr>
					<tr><td class="row_title">{lang:general:web}</td><td><input onblur="check_uri(this, true)" type="text" name="web" value="' . htmlspecialchars(($line['web'] == '') ? 'http://' : $line['web']) . '" /><br /><span style="display: none" class="error">{lang:general:invalid_uri}</span></td></tr>
					<tr><td class="row_title">{lang:general:city}/{lang:general:club}</td><td><input onblur="check_string(this)" type="text" name="city" value="' . htmlspecialchars($line['city']) . '" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:message}</td><td><textarea onblur="check_string(this)" name="text">' . htmlspecialchars($line['text']) . '</textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:general:date}</td><td>
						<select onblur="check_number(this)" name="date_day" size="1">'; for ($i = 1; $i <= 31; $i++) echo '<option' . ($i == date('j', $line['date']) ? ' selected="selected"' : '') . ' value="' . $i . '">' . $i . '</option>'; echo '</select>
						<select onblur="check_number(this)" name="date_month" size="1">'; for ($i = 1; $i <= 12; $i++) echo '<option' . ($i == date('n', $line['date'])?' selected="selected"':''), ' value="', $i, '">' . strftime('%B', mktime(0,0,0, $i)) . '</option>'; echo '</select>
						<input onblur="check_number(this)" id="gbook_date_year" type="text" name="date_year" value="' . date('Y', $line['date']) . '" />
						<input onblur="check_number(this)" id="gbook_time_hour" type="text" name="time_hour" value="' . date('G', $line['date']) . '"/>
						<input onblur="check_number(this)" id="gbook_time_minute" type="text" name="time_minute" value="' . date('i', $line['date']) . '" />
						<br /><span style="display: none" class="error">{lang:general:invalid_number}</span>
					</td></tr>	  
					<tr><td colspan="2"><input type="submit" name="edit" value="{lang:general:edit}" /></td></tr>
				</table>
			</form>';
		mysql_free_result($result);
	}
}
elseif ($rights['show']) {
	if ($rights['add']) echo '<div id="gbook_add"><a href="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=add">{icon:add:{lang:gbook:add}} {lang:gbook:add}</a></div>';
	$result = mysql_query('SELECT id, DATE_FORMAT(date, GET_FORMAT(DATE,\'EUR\')) AS date_formated, name, mail, web, city, text, ip  FROM gbook ORDER BY id DESC', $connection);
	if (mysql_num_rows($result)) {
		$c = mysql_num_rows($result) + 1;
		while($line = mysql_fetch_array($result)) {
			$c--;
			echo '<table class="gbook_entry">
					<a name="' . $line['id'] . '" />
					<tr><td style="width: 20%; font-size: 1.3em">' . $c . '</td><td>{lang:gbook:date_added} ' . $line['date_formated'] . '</td></tr>
					<tr><td style="width: 20%" class="row_title">{lang:general:name}</td><td>' . hl($line['name'],  get_search_words()) . '</td></tr>';
					if (!empty($line['mail'])) echo '<tr><td style="width: 20%" class="row_title">{lang:general:mail}</td><td><a href="mailto:' . $line['mail'] . '">' . hl(htmlspecialchars($line['mail']), get_search_words()) . '</a></td></tr>';
					if (!empty($line['web'])) echo '<tr><td style="width: 20%" class="row_title">{lang:general:web}</td><td><a href="' . htmlspecialchars($line['web']) . '">' . htmlspecialchars(hl($line['web'], get_search_words())) . '</a></td></tr>';
					if (!empty($line['city'])) echo '<tr><td style="width: 20%" class="row_title">{lang:general:city}/{lang:general:club}</td><td>' . hl(htmlspecialchars($line['city']), get_search_words()) . '</td></tr>';
					echo '<tr><td style="width: 20%" class="row_title">{lang:general:message}</td><td>' . nl2br(hl(htmlspecialchars($line['text']), get_search_words())) . '</td></tr>';
					if ($rights['edit']) echo '<tr><td style="width: 20%" class="row_title">{lang:general:ip}</td><td>' . $line['ip'] . '</td></tr>';
					if ($rights['del'])	echo '<tr><td style="width: 20%" colspan="2"><a onclick="return confirm(\'{lang:gbook:confirm_del}\')" class="gbook_entry" href="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=del&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}} {lang:general:del}</a></td></tr>';
					if ($rights['edit']) echo '<tr><td style="width: 20%" colspan="2"><a href="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=edit&amp;id=' . $line['id'] . '">{icon:pencil:{lang:general:edit}} {lang:general:edit}</a></td></tr>';
			echo '</table>';
		}
		echo '<div>' . mysql_num_rows($result) .' {lang:gbook:entries}</div>';
	}
	else
		echo '<div class="error">{lang:gbook:no_entries}</div>';
	mysql_free_result($result);
}
else no_rights();

echo '</div>';
?>