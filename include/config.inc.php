<?php

$config['site']['editor'] = 'tiny_mce';
$config['site']['season'] = 2009;
$config['site']['announcement'] = false;

$config['mysql']['user'] = 'tus';
$config['mysql']['pw'] = 'f1b0n4c1';
$config['mysql']['host'] = 'localhost';
$config['mysql']['db'] = 'st_tus';

$config['mail']['tus'] = 'handball@tusgriesheim.de';
$config['mail']['admin'] = 'handball@steffenvogel.de';

$config['mail']['granzeiger'] = 'redaktion-ga@bassenauer-druck.de';
$config['mail']['grwoche'] = 'info@griesheimer-woche.de';
$config['mail']['daecho'] = 'bkalkhof@web.de';

$config['mail']['header']['MIME-Version'] = '1.0';
$config['mail']['header']['From'] = $config['mail']['tus'];
$config['mail']['header']['Content-type'] = 'text/html; charset=utf-8';

$config['gallery']['max_height'] = 600;
$config['gallery']['max_width'] = 700;
$config['gallery']['quality'] = 80;
$config['gallery']['max_size'] = 5000; // in kilobytes

$config['articles']['home_count'] = 5;
$config['articles']['types'] = array ('article',
										'home',
										'outwards',
										'tournament');

$config['types'] = array ('home' =>'house',
							'outwards' => 'house_go',
							'article' => 'page_white_text',
							'tournament' => 'award_star_gold_1',
							'picture' => 'picture',
							'gbook' => 'book_open');

$config['site']['language'] = 'de';
$config['site']['template'] = 'tus';
$config['site']['redirect_timeout'] = 5;	// in seconds
$config['site']['name'] = 'TuS Griesheim Handball';
$config['site']['club'] = 'TuS Griesheim';

$config['security']['salt'] = '!3G&s"A';

$config['sis']['update'] = 60*30; // in seconds
$config['sis']['name'] = $config['site']['club']; // for matching own club in sis
$config['sis']['user'] = '1410801130';
$config['sis']['pw'] = '819725';

$config['google']['ajax_key'] = 'ABQIAAAA_W-Ke-iVOU-RFtiVGjLiOBRJ5Kj2mka_gRAKl9NXdzeGHPVivxQWJaKPRQ_v2zM4AvIgSdO8bXIkJA';
$config['google']['adsense'] = 'pub-8255125355392999';
$config['google']['analytics'] = 'UA-3065627-1';
$config['google']['webtools'] = 'dcYyM0kaLVw2WjMC/vxrEhJISGK8ZEFaBzPLdXCv9zM=';
$config['google']['maps']['start_address'] = '64347 Griesheim, Am Felsenkeller';

$config['search']['types'] = $config['articles']['types'];

$config['newsfeed']['image_url'] = $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/images/tuslogo.gif';
$config['newsfeed']['types'] = $config['articles']['types'];
$config['newsfeed']['count'] = 20;

$config['captcha']['lenght'] = 5;
$config['captcha']['font_size'] = 18;
$config['captcha']['img']['width'] = 145; 
$config['captcha']['img']['height'] = 50;
	
$config['captcha']['fonts'] = array('technine',
									'texasled',
									'xband',
									'3000',
									'42',
									'39smooth');

$config['captcha']['alphabet'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G',
										'H', 'Q', 'J', 'K', 'L', 'M', 'N',
										'P', 'R', 'S', 'T', 'U', 'V', 'Y',
										'W', '2', '3', '4', '5', '6', '7');
?>
