var pop = null;

function clip(element) {
	if(element.parentNode.nextSibling.style.display == "none") {
		element.previousSibling.src = element.previousSibling.src.replace(/plus/g,"minus");
		element.parentNode.nextSibling.style.display = "block";
	}
	else {
		element.previousSibling.src = element.previousSibling.src.replace(/minus/g,"plus");
		element.parentNode.nextSibling.style.display = "none";
	}
}

function get_parameters(querystring) {
	if (!querystring)
		return false;
	var parameters = querystring.slice(1);
	var pairs = parameters.split("&");
	var pair, name, value;
	for (var i = 0; i < pairs.length; i++) {
		pair = pairs[i].split("=");
		name = pair[0];
		value = pair[1];
		name = decodeURIComponent(name).replace(/\+/g, " ");
		value = decodeURIComponent(value).replace(/\+/g, " ");
		this[name] = value;
	}
}

function popup(url, windowname, w, h) {
	if (!url)
		return false;
	w = (w) ? w += 20 : 150;
	h = (h) ? h += 25 : 150;
	var args = 'width='+w+',height='+h+',resizable, scrollbars=yes';
	
	popdown();
	pop = window.open(url,windowname,args);
	return (pop) ? false : true;
}

function popdown() {
	if (pop && !pop.closed) pop.close();
}

window.onunload = popdown;
window.onfocus = popdown;