function sendback(obj, value) {
	if (value != '') {
		b = parent.window.opener.document.getElementById(obj) ;
		b.value = value;
	}
}

function changeColor(obj, color) {
	obj.className = color;
}