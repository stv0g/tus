<?php
############################################
# TUS Griesheim Handball CMS  (5.5.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)  #
############################################
# modules/article.php - The Article Module #
############################################

$rights = access($usr_id, 'article', $cat_id);

echo '<div id="article">';

if (isset($id) && $command == 'del') {
	redirect($site['path']['web'] . '/index.php?module=article&cat_id=' . $cat_id);
	echo '<table><tr><td class="title">{lang:general:article}</td></tr>';
	if ($rights['del']) {
		if (mysql_query('DELETE FROM articles WHERE id = \'' . $id . '\'', $connection)) echo '<tr><td>{lang:article:success_del}</td></tr>';
		else echo '<tr><td class="error">{lang:article:error_del}</td></tr>';
	}
	else {
		no_rights();
	}
	
	echo '</table>';
}
elseif (!empty($id) && $command == 'edit') {
	if ($rights['edit']) {
		if ($_POST) {
			echo '<table><tr><td class="title">{lang:general:article}</h1></td></tr>';
			
			if ($_POST['update_editor'])
				$editor =  ', editor_id = ' . $site['usr']['id'];
			
			$sql = 'UPDATE articles SET title = \'' . mysql_real_escape_string(stripslashes($_POST['title'])) . '\', text = \'' . mysql_real_escape_string(stripslashes($_POST['text'])) . '\', type = \'' . mysql_real_escape_string($_POST['type']) . '\', ip = \'' . $_SERVER['REMOTE_ADDR'] . '\', date = \'' . (int) $_POST['date_year'] . '-' . (int) $_POST['date_month'] . '-' . (int) $_POST['date_day'] . '\', last_update = NOW()' . $editor . ' WHERE id = ' . $id;
			if (mysql_query($sql, $connection)) {
				echo '<tr><td>{lang:article:success_edit}</td></tr>';
				redirect($site['path']['web'] . '/index.php?module=article&id=' . $id . '&cat_id=' . $cat_id);
			}
			else {
				echo '<tr><td class="error">{lang:article:error_edit}' . mysql_error() . '</td></tr>';
				redirect($site['redirect'][0]);
			}

			echo '</table>';
		}
		else {
			include_editor($config['site']['editor']);
			$result = mysql_query('SELECT articles.id AS id, articles.type AS type, DATE_FORMAT(articles.last_update, UNIX_TIMESTAMP(articles.last_update) AS last_update, DATE_FORMAT(articles.date, GET_FORMAT(DATE,\'EUR\')) AS date_formated, UNIX_TIMESTAMP(articles.date) AS date, articles.title AS title, articles.text AS text, users.prename AS editor_prename, users.lastname AS editor_lastname, users.mail AS editor_mail FROM articles LEFT JOIN users ON users.id = articles.editor_id WHERE articles.id = ' . $id . ' LIMIT 1', $connection);
			$line = mysql_fetch_array($result);
			echo '<form onsubmit="return check_article(this);" name="article" action="' . $_SERVER['PHP_SELF'] . '?module=article&amp;command=edit&amp;id=' . $line['id'] . '&amp;cat_id=' . $cat_id . '" method="post" accept-charset="utf-8">
					<input type="hidden" name="send_to_custom_mail" />
					<table>
						<tr><td class="title">{lang:general:article}</td></tr>
						<tr><td class="row_title">{lang:general:title}</td><td><input onblur="check_string(this);" type="text" name="title" value="' . $line['title'] . '" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
						<tr><td class="row_title">{lang:article:type}</td><td><select id="type" value="' . $line['type'] . '" name="type" size="1">';
																					foreach ($config['articles']['types'] as $type) {
																						echo '<option' . (($type == $line['type']) ? ' selected="selected"' : '') . ' value="' . $type . '">{lang:type:' . $type . '}</option>';
																					}
																					echo '</select></td></tr>
						<tr><td class="row_title">{lang:general:date}</td><td>
							<select onblur="check_number(this)" name="date_day" size="1">'; for ($i = 1; $i <= 31; $i++) echo '<option' . ($i == date('j', $line['date']) ? ' selected="selected"' : '') . ' value="' . $i . '">' . $i . '</option>'; echo '</select>
							<select onblur="check_number(this)" name="date_month" size="1">'; for ($i = 1; $i <= 12; $i++) echo '<option' . ($i == date('n', $line['date'])?' selected="selected"':''), ' value="', $i, '">' . strftime('%B', mktime(0,0,0, $i)) . '</option>'; echo '</select>
							<input onblur="check_number(this)" id="article_date_year" type="text" name="date_year" value="' . date('Y', $line['date']) . '" />
							<br /><span style="display: none" class="error">{lang:general:invalid_number}</span>
						</td></tr>
						<tr><td class="row_title">{lang:general:text}</td><td><textarea class="html_editor" name="text">' . $line['text'] . '</textarea></td></tr>
						<tr><td class="row_title">{lang:article:update_editor}</td><td><input type="checkbox" name="update_editor" checked="checked"></td></tr>
						<tr><td colspan="2"><input type="submit" value="{lang:general:edit}" /></td></tr>
					</table>
				</form>';
			mysql_free_result($result);
		}
	}
	else {
		no_rights();
	}
}
elseif ($command == 'add') {
	if ($rights['add']) {
		if ($_POST) {
			if ($_POST['type'] == 'home')		$title = $cat_name . ' - ' . $_POST['rival'];
			if ($_POST['type'] == 'outwards')	$title = $_POST['rival'] . ' - ' . $cat_name;
			if ($_POST['type'] == 'article')	$title = $_POST['title'];
			if ($_POST['type'] == 'tournament')	$title = lang('article', 'type_tourney') . ' bei ' . htmlspecialchars($_POST['organizer']) . ': ' . $cat_name;
			
			if ($_POST['announce'])
				$title = lang('article', 'announce') . ': ' . $title;
			else {
				if ($_POST['type'] == 'home' || $_POST['type'] == 'outwards') $title .= "\t" . (int) $_POST['score_home'] . ':' . (int) $_POST['score_rival'];
				if ($_POST['type'] == 'tournament') $title .= "\t" . (int) $_POST['rank'] . '. Platz';
			}
			
			echo '<table><tr><td class="title">{lang:general:article}</td></tr>';
			
			$sql = 'INSERT INTO articles (title, text, type, cat_id, editor_id, ip, date, last_update) VALUES( \'' . mysql_real_escape_string(stripslashes($title)) . '\', \'' . mysql_real_escape_string(stripslashes($_POST['text'])) . '\', \'' . mysql_real_escape_string($_POST['type']) . '\', ' . $site['cat']['id'] . ', ' . $site['usr']['id'] . ', \'' . $_SERVER['REMOTE_ADDR'] . '\', \'' . (int) $_POST['date_year'] . '-' . (int) $_POST['date_month'] . '-' . (int) $_POST['date_day'] . '\', NOW())';
			if (mysql_query($sql, $connection)) {
				echo '<tr><td>{lang:article:success_add}</td></tr>';
				
				$mail = '<html>
							<body>' . $_POST['text'] . '</body>
						</html>';
				$subject = '[' . $config['site']['name'] . '] ' . htmlspecialchars($title);
				
				$config['mail']['header']['From'] = $site['usr']['mail'];
				$header = mail_headers();
				
				if ($_POST['send_to_daecho'] && $site['cat']['type'] == 2)	mail($config['mail']['daecho'], $subject, $mail, $header);
				if ($_POST['send_to_granzeiger']) mail($config['mail']['granzeiger'], $subject, $mail, $header);
				if ($_POST['send_to_grwoche']) mail($config['mail']['grwoche'], $subject, $mail, $header);
				if ($_POST['send_to_custom_mail'] && $_POST['custom_mail'])	mail($_POST['custom_mail'], $subject, $mail, $header);
				redirect($site['path']['web'] . '/index.php?module=article&id=' . mysql_insert_id($connection) . '&cat_id=' . $cat_id);
			}
			else
				echo '<tr><td class="error">{lang:article:error_add}</td></tr>';
			
			echo '</table>';
		}
		else {
			include_editor($config['site']['editor']);
			echo '<form onsubmit="return check_article(this);" name="article" action="' . $site['path']['web'] . '/index.php?module=article&amp;cat_id=' . $cat_id . '&amp;command=add" method="post" accept-charset="utf-8">
					<table>
						<tr><td class="title">{lang:general:article}</td></tr>
						<tr><td class="row_title">{lang:article:type}</td><td><select id="type" onchange="show()" name="type" size="1" value="3">';
							foreach ($config['articles']['types'] as $type) {
								echo '<option value="' . $type . '">{lang:type:' . $type . '}</option>';
							}
							echo '</select></td></tr>
						<tr id="announce"><td class="row_title">{lang:article:announce}</td><td><input onclick="announce_func(this);" type="checkbox" name="announce" /></td></tr>
						<tr id="rival" style="display: none"><td class="row_title">{lang:article:rival}</td><td><input onblur="check_string(this)" type="text" name="rival" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
						<tr id="score" style="display: none"><td class="row_title">{lang:article:score}</td><td><input onblur="check_number(this)" size="2" class="article_score" type="text" name="score_home" />&nbsp;-&nbsp;<input onblur="return check_number(this)" size="2" class="article_score" type="text" name="score_rival" /><br /><span style="display: none" class="error">{lang:general:invalid_number}</span></td></tr>
						<tr id="organizer" style="display: none"><td class="row_title">{lang:article:organizer}</td><td><input onblur="check_string(this)" type="text" name="organizer" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
						<tr id="rank" style="display: none"><td class="row_title">{lang:article:rank}</td><td><input onblur="check_number(this)" type="text" name="rank" /><br /><span style="display: none" class="error">{lang:general:invalid_number}</span></td></tr>
						<tr id="title"><td class="row_title">{lang:general:title}</td><td><input onblur="check_string(this)" type="text" name="title" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
						<tr id="press"><td class="row_title">{lang:article:send_to_press}</td><td><input type="checkbox" name="send_to_granzeiger" /> {lang:article:griesheimeranzeiger} <input type="checkbox" name="send_to_grwoche" /> {lang:article:griesheimerwoche}'; if ($cat_type == 2) echo ' <input type="checkbox" name="send_to_daecho" /> {lang:article:darmstaedterecho}'; echo ' <input onclick="custom_mail_func(this);" type="checkbox" name="send_to_custom_mail" /> {lang:article:custom_mail}</td></tr>
						<tr id="custom_mail" style="display: none"><td class="row_title">{lang:article:custom_mail}</td><td><input onblur="check_mail(this)" type="text" name="custom_mail" /><br /><span style="display: none" class="error">{lang:general:invalid_mail}</span></td></tr>
						<tr id="date"><td class="row_title">{lang:general:date}</td><td>
							<select onblur="check_number(this)" name="date_day" size="1">'; for ($i = 1; $i <= 31; $i++) echo '<option' . ($i==date('j') ? ' selected="selected"' : '') . ' value="' . $i . '">' . $i . '</option>'; echo '</select>
							<select onblur="check_number(this)" name="date_month" size="1">'; for ($i = 1; $i <= 12; $i++) echo '<option' . ($i==date('n')?' selected="selected"':''), ' value="', $i, '">' . strftime('%B', mktime(0,0,0, $i)) . '</option>'; echo '</select>
							<input id="article_date_year" onblur="check_number(this)" type="text" name="date_year" value="' . date('Y') . '" />
							<br /><span style="display: none" class="error">{lang:general:invalid_number}</span>
						</td></tr>
						<tr id="text"><td class="row_title">{lang:general:text}</td><td><textarea class="html_editor" onblur="check_string(this)" name="text"></textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
						<tr id="submit"><td colspan="2"><input type="submit" name="submit" value="{lang:general:add}" /></td></tr>
					</table>
				</form>';
		}
	}
	else {
		no_rights();
	}
}
elseif (empty($site['command'])) {	
	if ($rights['show']) {
		if (empty($site['id']) && empty($_GET['module'])) {
			$result = mysql_query('SELECT MIN(id) AS id FROM articles WHERE cat_id = ' . $site['cat']['id'] . ' LIMIT 1', $site['db']['connection']);
			$line = mysql_fetch_array($result);
			//TODO Compability
			$site['id'] = $line['id'];
			$id = $site['id'];
		}
			
		if (empty($site['id'])) {
			echo '<div class="title">{lang:general:article}</div>';
			
			$types = (empty($_REQUEST['type'])) ? $config['search']['types'] : $_REQUEST['type'];
			
			$search = new listing($_GET['search_query'], $season, $_GET['editor_id'], $cat_id, $types);
			//$search->show_filters();
			
			echo '<div id="search_results">';
				echo $search->get_html(true, true);
			echo '</div>';
			
			if ($rights['add']) echo '<div id="article_add"><a href="' . $site['path']['web'] . '/index.php?module=article&amp;command=add&amp;cat_id=' . $cat_id . '">{icon:add:{lang:article:add}} {lang:article:add}</a></div>';
		}
		
		if (!empty($site['id'])) {
			
			if (empty($_GET['module'])) {
				echo '<div id="newest_articles">';
			
				$newest = new listing('', 0, 0, $cat_id, $config['articles']['types'], $config['articles']['home_count']);
				echo $newest->get_html(false, true) . '</div>';
			}
			
			$result = mysql_query('SELECT articles.id AS id, articles.type AS type, UNIX_TIMESTAMP(articles.last_update) AS last_update, DATE_FORMAT(articles.date, GET_FORMAT(DATE,\'EUR\')) AS date_formated, UNIX_TIMESTAMP(articles.date) AS date, articles.title AS title, articles.text AS text, articles.view_count AS view_count, users.id AS editor_id, users.prename AS editor_prename, users.lastname AS editor_lastname, users.mail AS editor_mail FROM articles LEFT JOIN users ON users.id = articles.editor_id WHERE articles.id = ' . $id . ' LIMIT 1', $connection);
			if (mysql_num_rows($result) > 0) {
				$line = mysql_fetch_array($result);
				
				$head['title'] = '<title>' . $config['site']['name'] . ' - ' . $line['title'] . '</title>';
				$head['meta_author'] = '<meta name="author" content="' . $line['editor_prename'] . ' ' . $line['editor_lastname'] . ', ' . $line['editor_mail'] . '" />';
				$head['meta_date'] = '<meta name="date" content="' . date('r', $line['date']) . '" />';
				$head['meta_reply'] = '<meta http-equiv="reply-to" content="' . $line['editor_mail'] . '"/>';

				echo '<div class="title">{icon:' . $config['types'][$line['type']] . ':{lang:type:' . $line['type'] . '}} ' . hl($line['title'], get_search_words()) . '</div>
						<div>' . hl($line['text'], get_search_words()) . '</div>
						<table id="article_details">
							<tr><td class="column_title">{lang:article:published}</td><td class="column_title">{lang:article:edited}</td><td class="column_title">{lang:article:publisher}</td><td class="column_title">{lang:general:view_count}</td></tr>
							<tr><td>' . strftime('%A, %e. %B %Y', $line['date']) . '</td><td>' . strftime('%A, %e. %B %Y, %H:%M', $line['last_update'])  . '</td><td><a href="' . $site['path']['web'] . '/index.php?module=contact&amp;usr_id=' . $line['editor_id'] . '">' . $line['editor_prename'] . ' ' . $line['editor_lastname'] . '</a></td><td>' . $line['view_count'] . ' mal</td></tr><tr>';
							if ($rights['del']) echo '<td><a onclick="return confirm(\'{lang:article:confirm_del}\')" href="' . $site['path']['web'] . '/index.php?module=article&amp;command=del&amp;cat_id=' . $cat_id . '&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}} {lang:general:del}</a></td>';
							if ($rights['edit']) echo '<td><a href="' . $site['path']['web'] . '/index.php?module=article&amp;command=edit&amp;cat_id=' . $cat_id . '&amp;id=' . $line['id'] . '">{icon:pencil:{lang:general:edit}} {lang:general:edit}</a></td></tr>';
				echo '</tr></table>';
				
				mysql_query('UPDATE articles SET view_count = view_count + 1 WHERE id = '  . $site['id'], $site['db']['connection']);

			}
			else {
				echo '<div class="error">{lang:article:error_id}</div>';
				trigger_error('Invalid article id!', E_USER_WARNING);
			}
		}
	}
	else {
		no_rights();
	}
}

echo '</div>';
?>
