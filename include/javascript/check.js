function show_error(element)
{
	element.parentNode.getElementsByTagName("span")[0].style.display = "";
	element.style.backgroundColor = "#790909";
	element.style.color = "#FFFFFF";
}

function hide_error(element)
{
	element.style.backgroundColor = "";
	element.style.color = "";
	element.parentNode.getElementsByTagName("span")[0].style.display = "none";
}

function check_string(element, optional)
{
	filter = RegExp("[a-zA-Z0-9üöäÜÖÄß?!.,\"'-\*\+\-]");

	if (filter.test(element.value) || (optional == true && element.value == "")) {
		hide_error(element);
		return true;
	}
	else {
		show_error(element);
		return false;
	}
}

function check_uri(element, optional)
{
	var protocol = "(http(s)?|ftp)";
	var domain = "([a-zA-Z0-9äöüÄÖÜ][a-zA-Z0-9äöüÄÖÜ._-]*\\.)*[a-zA-Z0-9äöüÄÖÜ][a-zA-Z0-9äöüÄÖÜ._-]*\\.[a-zA-Z]{2,6}";
	var path = "[a-zA-Z0-9äöüÄÖÜ._%/-]*";
	filter = new RegExp("^" + protocol + "://" + domain + path + "$");
	
	if (filter.test(element.value) || (optional == true && (element.value == "" || element.value === "http://"))) {
		hide_error(element);
		return true;
	}
	else {
		show_error(element);
		return false;
	}
}

function check_mail(element, optional)
{
	var usr    = "([a-zA-Z0-9][a-zA-Z0-9_.-]*|\"([^\\\\\x80-\xff\015\012\"]|\\\\[^\x80-\xff])+\")";
	var domain = "([a-zA-Z0-9äöüÄÖÜ][a-zA-Z0-9äöüÄÖÜ._-]*\\.)*[a-zA-Z0-9äöüÄÖÜ][a-zA-Z0-9äöüÄÖÜ._-]*\\.[a-zA-Z]{2,6}";
	filter = new RegExp("^" + usr + "\@" + domain + "$");

	if (filter.test(element.value) || (optional == true && element.value == "")) {
		hide_error(element);
		return true;
	}
	else {
		show_error(element);
		return false;
	}
}

function check_number(element, optional)
{
	var filter  = /^[\d+]+$/;
	
	if (filter.test(element.value) || (optional == true && element.value == "")) {
		hide_error(element);
		return true;
	}
	else {
		show_error(element);
		return false;
	}
}