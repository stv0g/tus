<?php

if ($_SERVER['SERVER_ADDR'] != '127.0.0.1')
	echo '<!-- Google Analytics Start -->
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
			<script type="text/javascript">
				_uacct = "' . $config['google']['analytics'] . '";
				urchinTracker();
			</script>
			<!-- Google Analytics End -->';
?>