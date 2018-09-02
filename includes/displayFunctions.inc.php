<?php			
	function dutySummary($date)
	{
		$day = new Day($date);
		$dutypersonnel = $day->setDutyPersonnel()->getDisplayName();
		return $dutypersonnel;
	}
?>