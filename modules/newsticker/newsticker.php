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



while (($line = mysql_fetch_array($result))) {
	$news[] = '<a href="' . $site['path']['web'] . '/index.php?module=article&amp;id=' . (int) $line['id'] . '&amp;cat_id=' . $line['cat_id'] . '">' . stripslashes($line['title']) . '</a>';
}
	
echo '<marquee onmouseover="this.stop()" onmouseout="this.start()" direction="left" scrollamount="3" scrolldelay="10" align="middle" height="25">' . implode(' +++ ', $news) . '</marquee>';


echo '</div>';

?>
