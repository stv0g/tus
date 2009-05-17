<?php
$head['title'] =				'<title>' . $config['site']['name'] . '</title>';

$head['meta_content_type'] =	'<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />';
$head['meta_language'] =		'<meta http-equiv="content-language" content="' . $config['site']['language'] . '"/>';
$head['meta_pragma'] =			'<meta http-equiv="pragma" content="no-cache"/>';
$head['meta_cache'] =			'<meta http-equiv="cache-control" content="no-cache"/>';
$head['meta_reply'] =			'<meta http-equiv="reply-to" content="' . $config['mail']['tus'] . '"/>';
$head['meta_description'] =		'<meta name="description" content="{lang:general:page_description}" />';
$head['meta_keywords'] =		'<meta name="keywords" content="Handball, TuS, Griesheim, Verein" />';
$head['meta_robots'] =			'<meta name="robots" content="index, follow" />';
$head['meta_revisit'] =			'<meta name="revisit-after" content="5 days" />';
$head['meta_expires'] =			'<meta name="expires" content="' . date('r', time() + 5*24*60*60) . '"/>';
$head['meta_date'] =			'<meta name="date" content="' . date('r') . '" />';
$head['meta_copyright'] =		'<meta name="copyright" content="{lang:general:page_copy}" />';
$head['meta_author'] =			'<meta name="author" content="' . $config['site']['name'] . ', ' . $config['mail']['tus'] . '" />';
$head['meta_publisher'] =		'<meta name="publisher" content="' . $config['site']['name'] . '"/>';
$head['meta_owner'] =			'<meta name="owner" content="' . $config['site']['name'] . '"/>';
$head['meta_address'] =			'<meta name="address" content="64347 Griesheim, Deutschland" />';
$head['meta_designer'] =		'<meta name="designer" content="Steffen Vogel"/>';
$head['meta_template'] =		'<meta name="template" content="' . $config['site']['template'] . '"/>';
$head['meta_geo_position'] =	'<meta name="geo.position" content="49.864560, 8.577576"/>';
$head['meta_geo_placename'] =	'<meta name="geo.placename" content="Griesheim"/>';
$head['meta_geo_country'] =		'<meta name="geo.country" content="de"/>';
$head['meta_google_wt'] =		'<meta name="verify-v1" content="dcYyM0kaLVw2WjMC/vxrEhJISGK8ZEFaBzPLdXCv9zM=" />';

$head['script_check'] =			'<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/check.js"></script>';
$head['script_general'] =		'<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/general.js"></script>';
$head['script_cookies'] =		'<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/cookies.js"></script>';

$head['css_general'] =			'<link rel="stylesheet" type="text/css" href="' . $site['path']['web'] . '/include/template/' . $config['site']['template'] . '/css/general.css" />';
$head['css_content'] =			'<link rel="stylesheet" type="text/css" href="' . $site['path']['web'] . '/include/template/' . $config['site']['template'] . '/css/content.css" />';

$head['link_newsfeed'] =		'<link id="site_newsfeed" href="' . $site['path']['web'] . '/modules/article/newsfeed.php" title="{lang:general:page_description}" type="application/rss+xml" rel="alternate" />';
$head['link_gbook'] =			'<link rel="gbook" href="' . $site['path']['web'] . '/index.php?module=gbook" title="{lang:general:gbook}" />';
$head['link_contact'] =			'<link rel="contact" href="' . $site['path']['web'] . '/index.php?module=contact" title="{lang:general:contact}" />';
$head['link_links'] =			'<link rel="links" href="' . $site['path']['web'] . '/index.php?module=links" title="{lang:general:links}" />';
$head['link_impressum'] =		'<link rel="impressum" href="' . $site['path']['web'] . '/index.php?id=199&amp;cat_id=1" title="{lang:general:impressum}" />';
$head['link_forum'] =			'<link rel="forum" href="http://forum.griesm.de" title="{lang:general:forum}" />';
$head['link_search'] =			'<link rel="search" href="' . $site['path']['web'] . '/index.php?module=search" title="{lang:general:search}" />';
$head['link_icon_1'] =			'<link rel="shortcut icon" href="' . $site['path']['web'] . '//images/favicon.ico" type="image/x-icon"/>';
$head['link_icon_2'] =			'<link rel="icon" href="' . $site['path']['web'] . '/images/favicon.ico" type="image/x-icon"/>';
$head['link_bookmark'] =		'<link rel="bookmark" type="text/html" href="http://tusgriesheim.de/handball/" title="' . $config['site']['name'] . '"/>';

?>