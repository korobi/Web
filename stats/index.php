<?php

// dependencies
require dirname(__DIR__) . '/app/lib/Core.php';

// grab args
$args = isset($_GET['what']) ? trim($_GET['what']) : '';

// force lowercase
if (strtolower($args) != $args) {
	header("Location: " . strtolower($_SERVER['REQUEST_URI']));
	exit;
}

// pop
$args = explode('/', $args);
$slug = $args[0];

// are we a channel?
if (!empty($args[1])) {
	$chan = $args[1];

	$security_key = '';
	if (true) {
		$key_args = explode('*', $chan);

		// check if this channel requires a key to view the log
		$sec_key = getKeyForChannel($slug . '/' . $key_args[0]);
		if ($sec_key != "NO_KEY") {
			if (!isset($key_args[1])) {
				require __DIR__ . '/auth.php';
				exit;
			}
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
		$chan = $key_args[0];
	}

	require __DIR__ . "/$slug/$chan.html";
	exit;
}

// data
$display_type = null;
$channels_glob = null;
$slug_name = '';
$title_append = '';
$channels = [];
$channel_names = [];

// are we searching for networks or channels?
if (empty($slug)) {
	$display_type = "networks";
	$channels_glob = glob(SBNC_STATS_DIR . "/stats/*", GLOB_ONLYDIR);
} else {
	$display_type = "channels";
	$channels_glob = glob(SBNC_STATS_DIR . "/stats/$slug/*.html");
}

// cycle through
foreach ($channels_glob as $channel) {
	$channel = str_replace(SBNC_STATS_DIR . '/stats/', '', $channel);

	if ($display_type == "channels") {
		$channel = str_replace("$slug/", '', $channel);
		$channel = str_replace(".html", "", $channel);
	}

	if ($channel == 'opers' || $channel == 'qtpi') {
		continue;
	}

	if ($display_type == "networks") {
		$slug_name = getNetworkDisplayNameFromSlug($channel);
	} else {
		$slug_name = getNetworkDisplayNameFromSlug($slug);
		$title_append = ' - ' . $slug_name;
	}

	if (shouldSkipNetworkRender($channel)) {
		continue;
	}

	$channels[] = $channel;
	$channel_names[$channel] = $slug_name;
}

// -----------------------
// ---- begin render ----
// -----------------------
?>
<!DOCTYPE html>
<html lang="en-us" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>qtpi - Channel Stats Directory<?php echo $title_append; ?></title>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono'>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css?<?php echo ASSET_VERSION; ?>">
	<link rel="stylesheet" href="/assets/css/qtpi.min.css?<?php echo ASSET_VERSION; ?>">
</head>
<body>
	<div class="container qtpiwrap">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="/">Home</a></li>
				<li><a href="/logs/">Logs</a></li>
				<li class="active"><a href="/stats/">Stats</a></li>
				<?php if (CUSTCMD_ENABLED) { echo "<li><a href='/commands/''>Commands</a></li>"; } ?>
			</ul>
			<h3>Stats - Directory<?php echo $title_append; ?></h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name">Stats are generated every 5 minutes.</div>
					</div>
				</div>
				<div class="m">
					<table class="table" id="stat">
						<thead>
						<tr>
							<?php echo ($display_type === "networks") ? "<th>Network</th>" : "<th>Channel</th>"; ?>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($channels as $_channel) {
							echo ($display_type === "networks") ?
								"<tr><td><a href='/stats/" . $_channel . "'>" . $channel_names[$_channel] . "</a></td></tr>"
								: "<tr><td><a href=\"/stats/" . $slug . "/" . $_channel . '">#' . $_channel . "</a></td></tr>";
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script src="/assets/js/jquery-2.0.3.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/bootstrap.min.js?<?php echo ASSET_VERSION; ?>"></script>
</body>
</html>
