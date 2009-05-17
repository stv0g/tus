function clear() {
	document.getElementById("rival").style.display = "none";
	document.getElementById("score").style.display = "none";
	document.getElementById("title").style.display = "none";
	document.getElementById("rank").style.display = "none";
	document.getElementById("organizer").style.display = "none";
	document.getElementById("custom_mail").style.display = "none";
	//document.getElementById("announce").style.display = "none";
	//document.getElementById("press").style.display = "none";
	//document.getElementById("text").style.display = "none";
	//document.getElementById("submit").style.display = "none";
	//document.getElementById("announce").style.display = "none";
	//document.getElementById("date").style.display = "none";
}

function announce_func(element) {
	if (document.forms.article.type.value == 1 || document.forms.article.type.value == 2)
		document.getElementById("score").style.display = (element.checked == true) ? "none" : "";
	if (document.forms.article.type.value == 4)
		document.getElementById("rank").style.display = (element.checked == true) ? "none" : "";
}

function custom_mail_func(element) {
	if (element.checked == true)
		document.getElementById('custom_mail').style.display = "";
	else
		document.getElementById('custom_mail').style.display = "none";
}

function show() {
	clear();
	if (document.forms.article.type.value == 'home' || document.forms.article.type.value == 'outwards') {
		document.getElementById("rival").style.display = "";
		document.getElementById("score").style.display = "";
		
	}
	else if (document.forms.article.type.value == 'article') {
		document.getElementById("title").style.display = "";
	}
	else if (document.forms.article.type.value == 'tournament') {
		document.getElementById("organizer").style.display = "";
		document.getElementById("rank").style.display = "";
	}
	
	announce_func(document.forms.article.announce);
	custom_mail_func(document.forms.article.custom_mail);
		
	if (document.forms.article.send_to_custom_mail.checked == true)
		document.getElementById("custom_mail").style.display = "";
	
	document.getElementById("announce").style.display = "";
	document.getElementById("press").style.display = "";
	document.getElementById("text").style.display = "";
	document.getElementById("submit").style.display = "";
	document.getElementById("date").style.display = "";
}

function check_article(element) {
	state = false;
	
	if (element.type.value == 'home' || element.type.value == 'outwards') {
		rival = check_string(element.rival);
		
		if (element.announce.checked == false) {
			score_home = check_number(element.score_home);
			score_rival = check_number(element.score_rival);
		}
		else {
			score_home = true;
			score_rival = true;
		}
		
		if (rival == true && score_home == true  && score_rival == true)
			state =  true	
	}
	else if (element.type.value == 'article') {
			state = check_string(element.title);
	}
	else if (element.type.value == 'tournament') {
		organizer = check_string(element.organizer);
		
		if (element.announce.checked == false)
			rank = check_number(element.rank);
		else
			rank == true;

		
		if (rank == true && organizer == true )
			state = true;
	}
	else
		state = false;
	
	mail = (element.send_to_custom_mail.checked == true) ? check_mail(element.custom_mail) : mail = true;
	date = (check_number(element.date_day) && check_number(element.date_month) && check_number(element.date_year)) ? true : false;
	
	if (state == true && mail == true && date == true && tinyMCE.getContent() != "")
		return true;
	else {
		alert("Bitte korrigieren Sie ihre Eingaben!");
		return false;
	}
}