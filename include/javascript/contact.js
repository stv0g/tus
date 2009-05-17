function check_contact(element) {	
	name = check_string(element.name);
	mail = check_mail(element.mail);
	web = check_uri(element.web, true);
	subject = check_string(element.subject);
	text = check_string(element.text);	
	captcha = check_string(element.captcha);
	
	if (name == true && mail == true && web == true && subject == true && text == true && captcha == true) return true;
	else {
		alert('Bitte Eingaben korrigieren!');
		return false;
	}
}