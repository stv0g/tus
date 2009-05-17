function http_request(url, data) {
	search_request = null;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		search_request = new XMLHttpRequest();
		if (search_request.overrideMimeType) {
			search_request.overrideMimeType('text/xml');
		}
	}
	else
		if (window.ActiveXObject) { // IE
			try {
				search_request = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					search_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
		}

	if (!search_request) {
		alert('Ende :( Kann keine XMLHTTP-Instanz erzeugen');
		return false;
	}
	
	search_request.onreadystatechange = requeststatechanged;
	search_request.open('POST', url, true);
	search_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	search_request.send(data);
	
}

function requeststatechanged() {
	if (search_request.readyState == 4) {
		if (search_request.status == 200) {
			document.getElementById('search_results').innerHTML = search_request.responseText;
		}
		else {
			alert('Bei dem Request ist ein Problem aufgetreten.');
		}
	}
}

var timer;

function delay_search() {
		if (timer)
			clearTimeout(timer);
		timer = setTimeout('search()', 500);
}

function search() {
		if (timer)
			clearTimeout(timer);
		if (check_string(document.getElementById('search_query'), true)) {
			params = 'search_query=' + encodeURIComponent(document.getElementById('search_query').value) + '&cat_id=' + document.getElementById('cat_id').value + '&editor_id=' + document.getElementById('editor_id').value + '&season=' + document.getElementById('search_season').value + '&sort=' + document.getElementById('sort').value + '&order=' + document.getElementById('order').value;
			
			for(var i=0; i < document.getElementsByName('type[]').length; i++){
				if(document.getElementsByName('type[]')[i].checked)
				params += '&type[]=' + document.getElementsByName('type[]')[i].value
			}
			
			http_request('modules/search/request.php', params);
			document.getElementById('listing_newsfeed').href = 'modules/article/newsfeed.php?' + params;
			document.getElementById('site_newsfeed').href = 'modules/article/newsfeed.php?' + params;
		}
		else
			alert('Bitte Suche korrigieren!');
}