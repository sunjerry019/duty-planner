<?php			
	function dutySummary($date)
	{
		$day = new Day($date);
		$dutypersonnel = $day->setDutyPersonnel()->getDisplayName();
		$farbe = getColorFromText($dutypersonnel);
		
		$tmp = strtoupper($dutypersonnel[0]);
		
		$output = <<<DP
<div class="row valign-wrapper">
	<div class="col s2">
		<div class="dp-profile white-text" style="background-color: {$farbe};"><div class="initial">{$tmp}</div></div>
	</div>
	<div class="col s10">
		<div class="black-text center dp-name">{$dutypersonnel}</div>
	</div>
</div>
DP;
		
		return $output;
	}
?>