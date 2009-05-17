tinyMCE.init({
	mode : "specific_textareas",
	editor_selector : "html_editor",
	theme : "advanced",
	width: "500px",
	height: "300px",
	language : "de",
	content_css :  "include/template/tus/css/content.css",
	theme_advanced_styles : "Ueberschrift 1=headline1; Ueberschrift 2=headline2; Ueberschrift 3=headline3;",
	plugins : "paste, table, advimage, advlink, contextmenu, safari, spellchecker",
	theme_advanced_buttons1 : "bold, italic, underline, separator, cut, copy, paste, separator, undo, redo, separator, styleselect",
	theme_advanced_buttons2 : " justifyleft, justifycenter, justifyright, justifyfull, separator, indent, outdent, separator, tablecontrols",
	theme_advanced_buttons3 : "image, link, unlinktable, separator, bullist, numlist, separator, spellchecker, separator, code",
	fix_table_elements : true,
	verify_css_classes : true,
	paste_create_paragraphs : true,
	paste_create_linebreaks : true,
	paste_use_dialog : true,
	paste_auto_cleanup_on_paste : true,
	paste_convert_headers_to_strong : false,
	paste_strip_class_attributes : "all",
	paste_remove_spans : true,
	paste_remove_styles : true,
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_path : false,
	theme_advanced_statusbar_location : "bottom",	
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : false,
	button_tile_map : true,
	strict_loading_mode : true,
	convert_fonts_to_spans : true,
	fix_table_elements : true,
	force_hex_style_colors : true,
	remove_trailing_nbsp : true,
	entity_encoding : "raw",
	entities : "amp,quot,lt,gt",
	convert_urls : false,
	cleanup: true,
	cleanup_on_startup: true,
	auto_resize : false,
	relative_urls : false,
	remove_script_host : false,
	gecko_spellcheck : false,
	spellchecker_languages : "+Deutsch=de,Englisch=en,Franzoesisch=fr",
	auto_reset_designmode : true,
	valid_elements : "" +
	"+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|" + 
	"ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup]," + 
	"-u[class|style]," + 
	"-p[id|style|dir|class|align]," + 
	"-ol[class|style]," + 
	"-ul[class|style]," + 
	"-li[class|style]," + 
 	"br," + 
 	"img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align]," + 
	"-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor]," + 
	"-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor]," + 
 	"tbody[id|class]," + 
 	"thead[id|class]," + 
 	"tfoot[id|class]," + 
	"-td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope]," + 
	"-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope]," + 
	"-div[id|dir|class|align|style]," + 
	"-span[style|class|align],"
});
