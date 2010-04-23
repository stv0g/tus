<?php

preg_match("/(\d+)/", '$Revision$', $result);
$rev = $result[1];

preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/", '$Date$', $result);
$date = strftime('%A, %e. %B %Y, %H:%M' ,strtotime($result[1]));

echo '<div id="footer">
		{lang:general:page_copy} - CMS Revision: <a href="https://0l.de/svn/tus/trunk/?rev=' . $rev . '">' . $rev . '</a> (' . $date . ') - Code &amp; Design von <a href="http://www.steffenvogel.de/">Steffen Vogel</a>
	</div>';
?>
