<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TuS Griesheim Handball CMS
 *
 * Code file for newsticker module
 *
 * @category	Module
 * @name		newsticker.php
 * @author		Steffen Vogel <info@steffenvogel.de>
 * @copyright	2006-2008 Steffen Vogel
 * @license		http://creativecommons.org/licenses/by-nc-nd/2.0/de/
 * @link		http://tusgriesheim.de/handball/
 * @since		File available since 3.10.2007 
 */

$result = mysql_query('SELECT articles.id AS id, cat_id, title
						FROM articles
						LEFT JOIN categories ON categories.id = articles.cat_id
						WHERE categories.season = ' . $site['season'] . '
						GROUP BY id ORDER BY date DESC LIMIT 10', $site['db']['connection']);
$news = '';

echo '<div id="newsticker">';
if (false) {
	while (($line = mysql_fetch_array($result))) {
		$news .= $line['title'] . ' +++ ';
	}
	
	echo '<object classid="java:zticker.class" codebase="' . $site['path']['web'] . '/modules/newsticker/" codetyte="application/java" width="790" height="25">
		<param name="msg" value="' . $news . '">
		<param name="href" value="index.php">
		<param name="speed" value="5">
		<param name="bgco" value="255,255,255">
		<param name="txtco" value="000,000,000">
		<param name="hrefco" value="255,255,255">
	</object>';
}
elseif (true) {
	while (($line = mysql_fetch_array($result))) {
		$news .= '<a href="' . $site['path']['web'] . '/index.php?module=article&amp;id=' . (int) $line['id'] . '&amp;cat_id=' . $line['cat_id'] . '">' . stripslashes($line['title']) . '</a> +++ ';
	}
	
	echo '<marquee onmouseover="this.stop()" onmouseout="this.start()" direction="left" scrollamount="3" scrolldelay="10" align="middle" height="25">' . $news . '</marquee>';
}

echo '</div>';

?>