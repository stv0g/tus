<?php 

if ($config['site']['announcement']) {
echo '<div id="announcement">
		<img id="announcement_logo" src="' . $site['path']['web'] . '/images/announcement.gif" alt="{lang:general:announcement}" />
		<p style="text-align: center">
			Wir suchen zur Unterstützung unserer kommenden A-Jugend ambitionierte<br />
			Spieler (Feldspieler und einen Torhüter) des Jahrgangs 91/92, die in der<br />
			Saison 2009/2010 mindestens die Oberliga erreichen wollen.<br />
		</p>
		<p style="text-align: center">
			Bei Interesse wendet Euch bitte an den Trainer: <a href="' . $site['path']['web'] . '/index.php?module=contact&usr_id=12">Uwe Rinschen</a>, 0171 7831302
		</p>
	</div>';
}

?>