myInitFunction = function () {
	// ensure window title in inlinepopups
	var obj; 
	var inlinepopups = false; 
	for (obj in tinyMCE.selectedInstance.plugins)
		if (tinyMCE.selectedInstance.plugins[obj] == "inlinepopups")
			inlinepopups = true;

	if (inlinepopups)
		tinyMCE.setWindowTitle(window, document.getElementsByTagName("title")[0].innerHTML);
}

function submit(url, title, description) {
	// insert information now
	win.document.getElementById(src).value = url;
	if (src == "src") {
		win.document.getElementById("title").value = title;
		win.document.getElementById("alt").value = description;
	}

	// Update image Preview
	win.showPreviewImage(url);
	
	// close popup window
	tinyMCEPopup.close();
}

var src = tinyMCE.getWindowArg("src");
var win = tinyMCE.getWindowArg("window");
var res = tinyMCE.getWindowArg("resizable");
var inline = tinyMCE.getWindowArg("inline");