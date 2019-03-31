<?php
	require("../includes/colours.inc.php");

	$str = "Sun Yudong";
	$farbe = getColorFromText($str);
	echo "<body style='background-color:".$farbe."; color: ".idealTextColour($farbe).";'>".$str."</body>";
?>