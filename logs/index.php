<?php

// dependencies
require dirname(__DIR__) . '/app/lib/Core.php';

// grab args
$args = isset($_GET['what']) ? trim($_GET['what']) : '';

// pop
$args = explode('/', $args);
$slug = $args[0];

// are we a channel?
if (!empty($args[1])) {
	require __DIR__ . '/channel.php';
	exit;
}

// data
$display_type = null;
$channels_glob = null;
$slug_name = "";
$title_append = "";
$channels = [];
$channel_names = [];

// are we searching for networks or channels?
if (empty($slug)) {
	$display_type = "networks";
	$channels_glob = glob(SBNC_ROOT_DIR . "logs/*", GLOB_ONLYDIR);
} else {
	$display_type = "channels";
	$channels_glob = glob(SBNC_ROOT_DIR . "logs/$slug/*", GLOB_ONLYDIR);
}

// cycle through
foreach ($channels_glob as $channel) {
	$channel = str_replace(SBNC_ROOT_DIR . "logs/", '', $channel);
	if ($display_type == "channels") {
		$channel = str_replace("$slug/", '', $channel);
	}

	if ($display_type == "networks") {
		$slug_name = getNetworkDisplayNameFromSlug($channel);
		$title_append = '';
	} else {
		$slug_name = getNetworkDisplayNameFromSlug($slug);
		$title_append = $slug_name;
	}

	if ($channel == "comkid'") {
		continue;
	}

	if (shouldSkipNetworkRender($channel)) {
		continue;
	}
	if (shouldSkipChannelRender($slug . '/' . $channel)) {
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
	<title>qtpi - Channel Log Directory <?php if ($title_append != '') {echo 'for ';} echo $title_append; ?></title>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono'>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css?<?php echo ASSET_VERSION; ?>">
	<link rel="stylesheet" href="/assets/css/qtpi.min.css?<?php echo ASSET_VERSION; ?>">
</head>
<body>
	<div class="container qtpiwrap">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="/">Home</a></li>
				<li class="active"><a href="/logs/">Logs</a></li>
				<li class="external"><a href="http://qts.vq.lc/#/dashboard/elasticsearch/qtpi%20-%20IRC%20Log%20Search">Search Logs</a>
				<li><a href="/stats/">Stats</a></li>
				<?php if (CUSTCMD_ENABLED) { echo "<li><a href='/commands/''>Commands</a></li>"; } ?>
			</ul>
			<h3>Logs - Directory<?php if ($title_append != '') {echo ' - ' . $title_append;} ?></h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name">If you want your channel logged here, contact Kashike in #qtpi on EsperNet</div>
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
							foreach ($channels as $channel) {
								if ($display_type === "networks") {
									echo "<tr><td><a href='/logs/" . $channel . "'>" . $channel_names[$channel] ."</a></td></tr>";
								} else {
									echo "<tr><td><a href=\"/logs/" . $slug . "/" . $channel . "\">#" . $channel . "</a></td></tr>";
								}
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
