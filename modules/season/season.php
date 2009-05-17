<?php
############################################
# TUS Griesheim Handball CMS (10.5.2007)   #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)        #
############################################
# modules/season.php - The Season Module   #
############################################

echo '<div id="season">
<form id="season_form" action="' . $site['path']['web'] . '/index.php?module=' . $site['module'] . '&amp;command=' .  $site['command'] . '" method="get">
<table>
	<tr><td class="row_title">{lang:general:season}</td><td><select name="season" size="1" onchange="document.getElementById(\'season_form\').submit();">';

$selected = '';
foreach (get_seasons() as $season)
	echo '<option value="' . $season . '" ' . (($season == $site['season']) ? 'selected="selected"' : '') . '>' . $season . '/' . ($season + 1) . '</option>';

echo '</select>
		</td></tr>
	</table>
</form>
</div>';

?>