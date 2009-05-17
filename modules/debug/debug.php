<?php
############################################
# TUS Griesheim Handball CMS  (9.6.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)        #
############################################
# modules/debug.php - The Debug Module     #
############################################

$rights = access($usr_id, 'debug', 0);

if ($rights['show'] || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	echo '<div id="debug">
			<div class="clip"><div class="clip_control"><img class="clip_img" alt="{lang:general:debug}" src="images/plus.gif"/><a href="#" onclick="clip(this); return false;">{lang:general:debug}</a></div><div class="clip_span" style="display: none;">
					<table>
						<tr><td class="column_title">{lang:debug:var}</td><td class="column_title">{lang:debug:value}</td></tr>';
						foreach ($_SERVER as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_SERVER[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_GET as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_GET[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_POST as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_POST[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_COOKIE as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_COOKIE[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						foreach ($_SESSION as $var => $value) if (!is_array($value)) echo '<tr><td class="row_title">$_SESSION[\'' . $var . '\']</td><td>' . htmlspecialchars($value) . '</td></tr>';
						echo '<tr><td class="row_title">{lang:debug:time}</td><td>{time} ms</td></tr>
						<tr><td class="row_title">$site[\'module\']</td><td>' . $site['module'] . '</td></tr>
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
					</table>
				</div></div>
		</div>';
}
