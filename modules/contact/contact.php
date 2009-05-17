<?php
############################################
# TUS Griesheim Handball CMS  (5.5.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)  #
############################################
# modules/contact.php - The Contact Module #
############################################

echo '<div id="contact"><span class="title">{lang:general:contact}</span>';

if ($command == 'send') {
	if (empty($_POST['name'])) echo '<div class="error">{lang:contact:empty_name}</div>';
	elseif (empty($_POST['text'])) echo '<div class="error">{lang:contact:empty_text}</tdiv>';
	elseif (!check_mail($_POST['mail'])) echo '<div class="error">{lang:general:error_incorrect_mail}</div>';
	elseif (empty($_POST['subject'])) echo '<div class="error">{lang:contact:empty_subject}</div>';
	elseif ($_POST['captcha'] !== $_SESSION['captcha']) {
		echo '<div class="error">{lang:general:invalid_captcha}</div>';
		trigger_error('Wrong Captcha Code!', E_USER_ERROR);
	}
	else {
		redirect($site['path']['web'] . '/index.php');
		$mail = '<html>
					<body>
						' . lang('general', 'name') . ': ' . htmlspecialchars(stripslashes($_POST['name'])) . '<br />
						' . ((isset($_POST['web'])) ? lang('general', 'web') . ': ' . htmlspecialchars(stripslashes( $_POST['web'])) . '<br />' : '') .
						lang('general', 'message') . ': ' . nl2br(htmlspecialchars(stripslashes($_POST['text']))) . '<br />
					</body>
				</html>';
		
		$subject = '[' . $config['site']['name'] . '] ' . htmlspecialchars(stripslashes($_POST['subject']));
		$config['mail']['From'] = stripslashes($_POST['mail']);
		$recipient = mysql_fetch_assoc(mysql_query('SELECT mail FROM users WHERE id = ' . (int) $_POST['recipient'], $site['db']['connection']));
		$to = $config['mail']['tus'] . ', ' . $config['mail']['admin'] . ', ' . $recipient['mail']; 
		
		mail($to, $subject, $mail, mail_headers());
		echo '<div>{lang:contact:success_submission}</div>';
	}
}
else {
	echo '<form accept-charset="utf-8" onsubmit="return check_contact(this)" action="' . $_SERVER['PHP_SELF'] . '?module=contact&amp;command=send" method="post">
		<table>
			<tr><td colspan="2">{lang:contact:description}</td></tr>
			<tr><td class="row_title">{lang:general:recipient}</td><td><select size="1" name="recipient">';
			foreach(get_users('login_count DESC') as $user)
				echo '<option ' . (($_GET['usr_id'] == $user['id']) ? 'selected="selected" ' : '') . 'value="' . $user['id'] . '">' . $user['prename'] . ' ' . $user['lastname'] . '</option>';
			echo '</select></td></tr>
			<tr><td class="row_title">{lang:general:name}</td><td><input type="text" name="name" onblur="check_string(this)" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
			<tr><td class="row_title">{lang:general:mail}</td><td><input type="text" name="mail" onblur="check_mail(this)" /><br /><span style="display: none" class="error">{lang:general:invalid_mail}</span></td></tr>
			<tr><td class="row_title">{lang:general:web}</td><td><input type="text" onblur="check_uri(this, true)" value="http://" name="web" /><br /><span style="display: none" class="error">{lang:general:invalid_uri}</span></td></tr>
			<tr><td class="row_title">{lang:general:subject}</td><td><input type="text" name="subject" onblur="check_string(this)" /><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
			<tr><td class="row_title">{lang:general:message}</td><td><textarea onblur="check_string(this)" name="text"></textarea><br /><span style="display: none" class="error">{lang:general:invalid_string}</span></td></tr>
			<tr><td class="row_title">{lang:general:captcha}</td><td><img id="captcha" alt="Captcha" src="' . $site['path']['web'] . '/include/captcha.php" /><a href="#" onclick="document.getElementById(\'captcha\').src = \'images/loading_small.gif\'; window.setTimeout(function() {document.getElementById(\'captcha\').src = \'include/captcha.php\'},500); return false;">{icon:arrow_rotate_clockwise:{lang:general:reload}}</a><br /><input onblur="check_string(this)" name="captcha" type="text" /><span style="display: none" class="error">{lang:general:invalid_captcha}</span><br />{lang:general:captcha_hint}</td></tr>
			<tr><td><input name="submit" type="submit" value="{lang:general:send}" /></td></tr>
		</table>
		</form>';
}

echo '</div>';
?>