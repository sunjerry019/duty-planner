<?php require("library.inc.php"); ?>
<?php 
	$doc_root 	= "/duty-planner";
	$user		= "35SCE";
	$calendar = array(
		"ro" => $doc_root."/35SCE/public/basic.ics",
		"rw" => $doc_root."/35SCE/private-SxSaoqIjm9hG95wBeF9M/basic.ics"
    );
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>Duty Planner</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Hanalei+Fill&text=DutyPlanner" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Mono|Material+Icons" rel="stylesheet">

	<!-- Materialize CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
	<!-- /Materialize CSS -->

	<link rel="stylesheet" href="style.css" type="text/css">
	<script src="script.js"></script>
</head>

<body>
	<div class="navbar-fixed">
		<!-- <ul id="quickFunctions" class="dropdown-content">
				<li><a href="#!">Swap Duties</a></li>
				<li class="divider"></li>
				<li><a href="#!">Manual Input</a></li>
			</ul> -->
		<nav>
			<div class="nav-wrapper teal darken-1">
				<a href="<?php echo $doc_root ?>/" class="brand-logo center">Duty Planner</a>
				<ul id="nav-mobile" class="left hide-on-med-and-down">
					<li class="active"><a href="<?php echo $doc_root ?>/" class="waves-effect waves-light"><i class="material-icons">dashboard</i></a></li>
					<li><a href="<?php echo $doc_root ?>/settings.php" class="waves-effect waves-light"><i class="material-icons">settings</i></a></li>
					<!-- <li><a class="dropdown-trigger" href="#!" data-target="quickFunctions" class="waves-effect">Quick Functions<i class="material-icons right">arrow_drop_down</i></a></li> -->
					<li><a href="<?php echo $doc_root ?>/logout.php" class="waves-effect waves-light"><i class="material-icons">exit_to_app</i></a></li>
				</ul>
			</div>
		</nav>
	</div>

	<div class="container" id="dashboard-wrap">
		<h3>Dashboard <small class="right">[<?php echo $user ?>]</small></h3>
		<div class="row">
			<div class="col s12 m6" id="download">
				<div class="card blue-grey darken-4">
					<div class="card-content white-text">
						<span class="card-title">Sync iCal</span>
						<p>You may use the links below for syncing with Google Calendar/Outlook etc.</p>
						<p class="red-text text-lighten-1">Please only share the Read-only version with others.</p>
					</div>
					<div class="card-action">
						<a href="<?php echo $calendar["ro"] ?>" class="ical">Read Only</a>
						<a href="<?php echo $calendar["rw"] ?>" class="ical">Read Write</a>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div id="modallen">
		<div id="alert" class="modal">
			<div class="modal-content">
				<h4 id="alert-header"></h4>
				<div id="alert-body"></div>
			</div>
			<div class="modal-footer">
				<a href="#" class="modal-close waves-effect waves-green btn-flat preventDefault">OK</a>
			</div>
		</div>
	</div>
</body>

</html>