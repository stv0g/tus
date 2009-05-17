<?php
############################################
# TUS Griesheim Handball CMS (5.5.2007)    #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)  #
############################################
# modules/access.php - The Access Module   #
############################################

echo '<div id="access"><span class="title">{lang:general:access}</span>';

if (isset($site['usr']['id']) && $command == 'logout') {
	redirect($redirect[1]);
	
	echo '<div>{lang:access:logout_message} ' . $site['usr']['prename'] . ' ' . $site['usr']['lastname'] . '!</div>';
	
	session_destroy();
	unset($usr_name);
	unset($usr_prename);
	unset($usr_lastname);
	unset($usr_id);
	unset($usr_mail);
	//TODO Compability
	unset($site['usr']);
}
elseif (empty($site['usr']['id']) && $site['command'] == 'login' && $_POST) {
	$result = mysql_query('SELECT id, name, prename, lastname, pw, active, mail, DATE_FORMAT(last_login, \'%d.%m.%Y %H:%i:%s\') AS last_login, login_count FROM users WHERE name = \'' . mysql_real_escape_string(stripslashes($_POST['name'])) . '\'', $connection);
	$line = mysql_fetch_array($result);
	if ($_POST['sid'] != session_id()) {
		redirect($site['path']['web'] . '/index.php', 10);
		echo '<div class="error">{lang:access:missing_session_cookie}' . '</div>';
	}
	elseif ($line['pw'] === md5($_POST['pw'])) {
		if($line['active'] == true) {
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['usr_id'] = $line['id'];
			$_SESSION['usr_name'] = $line['name'];
			$_SESSION['usr_mail'] = $line['mail'];
			$_SESSION['usr_prename'] = $line['prename'];
			$_SESSION['usr_lastname'] = $line['lastname'];
			
			//TODO Compability
			$usr_name = $_SESSION['usr_name'];
			$usr_prename = $_SESSION['usr_prename'];
			$usr_lastname = $_SESSION['usr_lastname'];
			$usr_id = $_SESSION['usr_id'];
			$usr_mail = $_SESSION['usr_mail'];
			
			$site['usr']['name'] = $_SESSION['usr_name'];
			$site['usr']['prename'] = $_SESSION['usr_prename'];
			$site['usr']['lastname'] = $_SESSION['usr_lastname'];
			$site['usr']['id'] = $_SESSION['usr_id'];
			$site['usr']['mail'] = $_SESSION['usr_mail'];
					
			mysql_query('UPDATE users SET last_ip = \'' . $_SERVER['REMOTE_ADDR'] . '\', login_count = login_count + 1 WHERE id = ' . $site['usr']['id'], $site['db']['connection']);
			redirect($site['redirect'][1]);
			echo '<div>{lang:access:login_message} ' . $site['usr']['prename'] . ' ' . $site['usr']['lastname'] . '!</div>
					<div>{lang:access:last_login} ' . $line['last_login'] . '!</div>
					<div>{lang:access:login_count_1} ' . $line['login_count'] . '{lang:access:login_count_2}!</div>';
			mysql_free_result($result);
		}
		else {
			redirect($site['path']['web'] . '/index.php?module=access', 10);
			echo '<div class="error">{lang:access:not_active}' . '</div>';
			trigger_error('Deactivated user tries to login', E_USER_WARNING);
		}
	}
	else {
		redirect($site['path']['web'] . '/index.php?module=access');
		echo '<div class="error">{lang:access:error_wrong_login}' . '</div>';
	}
}
elseif (isset($site['usr']['id']) && $command == 'pw') {
	if ($_POST) {
		$result = mysql_query('SELECT pw, active FROM users WHERE id = ' . $site['usr']['id'], $site['db']['connection']);
		$line = mysql_fetch_array($result);
		if ($line['pw'] == md5($_POST['old_pw']) && $line['active'] == true && !empty($_POST['new_pw']) && $_POST['new_pw'] == $_POST['repeat_pw']) {
			mysql_query('UPDATE users SET pw = MD5(\'' . $_POST['new_pw'] . '\') WHERE id = ' . $site['usr']['id'], $site['db']['connection']);
			if (mysql_affected_rows($site['db']['connection'])  > 0) {
				echo '<div>{lang:access:success_change_pw}</div>';
				redirect($site['path']['web'] . '/index.php');
			}
		}
		else {
			echo '<div class="error">{lang:access:error_change_pw}</div>';
			redirect($site['path']['web'] . '/index.php?module=access&command=pw');
		}
	}
	else {
		echo '<form onsubmit="return check_pws(this);" accept-charset="utf-8" action="' . $site['path']['web'] . '/index.php?module=access&amp;command=pw" method="post">
				<table>
					<tr><td class="row_title">{lang:access:old_pw}</td><td><input onblur="check_string(this);" type="password" name="old_pw" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:access:new_pw}</td><td><input id="new_pw" onblur="check_string(this); compare_pws(this, getElementById(\'repeat_pw\'));" type="password" name="new_pw" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:access:repeat_pw}</td><td><input id="repeat_pw" onkeyup="compare_pws(getElementById(\'new_pw\'), this);" type="password" name="repeat_pw" /><br /><span style="display: none" class="error">{lang:access:unmatching_pws}</span></td></tr>
					<tr><td><input type="submit" value="{lang:general:change}" /></td></tr>
				</table>
			</form>';
	}
}
elseif (isset($site['usr']['id']))
	echo '<div>' . $site['usr']['prename'] . ' ' . $site['usr']['lastname'] . '{lang:access:logged_in_message}</div>
			<div><a href="' . $site['path']['web'] . '/index.php?module=access&amp;command=logout">{icon:door_in:{lang:general:access}}&nbsp;{lang:access:logout}</a><br />
			<a href="' . $_SERVER['PHP_SELF'] . '?module=access&amp;command=pw">{icon:script_edit:{lang:access:change_pw}}&nbsp;{lang:access:change_pw}</a></div>';
else
	echo '<form onsubmit="return check_login(this);" accept-charset="utf-8" action="' . $site['path']['web'] . '/index.php?module=access&amp;command=login" method="post">
				<input type="hidden" name="sid" value="' . session_id() . '" />
				<table>
					<tr><td class="row_title">{lang:access:user}</td><td><input onblur="check_string(this);" type="text" name="name" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td class="row_title">{lang:access:pw}</td><td><input onblur="check_string(this);" type="password" name="pw" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
					<tr><td><input type="submit" value="{lang:access:login}" /></td></tr>
				</table>
			</form>';

echo '</div>';
?>