<?php			
	function dutySummary($date)
	{
		$output = array();
			
		$day = new Day($date);
		$dutypersonnel = $day->setDutyPersonnel()->getDisplayName();
		
		array_push($output, "");
		
		return join('', $output);;
	}
?>