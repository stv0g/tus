function check_login(element) {	
	if (check_string(element.name) && check_string(element.pw))
		return true;
	else {
		alert('Bitte Login-Daten korrigieren!');
		return false;
	}
}

function check_pws(element) {	
	if (check_string(element.new_pw) && check_string(element.old_pw) && compare_pws(element.new_pw, element.repeat_pw))
		return true;
	else {
		alert('Bitte korrigieren Sie ihre Eingaben!');
		return false;
	}
}

function compare_pws(element, repeat_element)
{
	if(element.value == repeat_element.value) {
		hide_error(repeat_element);
		return true;
	}
	else {
		show_error(repeat_element);
		return false;
		
	}
}