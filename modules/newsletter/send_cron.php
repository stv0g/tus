<?php
#############################################
# TUS Griesheim Handball CMS  (13.5.2007)   #
# Web: http://www.tusgriesheim.de/handball  #
# Mail: handball@tusgriesheim.de            #
# by Steffen Vogel (info@steffenvogel.de    #
#############################################
# send_cron.php - cronological newsletter   #
#############################################

$site['path']['server'] = dirname(dirname(dirname(__FILE__)));
$site['path']['web'] = '/workspace/TuS Handball/trunk';
$site['hostname'] = 'localhost';


require_once $site['path']['server'] . '/include/config.inc.php';
require_once $site['path']['server'] . '/include/functions.inc.php';
require_once $site['path']['server'] . '/include/db.inc.php';
require_once $site['path']['server'] . '/include/classes/sis.php';
require_once $site['path']['server'] . '/include/locale.inc.php';

echo "--- Starting sending weekly newsletter\n";

# Subject
$subject =  '[' . $config['site']['name'] . '] #' . date('Y') . '/' . date('W');

# Newsletter artice available?
$result = mysql_query('SELECT articles.id AS id,
							categories.id AS cat_id,
							articles.text AS text,
							articles.title AS title
						FROM articles
						LEFT JOIN categories ON articles.cat_id = categories.id
						WHERE
							categories.type = 5
							AND categories.season = ' . (int) $config['site']['season'] . '
							AND DATE_SUB(CURDATE(), INTERVAL 1 WEEK) <= articles.date
							AND CURDATE() >= articles.date
						ORDER BY date DESC
						LIMIT 1;');

if (mysql_num_rows($result) > 0) {
	$article = mysql_fetch_assoc($result);
	$intro .= '<h2><a href="http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?id=' . $article['id'] . '">' . $article['title'] . '</a></h2>' . $article['text'] . '<hr>';
}

# Get matches for next week from SiS
$result = mysql_query('SELECT id AS cat_id, name AS cat_name FROM categories WHERE sis_liga != \'\' AND season = ' . $config['site']['season'] . ' ORDER BY id ASC');
while ($line = mysql_fetch_array($result)) {
	$sis = new sis($line['cat_id'], 1);
	$delta = 60 * 60 * 24 * 7 * 1; // a week
	foreach ($sis->get_xml()->Spiel as $match) {
		$home = (strpos($match->Heim, $config['sis']['name'])  !== false ) ? true : false;
		$guest = (strpos($match->Gast, $config['sis']['name'])  !== false) ? true : false;
		if (strtotime($match->SpielVon) - time() < $delta && strtotime($match->SpielVon) -time() > 0 && ($home || $guest)) {
			$matches .= '<tr>
						<td>' . date('d.m.y G:i', strtotime($match->SpielVon)) . '</td>
						<td><a target="_blank" href="http://www.sis-handball.de/web/Default.aspx?view=Mannschaft&Liga=' . $match->Mannschaft1 . '">' . (($home) ? $line['cat_name'] : $match->Heim) . '</a></td>
						<td>:</td>
						<td><a target="_blank" href="http://www.sis-handball.de/web/Default.aspx?view=Mannschaft&Liga=' . $match->Mannschaft2 . '">' . (($guest) ? $line['cat_name'] : $match->Gast) . '</a></td>
					</tr>';
		}
	}
}
if ($matches > '')
	$matches = '<h4>Die Spiele der nächsten Woche:</h4><table>'. $matches . '</table><hr>';

# Fetch articles from last week
$result = mysql_query('SELECT articles.id AS id,
							categories.id AS cat_id,
							articles.text AS text,
							articles.title AS title,
							categories.name AS cat_name
						FROM articles
						LEFT JOIN categories ON articles.cat_id = categories.id
						WHERE
							categories.type != 5
							AND categories.season = ' . (int) $config['site']['season'] . '
							AND DATE_SUB(CURDATE(), INTERVAL 1 WEEK) <= articles.date
							AND CURDATE() >= articles.date
						ORDER BY date DESC');

if (mysql_num_rows($result) > 0) {
	$articles .= '<h4>Die Spielberichte der vergangenen Woche:</h4>';
	while ($article = mysql_fetch_assoc($result))
		$articles .= '<h3>[<a href="http://' . $site['hostname'] . $site['path']['web'] . '/index.php?cat_id=' . $article['cat_id'] . '">' . $article['cat_name'] . '</a>] <a href="http://' . $site['hostname'] . $site['path']['web'] . '/index.php?id=' . $article['id'] . '">' . $article['title'] . '</a></h3>' . $article['text'] . '<hr>';
}

# default intro
$def_intro .= '<h2>' . $subject . '</h2>Dies ist der wöchentliche Newsletter der TuS Handball Abteilung. Wir berichten ihnen ihr von unseren Spielen der letzten Woche und den anstehenden Begegnungen.<hr>';


# Footer with link to unsubscribe from newsletter
$checkout .= 'Um sich aus diesem wöchtenlichen Newsletter auszutragen klicken Sie bitte <a href="http://' . $site['hostname'] . $site['path']['web'] . '/index.php?' . htmlentities('module=newsletter&remove=true&mail={newsletter:mail}') . '" alt="austragen">hier</a>!';

$html = '<html><body>' . (($intro > '') ? $intro : $def_intro) . $matches . $articles . $checkout . '</body></html>';

if ($articles || $matches || $intro) {
	$mails = send_newsletter($subject, $html);
	echo '--- Newsletter sent to ' . (string) $mail . 'subscribers';
}
else {
	echo '--- Nothing to send!';
}

echo $html;


echo "--- Sending weekly newsletter finished!";

?>