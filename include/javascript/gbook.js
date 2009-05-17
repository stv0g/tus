function check_gbook(element) {	
	nick = check_string(element.name);
	city = check_string(element.city);
	mail = check_mail(element.mail);
	web = check_uri(element.web, true);
	text = check_string(element.text);
	captcha = check_string(element.captcha);
	
	if (nick == true && city == true && mail == true && web == true && text == true && captcha == true)
		return true;
	else {
		alert("Bitte korrigieren Sie ihre Eingaben!");
		return false;
	}
}