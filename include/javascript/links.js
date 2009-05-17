function check_links(element) {	
	if (check_uri(element.uri) && check_string(element.description))
		return true;
	else {
		alert('Bitte Eingaben korrigieren!');
		return false;
	}
}