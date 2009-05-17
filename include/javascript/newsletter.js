function check_newsletter(element) {	
	if (check_mail(element.from) && check_string(element.subject) == true && tinyMCE.getContent() != "") {
		return true;
	}
	else {
		alert('Bitte Eingaben korrigieren!');
		return false;
	}
}