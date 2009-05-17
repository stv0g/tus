check_upload_var = window.setInterval("check_upload()",100);


function check_upload() {
	if (document.getElementById('upload_picture').value != "") {
		window.clearInterval(check_upload_var);
		do_upload();
	}
}


function do_upload() {
	alt = check_string(document.getElementById('alt'));
	title = check_string(document.getElementById('title'));
	picture = check_string(document.getElementById('upload_picture'));
	
	if (alt && title) {
		iframe = document.createElement("iframe");
		iframe.setAttribute("name","upload_ifr");
		iframe.setAttribute("width","0");
		iframe.setAttribute("height","0");
		iframe.setAttribute("frameborder","0");
			
		document.body.appendChild(iframe);
			
		top.frames.upload_ifr.document.write('<?xml version="1.0" encoding="UTF-8" ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>Iframe for Form</title></head><body></body></html>');
		
		form = document.createElement('form');
		form.name = "upload_ifr_form";
		form.method = "post";
		form.action = "upload.php?" + opener.location.search;
		form.setAttribute("enctype","multipart/form-data");
			
		top.frames.upload_ifr.document.body.appendChild(form);
		
		document.getElementById('upload_picture').style.display = 'none';
		document.getElementById('upload_animation').style.display = '';
		
		file_node = document.getElementById('upload_picture').cloneNode(true);
		title_node = document.getElementById('title').cloneNode(true);
		alt_node = document.getElementById('alt').cloneNode(true);
		top.frames.upload_ifr.document.forms.upload_ifr_form.appendChild(file_node);
		top.frames.upload_ifr.document.forms.upload_ifr_form.appendChild(title_node);
		top.frames.upload_ifr.document.forms.upload_ifr_form.appendChild(alt_node);
			
		top.frames.upload_ifr.document.forms.upload_ifr_form.submit();
			
		check_upload_done = window.setInterval(function() {
			if (top.frames.upload_ifr.document.getElementById('success') || top.frames.upload_ifr.document.getElementById('error')) {
				if (top.frames.upload_ifr.document.getElementById('success')) {
					document.getElementById('src').value = top.frames.upload_ifr.document.getElementById('success').innerHTML;
					ImageDialog.showPreviewImage(document.getElementById('src').value);
					mcTabs.displayTab('general_tab','general_panel');
				}
				else if (top.frames.upload_ifr.document.getElementById('error')) {
					 alert("Die Datei konnte nicht hochgeladen werden:\r\n" + top.frames.upload_ifr.document.getElementById('error').innerHTML);
				}
				else
					alert("Unbekannter Fehler!");
					
				document.getElementById('upload_picture').style.display = '';
				document.getElementById('upload_animation').style.display = 'none';
				
				window.clearInterval(check_upload_done);
			}
		}, 100);
	}
	else {
		mcTabs.displayTab('general_tab','general_panel');
	}
	
	document.getElementById('upload_picture').value = '';
	check_upload_var = window.setInterval("check_upload()",100);
}