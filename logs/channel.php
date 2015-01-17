<?php

// set timezone
date_default_timezone_set('America/Vancouver');

// grab args
$args = isset($_GET['what']) ? trim($_GET['what']) : '';
$reverse = strrev($args);

// are we not a channel?
if ($reverse{0} == "/") {
	$newLocation = substr($_SERVER['REQUEST_URI'], 0, -1);
	header("Location: $newLocation");
	exit;
}

// lowercase
if (strtolower($args) != $args) {
	header("Location: " . strtolower($_SERVER['REQUEST_URI']));
	exit;
}

$security_key = '';
if (true) {
	$key_args = explode('*', $args);

	// check if this channel requires a key to view the log
	$sec_key = getKeyForChannel($key_args[0]);
	if ($sec_key != "NO_KEY") {
		if ($key_args[1] !== $sec_key) {
			if ($key_args[1] != '') {
				$invalid_security_key = true;
			}

			require __DIR__ . '/auth.php';
			exit;
		}

		$security_key = '*' . $sec_key;
	}

	// we're allowed passage, prepare args for use
	$args = $key_args[0];
}

// pop
$args = explode('/', $args);
$slug = $args[0];
$channel = $args[1];
if (!$channel) {
	header('Location: /logs/');
	exit;
}

// what type of log are we wanting?
$type = isset($args[2]) ? trim($args[2]) : 'latest';
$type_latest = $type === 'latest';
$tail = $type === 'tail';
$tail_length = 31;
if (isset($args[3])) {
	$requested_length = $args[3];
	if ($requested_length > 90) {
		$requested_length = 31;
	}

	$tail_length = $requested_length;
}

// data
$output = '';
$path = '';

// calculate
if ($type_latest || $tail) {
	$_title = date('d/m/Y');
	$path = SBNC_ROOT_DIR . "logs/$slug/$channel/" . date('Y/m/d') . '.log';
} else {
	$newPath = $args[2] . '/' . $args[3] . '/' . $args[4];
	$date = new DateTime($args[4] . '-' . $args[3] . "-" . $args[2]);
	$_title = $date->format('d/m/Y');
	$path = SBNC_ROOT_DIR . "logs/$slug/$channel/" . $newPath . '.log';
}

if (file_exists($path)) {
	$first_color = true;
	$parts = '';

	$lines = null;
	if (!$tail) {
		$lines = file_get_contents($path);
	} else {
		$lines = implode("", getLast30Lines($path, $tail_length));
	}

	foreach (explode("\n", $lines) as $line) {
		/*$first_color = true;
		$parts = '';

			$visibility = 'qt-visible';
			// todo - validation
			if (preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has joined.+/", $line) ||
				preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has quit.+/", $line) ||
				preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has left.+/", $line) ||
				preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ is now known as .+/", $line) ||
				preg_match("/\[\d\d:\d\d:\d\d\] .+\* .* sets mode.*\/", $line)) {
				$visibility = 'can-hide';
			}

		foreach (explode("\003", $line) as $part) {
			if (!$first_color) {
			//	$parts .= '</span';
			} else {
				$first_color = false;
			}

			$fg = 'black';
			$bg = 'none';
			if (strlen($part) > 0) {
				if (is_numeric($part[0])) {
					$fglength = getColor($part, 0);
					if ($fglength > 0) {
						$fg = getColorCode(substr($part, 0, $fglength));
						$part = substr($part, $fglength);
					}

					if ($part[0] == ",") {
						$bg_length = getColor($part, 1);
						if ($bg_length > 0) {
							$bg = getColorCode(substr($part, 1, $bg_length));
							$part = substr($part, $bg_length + 1);
						}
					}
				}
			}

			$parts .= "<span class='$visibility' style=\"color: $fg; background-color: $bg\">$part</span>";
		}

		$_output = '';
		$bold = false;
		$first = true;
		foreach (explode("\002", $parts) as $part) {
			if (!$first) {
				if ($bold) {
					$_output .= "</strong>";
				} else {
					$_output .= "<strong>";
				}

				$bold = !$bold;
			}

			$first = false;
			$_output .= $part;
		}

		if ($bold) {
			$_output .= "</strong>";
		}

		//echo $_output . "<br/>";
		$output .= $_output . "<br class='$visibility' />";*/
		$output .= lineParse($line);
	}
} else {
	$_output = 'no log found';
	$output .= $_output;
}

