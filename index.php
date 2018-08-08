<?php require("library.inc.php"); ?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Duty Planner</title>
		<link href="//fonts.googleapis.com/css?family=Roboto|Source+Sans+Pro" rel="stylesheet">
		<link rel="stylesheet" href="style.css">
	</head>
	<body class="start">
		<h1>Duty Planner</h1>
		<h3>The duty personnel for today is:</h3>
		<div class="dutypersonnel">
			<?php
				$day = new Day(date("Y-m-d"));
				echo $day->setDutyPersonnel()->getDisplayName();
			?>
		</div>
	</body>
</html>
