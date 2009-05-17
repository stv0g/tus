<?php
############################################
# TUS Griesheim Handball CMS  (13.5.2007)  #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by B. Sirker, S. Vogel & L. Lauer        #
############################################
# modules/stats.php - The Stats Module     #
############################################

$rights = access($usr_id, 'stats', 0);

if ($rights['show']) {
	// Eingetragene Newsletter
	$result = mysql_query('SELECT COUNT(id) FROM newsletter WHERE active=\'1\'', $connection);
	list($stats['newsletter']) = mysql_fetch_row($result);

	// Gästebucheinträge
	$result = mysql_query('SELECT COUNT(id) FROM gbook', $connection);
	list($stats['gbook']) = mysql_fetch_row($result);


	// Artikel
	$result = mysql_query('SELECT COUNT(id) FROM articles', $connection);
	list($stats['articles']) = mysql_fetch_row($result);

	// User
	$result = mysql_query('SELECT COUNT(id) FROM users', $connection);
	list($stats['users']) = mysql_fetch_row($result);
	
	$result = mysql_query('SELECT COUNT(id) FROM users WHERE active = TRUE', $connection);
	list($stats['users_active']) = mysql_fetch_row($result);

	// Bilder
	$result = mysql_query('SELECT COUNT(id) FROM pictures', $connection);
	list($stats['pictures']) = mysql_fetch_row($result);

	// Kategorien
	$result = mysql_query('SELECT COUNT(id) FROM categories', $connection);
	list($stats['categories']) = mysql_fetch_row($result);

	// Seasons
	$result = mysql_query('SELECT COUNT(DISTINCT season) FROM categories', $connection);
	list($stats['seasons']) = mysql_fetch_row($result);

	mysql_free_result($result);
	
	echo '<div id="stats">
		<table>
			<tr><td class="title" colspan="2">{lang:general:stats}</td></tr>
			<tr><td class="row_title">{lang:stats:newsletters}</td><td>' . $stats['newsletter'] . '</td></tr>
			<tr><td class="row_title">{lang:stats:gbook}</td><td>' . $stats['gbook'] . '</td></tr>
			<tr><td class="row_title">{lang:stats:articles}</td><td>' . $stats['articles'] . '</td></tr>
			<tr><td class="row_title">{lang:stats:users}</td><td>' . $stats['users'] . ' (' . $stats['users_active'] . ' {lang:general:active})</td></tr>
			<tr><td class="row_title">{lang:stats:pictures}</td><td>' . $stats['pictures'] . '</td></tr>
			<tr><td class="row_title">{lang:stats:categories}</td><td>' . $stats['categories'] . '</td></tr>
			<tr><td class="row_title">{lang:stats:seasons}</td><td>' . $stats['seasons'] . '</td></tr>
		</table>
	</div>';
}
?>