<?php
############################################
# TUS Griesheim Handball CMS  (14.12.2007) #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by B. Sirker, S. Vogel & L. Lauer        #
############################################
# modules/sis.php - The SIS XML Module     #
############################################

$rights = access($usr_id, 'sis', $cat_id);

echo '<div id="sis">';

$result = mysql_query('SELECT sis_liga FROM categories WHERE id = ' . $site['cat']['id'] . ' LIMIT 1', $connection);
$line = mysql_fetch_assoc($result);
$sis_liga = $line['sis_liga'];

if ($command == 'edit' && $rights['edit']) {
	if ($_POST) {
		redirect($site['path']['web'] . '/index.php?module=sis&cat_id=' . $cat_id . '&type=1');
			
		mysql_query('UPDATE categories SET sis_liga = \'' . mysql_real_escape_string($_POST['sis_liga']) . '\' WHERE id = ' . $site['cat']['id'], $connection);
		mysql_query('DELETE FROM sis_cache WHERE cat_id = ' . $site['cat']['id'], $site['db']['connection']);

		echo '<div id="sis">
				<div class="title">{lang:general:sis}</div>
				{lang:sis:success_edit}
			</div>';
	}
	else {
		echo '<form action="' . $site['path']['web'] . '/index.php?module=sis&amp;command=edit&amp;cat_id=' . $site['cat']['id'] . '" method="post">
			<table>
				<tr><td class="title">{lang:sis:liga_id}</td></tr>
				<tr><td colspan="2">{lang:sis:liga_id_description}</td></tr>
				<tr><td>{lang:sis:liga_id} für ' . $site['cat']['name'] . '</td><td><input type="text" id="sis_liga" name="sis_liga" value="' . $sis_liga . '" /> <input type="button" onclick="popup(\'' . $site['path']['web'] . '/modules/sis/select_liga.php\', \'Liga auswählen\', 560, 380);" value="{lang:sis:select_liga}" /></td></tr>
				<tr><td colspan="2"><input type="submit" value="{lang:general:edit}" /></td></tr>
			</table>
		</form>';
		mysql_free_result($result);
	}
}
elseif ($rights['show'] && !empty($_GET['cat_id'])) {	
	if (!empty($sis_liga)) {
		
		$sis = new sis($site['cat']['id'], ($_GET['type'] == 'ranking') ? 4 : 1);
		$xml = $sis->get_xml();
		
		if (!empty($xml)) {
			if ($_GET['type'] == 'ranking') {
				echo '<table class="sis_ranking">
					<tr><td class="sis_title" colspan="12">{lang:sis:table}</td></tr>
					<tr>
						<td class="column_title">{lang:sis:rank}</td>
						<td class="column_title">{lang:sis:club}</td>
						<td class="column_title" colspan="3">{lang:sis:games}</td>
						<td class="column_title" colspan="3">{lang:sis:goals}</td>
						<td class="column_title">{lang:sis:difference}</td>
						<td class="column_title" colspan="3">{lang:sis:points}</td>
					</tr>';

				foreach ($xml->Platzierung as $rank) {
					echo '<tr' . ((strpos($rank->Name, $config['sis']['name']) !== false) ? ' class="sis_highlight"' : '') . '>
						<td>' . $rank->Nr . '.</td>
						<td><a target="_blank" href="http://www.sis-handball.de/web/Default.aspx?view=Mannschaft&Liga=' . $rank->Verein . '">' . $rank->Name . '</a></td>
						<td>' . $rank->Spiele . '</td>
						<td class="sis_separator">/</td>
						<td>' . $rank->SpieleInsgesamt . '</td>
						<td>' . $rank->TorePlus . '</td>
						<td class="sis_separator">:</td>
						<td>' . $rank->ToreMinus . '</td>
						<td>' . $rank->D . '</td>
						<td>' . $rank->PunktePlus . '</td>
						<td class="sis_separator">:</td>
						<td>' . $rank->PunkteMinus . '</td>
					</tr>';
						
					unset($highlight);
				}
				echo '</table>';
			}
			else {
				echo '<table class="sis_all_games">
					<tr><td class="sis_title" colspan="10">{lang:sis:all_games}</td></tr>
					<tr>
						<td class="column_title">{lang:general:date}</td>
						<td class="column_title">{lang:sis:home}</td>
						<td class="column_title">:</td>
						<td class="column_title">{lang:sis:guest}</td>
						<td class="column_title" colspan="3">{lang:sis:result}</td>
						<td class="column_title" colspan="3">{lang:general:extras}</td>
					</tr>';
	
				foreach ($xml->Spiel as $match) {
					$home = (strpos($match->Heim, $config['sis']['name'])  !== false ) ? true : false;
					$guest = (strpos($match->Gast, $config['sis']['name'])  !== false) ? true : false;
						
					if ($home == true || $guest == true) {
						echo '<tr>
							<td>' . date('d.m.y G:i', strtotime($match->SpielVon)) . '</td>
							<td><a target="_blank" href="http://www.sis-handball.de/web/Default.aspx?view=Mannschaft&Liga=' . $match->Mannschaft1 . '">' . $match->Heim . '</a></td>
							<td>:</td>
							<td><a target="_blank" href="http://www.sis-handball.de/web/Default.aspx?view=Mannschaft&Liga=' . $match->Mannschaft2 . '">' . $match->Gast . '</a></td>
							<td>' . $match->Tore1 . '</td>
							<td class="sis_separator">:</td>
							<td>' . $match->Tore2 . '</td>';
						if ($match->HallenName != 'unbekannt') echo '<td><a href="' . $site['path']['web'] . '/index.php?module=gmap&address=' . urlencode($match->HallenOrt . ', ' . $match->HallenStrasse) . '">{icon:map:Halle}</a>';
						if ($guest == true && $match->HallenName != 'unbekannt') echo ' <a href="' . $site['path']['web'] . '/index.php?module=frame&url=' . urlencode('http://maps.google.de/maps?saddr=' . $config['google']['maps']['start_address'] . '&daddr=' . urlencode($match->HallenOrt . ', ' . $match->HallenStrasse)) . '">{icon:car:Anfahrt}</a>';
						if ($match->Anmerkung > '') echo ' {icon:email:' . $match->Anmerkung . '}';
						echo '</td></tr>';
	
						unset($highlight);
					}
				}
				echo '</table>
					<div id="sis_ical"><a href="' . $site['path']['web'] . '/modules/sis/ical.php?cat_id=' . $site['cat']['id'] . '">{icon:time:{lang:sis:ical}} {lang:sis:ical}</a></div>';
			}
			echo '<div id="sis_last_update">Stand: ' . $sis->last_update_formatted . '</div>';
		}
		else {
			echo '<div class="error">{lang:sis:no_data}</div>';
			trigger_error('No SiS data available', E_USER_NOTICE);
		}
	}
	else {
		echo '<div class="error">{lang:sis:no_id}</div>';
	}

	if ($rights['edit']) echo '<div><a href="' . $site['path']['web'] . '/index?module=sis&amp;command=edit&amp;cat_id=' . $cat_id . '">{icon:pencil:{lang:general:edit}}&nbsp;{lang:sis:change_liga}</a></div>';
}
elseif ($rights['show']) {
	echo '<div id="sis_ranking">
			<div class="title">{lang:sis:ranking} ' . $site['season'] . '</div>';

	$result = mysql_query('SELECT id AS cat_id, name AS cat_name FROM categories WHERE sis_liga != \'\' AND season = ' . $site['season'] . ' ORDER BY id ASC');
	if (mysql_num_rows($result) > 0) {
		echo '<table>
				<tr><td class="column_title">{lang:general:team}</td><td class="column_title">{lang:sis:rank}</td></tr>';
		while ($line = mysql_fetch_array($result)) {
			$sis = new sis($line['cat_id']);
			$rank = $sis->get_rank();

			if (!empty ($rank))
			echo '<tr><td class="row_title"><a href="' . $site['path']['web'] . '/index.php?cat_id=' . $line['cat_id'] . '&amp;module=sis">' . htmlspecialchars($line['cat_name']) . '</a></td><td>' . $rank . '</td></tr>';

			if ($sis->last_update > $last_update) {
				$last_update = $sis->last_update;
				$last_update_formatted = $sis->last_update_formatted;
			}
		}
		echo '</table>
			<div id="sis_last_update">Stand: ' . $last_update_formatted . '</div>';
	}
	else {
		echo '<div class="error">{lang:sis:no_data}</div>';
	}
	echo '</div>';
}
else
no_rights();

echo '</div>';

?>
