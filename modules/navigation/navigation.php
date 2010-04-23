<?php
##################################################
# TUS Griesheim Handball CMS  (5.5.2007)         #
# Web: http://www.tusgriesheim.de/handball       #
# Mail: handball@tusgriesheim.de                 #
# by B. Sirker, S. Vogel & L. Lauer              #
##################################################
# modules/navigation.php - The Navigation Module #
##################################################

$result_cat = mysql_query('SELECT * FROM categories WHERE season = ' . (int) $site['season'] , $connection);

echo '<div id="navigation">
		<ul>';
while($line_cat = mysql_fetch_array($result_cat)) {
	if ($site['cat']['id'] == $line_cat['id']) {
		echo '<li><a' . ((empty($_GET['nav_id']) && $site['module'] == 'article') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?cat_id=' . $line_cat['id'] . '">{icon:group:' . htmlspecialchars($line_cat['name']) . '}&nbsp;' . htmlspecialchars($line_cat['name']) . '</a><ul>';
		$result_nav = mysql_query('SELECT * FROM navigation WHERE cat_id = ' . $site['cat']['id'], $site['db']['connection']);
		while ($line_nav = mysql_fetch_array($result_nav)) {
			echo '<li><a' . (((int) $_GET['nav_id'] == $line_nav['id']) ? ' id="navigation_active"' : '') . ' href="' . htmlentities($line_nav['uri']) . ((strpos($line_nav['uri'], 'index.php') === 0) ? '&nav_id=' . $line_nav['id'] : '') . '">{icon:bullet_black:' . $line_nav['title'] . '}&nbsp;' . $line_nav['title'] . '</a></li>';
		}
		mysql_free_result($result_nav);
		echo '</ul></li>';
	}
	else
		echo '<li><a href="' . $site['path']['web'] . '/index.php?cat_id=' . $line_cat['id'] . '">{icon:group:' . htmlspecialchars($line_cat['name']) . '} ' . htmlspecialchars($line_cat['name']) . '</a></li>';
}
echo '</ul>';
mysql_free_result($result_cat);

// Admin Menü

echo '<ul>';

$rights_stats = access($usr_id, 'stats', 0);

if ($rights_stats['show']) echo '<li><a' . (($site['module'] == 'stats') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=stats">{icon:chart_curve:{lang:general:stats}}&nbsp;{lang:general:stats}</a></li>';

if (isset($site['usr']['id'])) {
	echo '<li><a' . (($site['module'] == 'access' && $site['command'] == 'logout') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=access&amp;command=logout">{icon:door_in:{lang:access:logout}}&nbsp;{lang:access:logout}<br />(' . $site['usr']['prename'] . ' ' . $site['usr']['lastname'] . ')</a></li>';
	echo '<li><a' . (($site['module'] == 'access' && $site['command'] == 'pw') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=access&amp;command=pw">{icon:script_edit:{lang:access:change_pw}}&nbsp;{lang:access:change_pw}</a></li>';
}
else
	echo '<li><a' . (($site['module'] == 'access') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=access">{icon:key:{lang:general:access}}&nbsp;{lang:access:login}</a></li>';

echo '</ul><ul>';
echo '<li><a' . (($site['module'] == 'search') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=search">{icon:zoom:{lang:general:search}} {lang:general:search}</a></li>';
echo '<li><a' . (($site['module'] == 'newsletter') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=newsletter">{icon:email_add:{lang:general:newsletter}} {lang:general:newsletter}</a></li>';
echo '<li><a' . (($site['module'] == 'gbook') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=gbook">{icon:book_open:{lang:general:gbook}} {lang:general:gbook}</a></li>';
echo '<li><a' . (($site['module'] == 'contact') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=contact">{icon:email_edit:{lang:general:contact}} {lang:general:contact}</a></li>';
echo '<li><a' . (($site['module'] == 'links') ? ' id="navigation_active"' : '') . ' href="' . $site['path']['web'] . '/index.php?module=links">{icon:link:{lang:general:links}} {lang:general:links}</a></li>';
echo '<li><a href="' . $site['path']['web'] . '/modules/article/newsfeed.php">{icon:feed:{lang:general:newsfeed}} {lang:general:newsfeed}</a></li>';
echo '<li><a href="' . $site['path']['web'] . '/index.php?module=frame&url=' . urlencode('http://www.forum.griesm.de/viewforum.php') . '">{icon:group:{lang:general:forum}} {lang:general:forum}</a></li>';
echo '</ul></div>';

?>
