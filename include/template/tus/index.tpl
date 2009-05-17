<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		{head}
	</head>
	<body>
		<div id="wrap">
			<div id="header">
				<a href="index.php">
					<img class="icon" src="include/template/tus/images/tuslogo.png" alt="" />
				</a>
			</div>
			{module:announcement}
			<div id="left">
				{module:season}
				{module:navigation}
			</div>
			<div id="middle">
				{module:newsticker}
				{content}
			</div>
			{module:ads}
			{module:footer}
			{module:debug}
		</div>
		{module:ganalytics}
	</body>
</html>
