<?php
############################################
# TUS Griesheim Handball CMS  (13.5.2007)  #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by B. Sirker, S. Vogel & L. Lauer        #
############################################
# include/include.inc.php - The Includes   #
############################################

/**
 * Send newsletter to subscribed users
 * 
 * Following strings will be replaced in $content:
 * {newsletter:mail} the mailaddress
 * {newsletter:id} the db internal id
 * {newsletter:name} the realname
 * 
 */
function send_newsletter($subject, $content) {
	global $config;
	global $site;
	
	$result = mysql_query('SELECT * FROM newsletter WHERE active = 1', $site['db']['connection']);
	
	while ($subscriber = mysql_fetch_assoc($result)) {
		$individual_content = str_replace('{newsletter:mail}', $subscriber['mail'], $content);
		$individual_content = str_replace('{newsletter:id}', $subscriber['id'], $individual_content);
		$individual_content = str_replace('{newsletter:name}', $subscriber['name'], $individual_content);

		$html = '<html>
						<body>' .
							$individual_content .
						'</body>
				</html>';
			
		mail($subscriber['mail'], mb_encode_mimeheader($subject, 'UTF-8', 'Q', "\n"), $html, mail_headers());
		echo $subscriber['name'] . ' ' . $subscriber['mail'] . '<br />';
	}
	return mysql_num_rows($result);
}

function newsletter_token($user) {
	global $config;
	
	return md5($config['security']['salt'] . md5($user['id'] . $user['mail'] . $user['ip'] . $user['name']));
}

function get_cats() {
	global $site;
	
	$result = mysql_query('SELECT * FROM categories ORDER BY id ASC', $site['db']['connection']);
	while ($line = mysql_fetch_assoc($result))
		$categegories[$i++] = $line;
		
	return $categegories;
}

function get_users($order = 'name ASC') {
	global $site;
	
	$result = mysql_query('SELECT * FROM users ORDER BY ' . $order, $site['db']['connection']);
	while ($line = mysql_fetch_assoc($result))
		$users[$i++] = $line;
		
	return $users;
}

function encode_mail_subject($header) {
	return '=?utf-8?b?' . base64_encode($header) - '=?=';
}

function get_seasons() {
	global $site;
	
	$result = mysql_query('SELECT DISTINCT season FROM categories ORDER BY season DESC', $site['db']['connection']);
	while ($line = mysql_fetch_assoc($result))
		$seasons[$i++] = $line['season'];
		
	return $seasons;
}

function access($usr_id, $module, $cat_id) {
	global $connection;
	
	$result = mysql_query('SELECT MAX(`add`) AS `add`, MAX(`del`) AS del, MAX(`edit`) AS edit, MAX(`show`) AS `show` FROM access WHERE (usr_id = \'' . $usr_id . '\' OR usr_id = 0) AND (cat_id = \'' . $cat_id . '\' OR cat_id = 0) AND (module = \'' . $module . '\' OR module = \'\')', $connection);

	if ($line = mysql_fetch_array($result)) {
		$return = $line;	
	}
	else $return = 0;
	
	return $return;
	
	mysql_free_result($result);
}

function mail_headers() {
	global $config;
	$header = '';
	
	ksort($config['mail']['header']);
	foreach ($config['mail']['header'] as $key => $value) {
		$header .= $key . ': ' . $value . "\n";
	}
	$header .= 'Message-Id: <' . uniqid() . '@griesm.de>';
	
	return $header;	
}

function html_headers() {
	global $head;
	global $site;
	global $config;
	
	$head_tmp = '';
	ksort($head);
	foreach ($head as $line) {
		$head_tmp .= $line . "\n";
	}
		
	return $head_tmp;	
}

function no_rights() {
	global $site;
	
	echo '<div>
			<img src="' . $site['path']['web'] . '/images/no_rights.png" alt="{lang:general:no_rights}" />
		</div>
		<div class="error">
			{lang:general:no_rights}
		</div>';
	redirect($site['path']['web'] . '/index.php?module=access');
	trigger_error('No rights!', E_USER_ERROR);
}

