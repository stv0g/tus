<?php

if ($_SERVER['SERVER_ADDR'] != '127.0.0.1' || ($_SERVER['SERVER_ADDR'] != '127.0.0.1' && empty($site['usr']['id'])))
	echo '<div id="gads">
			<!-- Google Ads Start -->
			<div id="ads_content">
				<script type="text/javascript"><!--
					google_ad_client = "' . $config['google']['adsense'] . '";
					//TuS Handball Footer Leaderboard
					google_ad_slot = "2068427574";
					google_ad_width = 728;
					google_ad_height = 90;
			//--></script>
				<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
			</div>
			<!-- Google Ads End -->
		</div>';
?>