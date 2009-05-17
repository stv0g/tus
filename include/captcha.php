<?php

$site['path']['web'] = $_SERVER['SCRIPT_NAME'];
for ($i = 0; $i < 2; $i++) $site['path']['web'] = dirname($site['path']['web']);
$site['path']['web'] = (substr($site['path']['web'], -1, 1) == DIRECTORY_SEPARATOR) ? substr($site['path']['web'], 0, -1) : $site['path']['web'];
$site['path']['server'] = $_SERVER['DOCUMENT_ROOT'] . $site['path']['web'];

include $site['path']['server'] . '/include/init.inc.php';

header('Content-Type: image/gif', true);
header('Cache-Control: no-store', true);

$img = imagecreatetruecolor($config['captcha']['img']['width'], $config['captcha']['img']['height']);
//$col = imagecolorallocate($img, rand(200, 255), rand(200, 255), rand(200, 255));
$col = imagecolorallocate($img, 255, 255, 255);

imagefill($img, 0, 0, $col);

$captcha = '';
$x = 10;

for ($p = 0; $p < 15; $p++) {
	$col = imagecolorallocate($img, rand(150, 255), rand(150, 255), rand(150, 255));
	imageline($img, rand(0, $config['captcha']['img']['width']), rand(0, $config['captcha']['img']['height']), rand(0, $config['captcha']['img']['width']), rand(0, $config['captcha']['img']['height']), $col);
}


for($i = 0; $i < $config['captcha']['lenght']; $i++) {

	$chr = $config['captcha']['alphabet'][rand(0, count($config['captcha']['alphabet']) - 1)];
	$captcha .= $chr;

	$col = imagecolorallocate($img, rand(0, 199), rand(0, 199), rand(0, 199));
	$font = $site['path']['server'] . '/include/fonts/' . $config['captcha']['fonts'][rand(0, count($config['captcha']['fonts']) - 1)] . '.ttf';

	$y = 25 + rand(0, 20);
	$angle = rand(0, 45);

	imagettftext($img, $config['captcha']['font_size'], $angle, $x, $y, $col, $font, $chr);

	$dim = imagettfbbox($config['captcha']['font_size'], $angle, $font, $chr);
	$x += $dim[4] + abs($dim[6]) + 10;
}

imagegif($img);
imagedestroy($img);

$_SESSION['captcha'] = $captcha;

?> 