google.load("maps", "2.x");

var map;
var geocoder;
var griesheim;
var zoom;
var address;
var label;

function showAddress(address, zoom, label) {
	if (geocoder) {
		if (!label)
			label = address;
		
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
					alert(label + " wurde nicht gefunden!");
				}
				else {
					map.setCenter(point, zoom);
					var marker = new google.maps.Marker(point);
					map.addOverlay(marker);
					marker.openInfoWindowHtml(label);
				}
			}
		);
	}
}

function initialize() {
	if (google.maps.BrowserIsCompatible()) {
		map = new google.maps.Map2(document.getElementById("gmap_map_container"));
	
		map.addControl(new google.maps.LargeMapControl());
		map.addControl(new google.maps.MapTypeControl());

		griesheim = new google.maps.LatLng(49.863889, 8.563889);
		geocoder = new google.maps.ClientGeocoder();
		
		parameters = new get_parameters(location.search);
		
		if (parameters['zoom'])
			zoom = parseInt(parameters['zoom']);
		else
			zoom = 13;
			
		if (parameters['address'])
			address = parameters['address'];
		else
			address = '64347 Griesheim';
		
		document.getElementById('gmap_destination_address').value = address;
		showAddress(address, zoom, parameters['label']);
	}
}

google.setOnLoadCallback(initialize);