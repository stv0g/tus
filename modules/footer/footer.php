<?php

preg_match("/(\d+)/", '$Revision$', $result);
$rev = $result[1];

preg_match("/\((.*)\)/", '$Date$', $result);
$date = $result[1];

echo '<div id="footer">
		{lang:general:page_copy} - 
		CMS Revision: <a href="http://svn.0l.de/tus">' . $rev . '</a> (' . $date . ') - Code &amp; Design von <a href="http://www.steffenvogel.de/">Steffen Vogel</a>
	</div>';
?>
