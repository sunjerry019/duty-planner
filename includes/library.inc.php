<?php

	/**************************************************
	 * Main library to handle most if not all actions *
	 *       Usage: require_once("library.php")       *
	 **************************************************/

	date_default_timezone_set('Asia/Singapore');

	require("User.inc.php");
	require("Day.inc.php");
	require("DutyPool.inc.php");

	const DB_PATH = "sqlite:database.db";
	const WEEKDAY_POINTS = 1;
	const FRIDAY_POINTS = 1.5;
	const WEEKEND_POINTS = 2;

?>
