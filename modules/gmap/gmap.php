<?php
############################################
# TUS Griesheim Handball CMS  (21.11.2007) #
# Web: http://www.tusgriesheim.de/handball #
# Mail: handball@tusgriesheim.de           #
# by Steffen Vogel (info@steffenvogel.de)        #
############################################
# modules/gmap/gmap.php - The GMap Module  #
############################################

$head['js_google_maps'] = '<script type="text/javascript" src="http://www.google.com/jsapi?key=' . $config['google']['ajax_key'] . '"></script>';
$head['js_google_maps_settings'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/gmap.js"></script>';
$head['title'] = '<title>{lang:general:gmap}</title>';

echo '<div id="gmap">
		<div id="gmap_map_container"></div>
		<div id="gmap_search_container"><input id="gmap_address" type="text" /><input type="button" value="{lang:gmap:find}" onclick="showAddress(getElementById(\'gmap_address\').value)" /></div>
		<div id="gmap_route_container">{lang:gmap:route_from} <input type="text" id="gmap_start_address" value="' .  $config['google']['maps']['start_address'] . '" /> {lang:gmap:route_to} <input type="text" id="gmap_destination_address" /> <input type="button" onclick="window.location = \'http://maps.google.de/maps?saddr=\' + getElementById(\'gmap_start_address\').value + \'&daddr=\' + getElementById(\'gmap_destination_address\').value" value="{lang:gmap:plan}" /></div>
	</div>';

?>