<?php

$site['path']['server'] = dirname(dirname(dirname(__FILE__)));
include $site['path']['server'] . '/include/init.inc.php';

header('Content-type: text/calendar; charset=utf-8');

$sis = new sis($site['cat']['id'], 1, 0, false);

echo 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php
METHOD:PUBLISH
';
foreach ($sis->get_xml()->Spiel as $match) {
	$home = (strpos($match->Heim, $config['sis']['name'])  !== false ) ? true : false;
	$guest = (strpos($match->Gast, $config['sis']['name'])  !== false) ? true : false;

	if ($home == true || $guest == true) {
		echo 'BEGIN:VEVENT
UID:' . uniqid() . '@' . $_SERVER['HTTP_HOST'] . '
URL:http://www.sis-handball.de/web/Default.aspx?view=Spiel&Liga=' . (string) $match->Liga . '
SUMMARY:' . $match->Heim . ' - ' . $match->Gast . '
DESCRIPTION:' . $match->Anmerkung . '
CLASS:PUBLIC
CATEGORIES: Handball
LOCATION:' . $match->HallenStrasse . ', ' . $match->HallenOrt . '
DTSTART:' . date('Ymd\THis', strtotime($match->SpielVon)) . '
DTEND:' . date('Ymd\THis', strtotime($match->SpielBis)) . '
LAST-MODIFIED:' . date('Ymd\THis', strtotime($match->Aktualisierungsdatum)) . '
DTSTAMP:20060812T125900Z
END:VEVENT
';
	}
}
echo 'END:VCALENDAR';

?>