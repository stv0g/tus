<?php
##################################################
# TUS Griesheim Handball CMS  (5.5.2007)         #
# Web: http://www.tusgriesheim.de/handball       #
# Mail: handball@tusgriesheim.de                 #
# by Steffen Vogel (info@steffenvogel.de)        #
##################################################
# modules/newsletter.php - The Newsletter Module #
##################################################

echo '<div id="newsletter">';
	
$rights = access($usr_id, 'newsletter', 0);

if ($_REQUEST['add']) {	
	redirect($site['path']['web'] . '/index.php');
	$result = mysql_query('SELECT * FROM newsletter WHERE mail = \'' . mysql_real_escape_string(stripslashes($_REQUEST['mail'])) . '\' AND active = 1', $site['db']['connection']);
	
	if (mysql_num_rows($result) > 0)
		echo '<div class="error">{lang:newsletter:error_already_added}</div>';
	elseif (!check_mail(stripslashes($_REQUEST['mail'])))
		echo '<div class="error">{lang:general:error_incorrect_mail}</div>';
	else {
		if (mysql_query('INSERT INTO newsletter (mail, name, ip) VALUES(\'' . mysql_real_escape_string(stripslashes($_REQUEST['mail'])) . '\', \'' . mysql_real_escape_string(stripslashes($_REQUEST['name'])) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\')', $site['db']['connection'])) {
			$result = mysql_query('SELECT * FROM newsletter WHERE id = ' . mysql_insert_id($site['db']['connection']) . ' LIMIT 1', $site['db']['connection']);
			$user = mysql_fetch_assoc($result);
			
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?module=newsletter&amp;command=confirm_add&amp;id=' . $user['id'] . '&amp;token=' . htmlentities(newsletter_token($user));
			$mail = '<html>
						<body>' .
							lang('newsletter', 'confirm_add') . '<br />
							<a href="' . $url . '">' . $url . '</a>
						</body>
					</html>';
			
			mail($_REQUEST['mail'], mb_encode_mimeheader(lang('newsletter', 'confirm_subject'), 'UTF-8', 'Q', "\n"), $mail, mail_headers());
			echo '<div>{lang:newsletter:success_add_confirm}</div>';
		}
	}
	
	mysql_free_result($result);
}
elseif ($_REQUEST['remove']) {
	redirect($site['path']['web'] . '/index.php');
	$result = mysql_query('SELECT * FROM newsletter WHERE mail = \'' . mysql_real_escape_string(stripslashes($_REQUEST['mail'])) . '\'', $site['db']['connection']);
	if (mysql_num_rows($result) == 1) {
		$user = mysql_fetch_array($result);
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?module=newsletter&amp;command=confirm_del&amp;id=' . $user['id'] . '&amp;token=' . htmlentities(newsletter_token($user));
		$mail = '<html>
					<body>
						' . lang('newsletter', 'confirm_del') . '<br />
						<a href="' . $url . '">' . $url . '</a>
					</body>
				</html>';
		
		mail($user['mail'], mb_encode_mimeheader(lang('newsletter', 'confirm_subject'), 'UTF-8', 'Q', "\n") , $mail, mail_headers());
		echo '<div>{lang:newsletter:success_del_confirm}</div>';
	}
	else {
		echo '<div class="error">{lang:newsletter:error_already_deleted}</div>';
		trigger_error('Mail address already deleted', E_USER_ERROR);
	}
	
	mysql_free_result($result);
}
elseif ($site['command'] == 'confirm_add') {
	redirect($site['path']['web'] . '/index.php');
	$result = mysql_query('SELECT id, mail, active FROM newsletter WHERE id = ' . $site['id'] . ' AND active = 0 LIMIT 1', $site['db']['connection']);
	if (mysql_num_rows($result) > 0) {
		$user = mysql_fetch_assoc($result);
		if ($_GET['token'] == newsletter_token($user)) {
			if (mysql_query('UPDATE newsletter SET active = 1 WHERE id = ' . $site['id'], $site['db']['connection']))
				echo '<tr><td>{lang:newsletter:success_activation}</td</tr>';
		}
		else {
			echo '<div class="error">{lang:newsletter:error_invalid_token}</div>';
			trigger_error('Invalid token', E_USER_ERROR);
		}
	}
	else {
		echo '<div class="error">{lang:newsletter:error_already_activated}</div>';
		trigger_error('Mail address already activated', E_USER_ERROR);
	}
	
	mysql_free_result($result);
}
elseif ($site['command'] == 'confirm_del') {
		
	redirect($site['path']['web'] . '/index.php');
	$result = mysql_query('SELECT * FROM newsletter WHERE id = ' . $site['id'], $site['db']['connection']);
	if (mysql_num_rows($result) > 0) {
		$user = mysql_fetch_assoc($result);
		if ($_GET['token'] == newsletter_token($user)) {
			if (mysql_query('DELETE FROM newsletter WHERE id = ' . (int) $site['id'], $site['db']['connection']))
				echo '<tr><td>{lang:newsletter:success_del}</td</tr>';
		}
		else {
			echo '<div class="error">{lang:newsletter:error_invalid_token}</div>';
			trigger_error('Invalid token', E_USER_ERROR);
		}
	}
	else {
		echo '<div class="error">{lang:newsletter:error_already_deleted}</div>';
		trigger_error('Mailaddress already deleted', E_USER_ERROR);
	}
		
	mysql_free_result($result);
}
elseif ($site['command'] == 'show' && $rights['show']) {	
	$result = mysql_query('SELECT id, mail, name, active FROM newsletter ORDER BY mail', $site['db']['connection']);
	if (mysql_num_rows($result) > 0) {
		echo '<table>
				<tr><td class="column_title">{lang:general:mail}</td><td class="column_title">{lang:general:name}</td><td class="column_title" colspan="2">{lang:general:extras}</td></tr>';
		while ($line = mysql_fetch_array($result)) {
			$state = $line['active'] ? '{icon:tick:{lang:newsletter:active}}' : '{icon:cross:{lang:newsletter:inactive}}';
			echo '<tr><td><a href="mailto:' . $line['mail'] . '">' . $line['mail'] . '</a></td><td>' . $line['name'] . '</td><td><a href="' . $site['path']['web'] . '/index.php?module=newsletter&amp;command=toggle&amp;id=' . $line['id'] . '">' . $state . '</a>';
			if ($rights['del'])
				echo '<a href="index.php?module=newsletter&amp;command=del&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}}';
			echo '</td></tr>';
		}
		echo '</table><div id="newsletter_count">' . mysql_num_rows($result) . ' {lang:newsletter:subscribers}</div>';
	}
	else
		echo '<div class="error">{lang:newsletter:no_mails}</div>';
	
	mysql_free_result($result);
}
elseif ($site['command'] == 'del' && $rights['del']) {
	redirect($site['path']['web'] . '/index.php?module=newsletter&amp;command=show');
	mysql_query('DELETE FROM newsletter WHERE id = \'' . $site['id'] . '\'', $site['db']['connection']);
	if (mysql_affected_rows($site['db']['connection']) == 1)
		echo '<div>{lang:newsletter:success_del}</div>';
	else
		echo '<div class="error">{lang:newsletter:error_del}</div>';
}
elseif ($site['command'] == 'toggle' && $rights['edit']) {
	redirect($site['path']['web'] . '/index.php?module=newsletter&command=show');
	$result = mysql_query('SELECT id, active FROM newsletter WHERE id = ' . $site['id'] . ' LIMIT 1', $site['db']['connection']);
	$line = mysql_fetch_array($result);
		
	if ($line['active'] == true) {
		if (mysql_query('UPDATE newsletter SET active = 0 WHERE id = ' . $site['id'], $site['db']['connection']))
			echo '<div>{lang:newsletter:success_deactivated}</div>';
	}
	else {
		if (mysql_query('UPDATE newsletter SET active = 1 WHERE id = ' . $site['id'], $site['db']['connection']))
			echo '<div>{lang:newsletter:success_activated}</div>';
	}
	mysql_free_result($result);
}
else {
	echo '<form accept-charset="utf-8" action="' . $site['path']['web'] . '/index.php?module=newsletter" method="post">
			<table>
				<tr><td colspan="2">{lang:newsletter:description}</td></tr>
				<tr><td class="row_title">{lang:general:name} ({lang:general:optional})</td><td><input onblur="check_string(this, true);" type="text" name="name" /><br/><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
				<tr><td class="row_title">{lang:general:mail}</td><td><input id="newsletter_mail" onblur="check_mail(this);" type="text" name="mail" /><br /><span style="display: none" class="error">{lang:general:invalid_mail}</span></td></tr>
				<tr><td colspan="2"><input type="submit" name="add" value="{lang:general:submit}" /> <input type="submit" name="remove" value="{lang:general:del}" /></td></tr>';
				if ($rights['show']) echo '<tr><td colspan="2"><a href="' . $site['path']['web'] . '/index.php?module=newsletter&amp;command=show">{icon:text_list_bullets:{lang:newsletter:show}}&nbsp;{lang:newsletter:show}</a></td</tr>';

	echo '</table>
		</form>';
}

echo '</div>';
?>