function error_handler($errno, $errmsg, $filename, $linenum, $vars) {
	global $config;
	
	$dt = date('Y-m-d H:i:s');
	$errortype = array (
					E_ERROR					=> 'Error',
					E_WARNING				=> 'Warning',
					E_PARSE					=> 'Parsing Error',
					E_NOTICE				=> 'Notice',
					E_CORE_ERROR			=> 'Core Error',
					E_CORE_WARNING 			=> 'Core Warning',
					E_COMPILE_ERROR			=> 'Compile Error',
					E_COMPILE_WARNING		=> 'Compile Warning',
					E_USER_ERROR			=> 'User Error',
					E_USER_WARNING			=> 'User Warning',
					E_USER_NOTICE			=> 'User Notice',
					E_STRICT				=> 'Runtime Notice',
					E_RECOVERABLE_ERROR		=> 'Catchable Fatal Error',
					E_DEPRECATED			=> 'Deprecated',
					E_USER_DEPRECATED		=> 'User Deprecated'
				);
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
	
	$err = '[' . $dt . '][' . $errortype[$errno] . '][' . $_SERVER['REMOTE_ADDR'] . '] ' . $errmsg . ' in ' . $filename . ' on line ' .  $linenum . "\n";
	error_log($err, 3, '/var/log/php/error_tus.log');
	
	if ($errno == E_ERROR || $errno == E_USER_ERROR || $errno == E_WARNING || $errno == E_USER_WARNING) {
	
		ob_start();
		print_r($vars);
		$vartrace = '<pre>' . htmlspecialchars(ob_get_clean()) . '</pre>';
		ob_start();
		debug_print_backtrace();
		$backtrace = '<pre>' . htmlspecialchars(ob_get_clean()) . '</pre>';
		
		ob_start();
		echo '<html>
				<body>
					<table>
						<tr><td>' . lang('general', 'date') . '</td><td>' . $dt . '</td></tr>
						<tr><td>' . lang('general', 'error_type') . '</td><td>' . $errortype[$errno] . '</td></tr>
						<tr><td>' . lang('general', 'error_message') . '</td><td>' . $errmsg . '</td></tr>
						<tr><td>' . lang('general', 'file') . '</td><td>' . $filename . '</td></tr>
						<tr><td>' . lang('general', 'line') . '</td><td>' . $linenum . '</td></tr>';
						foreach ($_SERVER as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_SERVER[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_GET as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_GET[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_POST as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_POST[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_COOKIE as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_COOKIE[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_SESSION as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_SESSION[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
				echo '<tr><td class="row_title">$site[\'module\']</td><td>' . $site['module'] . '</td></tr>
						<tr><td class="row_title">$site[\'usr\'][\'name\']</td><td>' . $site['usr']['name'] . '</td></tr>
						<tr><td class="row_title">$site[\'usr\'][\'prename\']</td><td>' . $site['usr']['prename'] . '</td></tr>
						<tr><td class="row_title">$site[\'usr\'][\'lastname\']</td><td>' . $site['usr']['lastname'] . '</td></tr>
						<tr><td class="row_title">$site[\'usr\'][\'id\']</td><td>' . $site['usr']['id'] . '</td></tr>
						<tr><td class="row_title">$site[\'usr\'][\'mail\']</td><td>' . $site['usr']['mail'] . '</td></tr>
						<tr><td class="row_title">$site[\'season\']</td><td>' . $site['season'] . '</td></tr>
						<tr><td class="row_title">$site[\'cat\'][\'id\']</td><td>' . $site['cat']['id'] . '</td></tr>
						<tr><td class="row_title">$site[\'cat\'][\'name\']</td><td>' . $site['cat']['name'] . '</td></tr>
						<tr><td class="row_title">$site[\'command\']</td><td>' . $site['command'] . '</td></tr>
						<tr><td class="row_title">$site[\'id\']</td><td>' . $site['id'] . '</td></tr>
						<tr><td class="row_title">$site[\'redirect\']</td><td>'; foreach ($site['redirect'] as $var => $value) echo '[' . $var . '] ' . htmlspecialchars($value) . '<br />'; echo '</td></tr>
						<tr><td class="row_title">$site[\'path\']</td><td>' . $site['path']['server'] . '</td></tr>
						<tr><td class="row_title">$site[\'webpath\']</td><td>' . $site['path']['web'] . '</td></tr>
						<tr><td class="row_title">Reponse headers</td><td>'; foreach (apache_response_headers() as $var => $value) echo '[' . $var . '] ' . $value . '<br />'; echo '</td></tr>
						<tr><td class="row_title">Request headers</td><td>'; foreach (apache_request_headers() as $var => $value) echo '[' . $var . '] ' . $value . '<br />'; echo '</td></tr>
						<tr><td>' . lang('general', 'error_vartrace') . '</td><td>' . $vartrace . '</td></tr>
						<tr><td>' . lang('general', 'error_backtrace') . '</td><td>' . $backtrace . '</td></tr>
					</table>
				</body>
				</html>';
		$mail = ob_get_clean();
	
		mail($config['mail']['admin'], '[' . $config['site']['name'] . '] ' . $errortype[$errno] . ': ' . $errmsg, $mail, mail_headers());
	}
}
@set_error_handler('error_handler');

function redirect($uri, $timeout = false) {
	global $head;
	global $config;
	
	if(strlen($uri) < 2)
		$uri = $site['path']['web'] . '/index.php';
	
	if ($timeout === false)
		$timeout = $config['site']['redirect_timeout'];
		
	$head['redirect'] = '<meta http-equiv="refresh" content="' . (int) $timeout . '; URL=' . htmlspecialchars($uri) . '" />';
}

function check_mail($mail) {
	if (ereg('^([a-zA-Z0-9]((\.|\-|\_)?[a-zA-Z0-9])*)@([a-zA-Z]((\.|\-)?[a-zA-Z0-9])*)\.([a-zA-Z]{2,8})$', $mail) || empty($mail))
		return true;
	else
		return false;
}

function check_uri($uri) {
	if (ereg('^(http(s)?://)?(www\.)?([a-zA-Z]((\.|\-)?[a-zA-Z0-9])*)\.([a-zA-Z]{2,8})[a-zA-Z0-9|_|-|+|.|/]*$', $uri) or empty($uri) or $uri == 'http://') return true;
	else false;
}

function module($load) {
	//TODO Compability
	global $connection;
	global $season;
	global $usr_id;
	global $usr_name;
	global $usr_mail;
	global $usr_prename;
	global $usr_lastname;
	global $cat_id;
	global $cat_name;
	global $cat_type;
	global $id;
	global $command;
	global $redirect;
	global $main_module;
	
	global $site;
	global $lang;
	global $head;
	global $config;
		
	if (is_string($load)) $module = $load;
	elseif (is_array($load)) $module = $load[1];
	
	if (file_exists($site['path']['server'] . '/include/template/' . $config['site']['template'] . '/css/' . $module . '.css'))
		$head['css_' . $module] = '<link rel="stylesheet" type="text/css" href="' . $site['path']['web'] . '/include/template/' . $config['site']['template'] . '/css/' . $module . '.css" />';
	
	if (file_exists($site['path']['server'] . '/include/javascript/' . $module . '.js'))
		$head['script_' . $module] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/' . $module . '.js"></script>';
		
	if (file_exists($site['path']['server'] . '/modules/' . $module . '/' . $module . '.php')) {
		ob_start();
		include $site['path']['server'] . '/modules/' . $module . '/' . $module . '.php';
		$return = ob_get_clean();
	}
	else {
		$return = '<div class="error">{lang:general:error_module_not_found}: ' . htmlentities($module) . '</div>';
		$head['title'] = '<title>{lang:general:error_module_not_found}: ' . htmlentities($module) . '</title>';
		trigger_error('Module not found: ' . $module, E_USER_ERROR);
	}
	
	return $return;
}

function lang($par1, $par2 = null) {
	global $lang;
	global $config;
	global $site;
	
	if (is_string($par1)) {
		$module = $par1;
		$word = $par2;
	}
	elseif (is_array($par1)) {
		$module = $par1[1];
		$word = $par1[2];
	}
	
	if (!isset($lang[$module])) {
		$file = $site['path']['server'] . '/include/lang/' . $config['site']['language'] . '/' . $module . '.php';
		if (file_exists($file))
			require($file);
		else {
			trigger_error('File not found: /include/lang/' . $config['site']['language'] . '/' . $module . '.php', E_USER_ERROR);
			return lang('general', 'error_file_not_found') . ': ' . $file;
		}
	}
	
	if ($lang[$module][$word])
		return $lang[$module][$word];
	else {
		$return = '{lang:' . $module . ':' . $word . '}';
		trigger_error('Undefined lang variable: ' . $return, E_USER_WARNING);
		return $return;
	}
}

function icon($par1, $par2 = null) {
	global $config;
	global $site;
	
	if (is_string($par1)) {
		$icon = $par1;
		$tooltip = $par2;
	}
	elseif (is_array($par1)) {
		$icon = $par1[1];
		$tooltip = $par1[2];
	}
	
	if (file_exists($site['path']['server'] . '/images/icons/' . $icon . '.png'))
		return '<img title="' . $tooltip . '" alt="' . $tooltip . '" class="icon" src="images/icons/' . $icon . '.png" />';
	else {
		trigger_error('Missing icon: /images/icons/' . $icon . '.png', E_USER_ERROR);
		return '{icon:' . $icon . ':' . $tooltip . '}';
	}
}

function upload_image($file, $title, $description, $cat_id, $usr_id, $gal_id = 0) {
	global $config;
	global $season;
	global $site;
	
	$md5 = md5_file($file['tmp_name']);
	$sql = 'SELECT
				categories.season AS season,
				categories.name AS cat_name,
				categories.id AS cat_id,
				picture_categories.path AS gal_path,
				pictures.full AS full,
				pictures.thumb AS thumb
			FROM pictures
				LEFT JOIN picture_categories ON pictures.gal_id = picture_categories.id
				LEFT JOIN categories ON picture_categories.cat_id = categories.id
			WHERE
				pictures.md5 = \'' . $md5 . '\'
			LIMIT 1';
	$result = mysql_query($sql, $site['db']['connection']);
	$line = mysql_fetch_assoc($result);
	
	if (mysql_num_rows($result) > 0 && file_exists($site['path']['server'] . '/images/gallery/' . $line['season'] . '/' . str_replace(' ', '_', strtolower($line['cat_name'])) . '/' . $line['gal_path'] . '/' . $line['full'])) {
		return $line['id'];
	}
	else {
		$size = getimagesize($file['tmp_name']);
		$img = imagecreatefromstring(file_get_contents($file['tmp_name']));
		
		if (is_uploaded_file($file['tmp_name'])) {
			if (empty($gal_id)) {
				$result = mysql_query('SELECT MIN(id) AS gal_id FROM picture_categories WHERE cat_id = ' . $cat_id, $site['db']['connection']);
				$line = mysql_fetch_array($result);
				if (empty($line['gal_id'])) {
					mysql_query('INSERT INTO picture_categories (cat_id, name, description, path, ip, date) VALUES(' . (int) $cat_id . ', \'Sonstige Bilder\', \'Hier liegen die Bilder, die in den Berichten eingefügt wurden\', \'misc\', \'' . $_SERVER['REMOTE_ADDR'] . '\', NOW())', $site['db']['connection']);
					$gal_id = mysql_insert_id();
				}
				else $gal_id = $line['gal_id'];
			}
			
			$result = mysql_query('SELECT
										categories.name AS cat_name,
										categories.season AS season,
										picture_categories.path AS gal_path
									FROM categories
									LEFT JOIN picture_categories ON picture_categories.cat_id = categories.id
									WHERE categories.id = ' . $cat_id . ' AND picture_categories.id = ' . $gal_id, $site['db']['connection']);
			$line = mysql_fetch_array($result);

			$path = '/images/gallery/' . $line['season'] . '/' . str_replace(' ', '_', strtolower($line['cat_name'])) . '/' . $line['gal_path'] . '/';
			if (!file_exists($site['path']['server'] . $path))
				mkdir($site['path']['server'] . $path, 0777, true);

			if (strrpos($file['name'], '.') !== false)
				$filename = substr($file['name'], 0, strrpos($file['name'], '.'));
			else
				$filename = $file['name'];
				
			$filename = str_replace(' ', '_', strtolower($filename));

			$newfilename = $filename;

			while (file_exists($site['path']['server'] . $path . 'full_' . $newfilename . '.jpg')) {
				$c++;
				$newfilename = $filename . '_' . $c;
			}
			
			$newfilename .= '.jpg';
			
			$width = $size[0];
			$height = $size[1];

			if ($width > $config['gallery']['max_width']) {
				$newWidth = $config['gallery']['max_width'];
				$newHeight = intval($height * ($newWidth / $width));
				if ($newHeight > $config['gallery']['max_height']) {
					$newHeight = $config['gallery']['max_height'];
					$newWidth = intval($width * ($newHeight / $height));
				}
				$resize = true;
			}
			elseif ($height > $config['gallery']['max_height']) {
				$newHeight = $config['gallery']['max_height'];
				$newWidth = intval($width * ($newHeight / $height));
				$resize = true;
			}
			
			if ($resize == true) {
				$resized_img =  ImageCreateTrueColor ($newWidth , $newHeight);
				imagecopyresampled($resized_img, $img, 0,0,0,0, $newWidth, $newHeight, $width, $height);
				ImageJPEG($resized_img, $site['path']['server'] . $path . 'thumb_' . $newfilename, $config['gallery']['quality']);
				ImageDestroy($resized_img);
			}
			
			ImageJPEG($img, $site['path']['server'] . $path . 'full_' . $newfilename, $config['gallery']['quality']);
			ImageDestroy($img);
	
			mysql_query('INSERT INTO pictures (gal_id, title, description, ip, editor_id, full, thumb, md5, date) VALUES(' . (int) $gal_id . ', \'' . mysql_real_escape_string($title) . '\', \'' . mysql_real_escape_string($description) . '\', \'' . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . '\', ' . (int) $usr_id . ', \'' . mysql_real_escape_string('full_' . $newfilename) . '\', \'' . (($resize == true) ? mysql_real_escape_string('thumb_' . $newfilename) : '') . '\', \'' . mysql_real_escape_string($md5) . '\', NOW())', $site['db']['connection']);
	
			return mysql_insert_id();
		}
		else {
			return false;
		}
	}
}

if (function_exists('mysql_set_charset') === false) {
	function mysql_set_charset($charset, $link_identifier = null) {
		if ($link_identifier == null) {
			return mysql_query('SET CHARACTER SET "'.$charset.'"');
		} else {
			return mysql_query('SET CHARACTER SET "'.$charset.'"', $link_identifier);
		}
	}
}

if ( !function_exists('image_type_to_extension') ) {
	function image_type_to_extension ($type, $dot = true) {
		$e = array ( 1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc','aiff', 'wbmp', 'xbm');

		$type = (int) $type;
		if (!$type) {
			trigger_error( 'Invalid argument for image_type_to_extension()', E_USER_NOTICE );
			return null;
		}

		if ( !isset($e[$type]) ) {
			trigger_error( 'Invalid argument for image_type_to_extension()', E_USER_NOTICE );
			return null;
		}

		return ($dot ? '.' : '') . $e[$type];
	}
}

function hl($text, $words) {
	if(is_array($words)) {
		$colors = array('#ff9999', '#ffff99', '#ff99ff', '#99ffff','#99ff99', '#9999ff');

		foreach ($words as $word) {
			if(!empty($word)) {
				if ($i++ >= count($colors))
					$i = 0;
			$text = preg_replace('/(' . preg_quote($word) . ')(?![^<]+>)/i', '<span style="background-color: ' . $colors[$i] . ';">${1}</span>', $text);
			}
		}
	}
	return $text;
}

function get_search_words() {
	$referer = urldecode($_SERVER['HTTP_REFERER']);

	if(preg_match('/www\.google.*/i',$referer)
		|| preg_match('/search\.msn.*/i',$referer)
		|| preg_match('/search\.yahoo.*/i',$referer)
		|| preg_match('/search\.lycos\.com/i', $referer)
		|| preg_match('/search\.aol\.com/i', $referer)
		|| preg_match('/ask\.com/i', $referer)
		|| preg_match('/search\.netscape\.com/i', $referer)) {

		if(preg_match('/(www\.google.*)|(search\.msn.*)|(ask\.com)/i',$referer))
			 $delimiter = "q";
		elseif(preg_match('/search\.yahoo.*/i',$referer))
			 $delimiter = "p";
		elseif(preg_match('/(search\.lycos\.com)|(search\.aol\.com)|(search\.netscape\.com)/i', $referer))
			 $delimiter = "query";

		$pattern = "/^.*" . $delimiter . "=([^&]+)&?.*\$/i";
		$query_terms = preg_replace($pattern, '$1', $referer);
	}
	elseif (!empty($_GET['hl'])) {
		$query_terms = rawurldecode($_GET['hl']);
	}
	else
		return false;
	
	$search_array = escape_search_words($query_terms);
	return $search_array;
}

function escape_search_words($query_terms) {
	$query_ops = array('+', '-', '<', '>', '~', '*', '"', '(', ')');
	
	$query_terms = trim( $query_terms);
	$query_terms = preg_replace('/[(' . preg_quote(implode($query_ops)) . ')]/', '', $query_terms);
	$query_array = preg_split ("/[\s,\+\.]+/", $query_terms);
	
	return $query_array;
}

function include_editor($editor) {
	global $site;
	global $head;
	
	if ($editor == 'tiny_mce') {
		$head['js_tinymce_gzip'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/tiny_mce/tiny_mce_gzip.js"></script>';
		$head['js_tinymce_gzip_config'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/tiny_mce/settings_gzip.js"></script>';
		$head['js_tinymce_settings'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/tiny_mce/settings.js"></script>';
	}
	elseif ($editor == 'fckeditor') {
		$head['js_fck'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/fckeditor/fckeditor.js"></script>';
	}
}

//TODO complete word splitting
function subwords($string, $start, $length) {	
	if ($start < 0)
		$start = 0;
		
	if ($length > strlen($string))
		$length = strlen($string);
	
	while ($length < strlen($string) && $string[$length] != " ")
		$length++;
	
	while ($start > 0 && $string[$start] != " ")
		$start--;
		
	return substr($string, $start, $length);
}

?>