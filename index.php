<?php
	
	//include this file at the start of every page
	//library.inc.php includes the all objects and the configuration
	require("library.inc.php");
	
	//sets and returns duty personnel (as a User object) for 10 Sept 2018
	//returns the duty personnel if duty personnel is already set for the day
	$day = new Day("2018-09-10"); //all dates use YYYY-MM-DD
	echo "The duty personnel for 10 Sept 2018 is:<br/>";
	echo $day->setDutyPersonnel()->getDisplayName()."<br /><br />";

	//users can be constructed by either passing its database ID or username
	$user1 = new User(1);
	$user2 = new User("charlie");
	echo "The user with DB index 1 is:<br />".$user1->getDisplayName()."<br /><br />";
	echo "The user with username \"charlie\" is:<br />".$user2->getDisplayName()."<br />";
	
?>