// listing
$modal_output = "";
$first_year = true;
$first_month = true;
foreach (scandir(SBNC_ROOT_DIR . "logs/$slug/$channel") as $year) {
	if (strpos($year, ".") === 0) {
		continue;
	}

	if ($first_year) {
		$modal_output .= "<div class='year visible'>";
		$first_year = false;
	} else {
		$modal_output .= "<div class='year'>";
	}

	$modal_output .= "<h4><span class='label label-primary'>$year</span></h4>";
	$modal_output .= "<div class='qtpi-months'>";
	foreach (scandir(SBNC_ROOT_DIR . "logs/$slug/$channel/$year") as $month) {
		if (strpos($month, ".") === 0) {
			continue;
		}

		if ($first_month) {
			$modal_output .= "<div class='qtpi-month qtpi-visible'>";
			$first_month = false;
		} else {
			$modal_output .= "<div class='qtpi-month'>";
		}

		if ($month == "12") {
			$month_name = $month;
		}

		switch ($month) {
			case "01":
				$month_name = "January";
				break;
			case "02":
				$month_name = "February";
				break;
			case "03":
				$month_name = "March";
				break;
			case "04":
				$month_name = "April";
				break;
			case "05":
				$month_name = "May";
				break;
			case "06":
				$month_name = "June";
				break;
			case "07":
				$month_name = "July";
				break;
			case "08":
				$month_name = "August";
				break;
			case "09":
				$month_name = "September";
				break;
			case "10":
				$month_name = "October";
				break;
			case "11":
				$month_name = "November";
				break;
			case "12":
				$month_name = "December";
				break;
		}

		$modal_output .= "<h5><span class='label label-success'>$month_name</span></h5>";

		// days
		$modal_output .= "<div class='qtpi-day'>";
		foreach (scandir(SBNC_ROOT_DIR . "logs/$slug/$channel/$year/$month") as $day) {
			if (strpos($day, ".") === 0) {
				continue;
			}

			$day = explode(".", $day)[0];
			$modal_output .= "<a href=\"/logs/$slug/$channel/$year/$month/$day/$security_key\"><span class='badge'>$day</span></a>";
			$modal_output .= " ";
			$_date = "$day/$month/$year";
		}

		$modal_output .= "</div>";
	}

	$modal_output .= "</div></div>";
}

// prepare links
$link_tail = null;
if ($type_latest) {
	$split_url = explode('*', $_SERVER["REQUEST_URI"]);

	$link_tail = $split_url[0] . '/tail';

	// check for key
	if ($split_url[1]) {
		$link_tail .= '*' . $split_url[1];
	}
} else if ($tail) {
	$link_tail = $_SERVER["REQUEST_URI"];
}

$the_magical_title = 'Log';
if ($tail) {
	$the_magical_title = 'Log Tail';
}

// -----------------------
// ---- begin render ----
// -----------------------
?>
<!DOCTYPE html>
<html lang="en-us" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>qtpi - <?php echo $the_magical_title; ?> for #<?php echo $channel; ?></title>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono'>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css?<?php echo ASSET_VERSION; ?>">
	<link rel="stylesheet" href="/assets/css/qtpi.min.css?<?php echo ASSET_VERSION; ?>">
</head>
<body>
	<div class="container qtpiwrap">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="/">Home</a></li>
				<li class="active"><a href="/logs/<?php echo $slug; ?>">Logs</a></li>
				<li class="external"><a href="http://qts.vq.lc/#/dashboard/elasticsearch/qtpi%20-%20IRC%20Log%20Search">Search Logs</a>
				<li><a href="/stats/">Stats</a></li>
				<?php if (CUSTCMD_ENABLED) { echo "<li><a href='/commands/''>Commands</a></li>"; } ?>
			</ul>
			<h3><?php echo $the_magical_title; ?> for #<?php echo $channel; ?> on <?php echo $_title; ?></h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name"><a href="/logs/<?php echo $slug; ?>/<?php echo $channel; ?>" class="qtpi-no-ugly"><span class="label label-success">Latest</span></a> <a href="#" data-toggle="modal" data-target="#moreModal" class="qtpi-no-ugly"><span class="label label-danger">Archive</span></a></div>
						<div class="name"><span class="label label-info" id="timezone-notice">*** Please note that times are in "America/Vancouver" timezone.</span></div>
						<div class="name"><button class="btn btn-small label label-warning" id="toggle" data-toggle="tooltip" data-title="This is stored using a cookie.">Click to toggle join/part/mode/nick messages</button></div>
						<div class="name"><a href="<?php echo $link_tail; ?>"><span class="label label-info">Tail</span></a></div>
					</div>
				</div>
				<div class="m">
					<div style="font-family: 'Courier New', monospace; margin-bottom: 10px;">
						<?php echo $output; ?>
					</div>
				</div>
			</div>
			<a name="bottom" id="bottom"></a>
		</div>
		<div class="modal fade" id="moreModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Log History</h4>
					</div>
					<div class="modal-body">
						<p><?php echo $modal_output; ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="/assets/js/jquery-2.0.3.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/bootstrap.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/moment.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/jquery.cookie.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/log.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script>$.fn.extend({
		scrollToMe: function() {
			var x = $(this).offset().top - 100;
			$('html,body').animate({scrollTop: x}, 400);
		}
	});</script>
	<?php
	if ($tail) {
		echo '<script>$(document).ready(function(){setTimeout(function(){location.reload()},6000);});$(function() {$(document).scrollTop($("#bottom").offset().top);});</script>';
	}
	?>
	<!-- <?php echo GIT_HASH; ?> -->
</body>
</html>
