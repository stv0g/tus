<?php
$site['path']['server'] = dirname(dirname(dirname(__FILE__)));

include $site['path']['server'] . '/include/init.inc.php';

function getValue($token, $doc) {
	return $doc->getElementById($token)->getAttribute('value');
}

$url = 'http://web1.sis-handball.de/xmlexport/Login.aspx?ReturnUrl=%2fxmlexport%2fsuche_liga.aspx';
$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 
$cookies = 'cookie.txt';
$reffer = "http://web1.sis-handball.de/xmlexport/";

$fp = fopen($cookies,"w") or die("Unable to open cookie file for write!");
fclose($fp);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$doc = new DOMDocument();
$doc->loadHTML(curl_exec($ch));
curl_close($ch);

$post = '__VIEWSTATE=' . getValue('__VIEWSTATE', $doc) . '&__EVENTVALIDATION=' . getValue('__EVENTVALIDATION', $doc) . '&TextBox1=' . $config['sis']['user'] . '&TextBox2=' . $config['sis']['pw'] . '&Button1=Anmelden';

//die($post);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

$result = str_replace('bgcolor="#ff9966"', 'class="Title"', $result);
$result = str_replace(' bgcolor="#ffffcc"', '', $result);
$result = str_replace('bgcolor="black"', 'id="Table"', $result);

$result = preg_replace("/sendback\('(\d+)', 'ctl00_ContentPlaceHolder1_txtLigaVerein'\);sendback\('(\d+)', 'ctl00_ContentPlaceHolder1_txtLigaVereinHidden'\);schliessen\(\);/", 'sendback(\'sis_liga\', \'${1}\'); self.close();', $result);

$result = str_replace('style.css', $site['path']['web'] . '/include/template/' . $config['site']['template'] . '/css/select_sis_liga.css', $result);
$result = str_replace('GateCom.JSTools.js', $site['path']['web'] . '/include/javascript/select_sis_liga.js', $result);

echo $result;

unlink($cookies);
?>
