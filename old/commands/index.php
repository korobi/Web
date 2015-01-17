<?php

// dependencies
require dirname(__DIR__) . '/app/lib/Core.php';
//require __DIR__ . '/qtpi.php';

// grab args
$args = isset($_GET['what']) ? trim($_GET['what']) : '';

// pop
$args = explode('/', $args);
$slug = $args[0];

if (!empty($args[1])) {
	$slug_name = getNetworkDisplayNameFromSlug($slug);

	require __DIR__ . '/channel.php';
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
	$channels_glob = glob(SBNC_ROOT_DIR . "commands/*", GLOB_ONLYDIR);
} else {
	$display_type = "channels";
	$channels_glob = glob(SBNC_ROOT_DIR . "commands/$slug/*", GLOB_ONLYDIR);
}

// cycle through
foreach ($channels_glob as $channel) {
	$channel = str_replace(SBNC_ROOT_DIR . 'commands/', '', $channel);
	if ($display_type == "channels") {
		$channel = str_replace("$slug/", '', $channel);
	}

	if ($display_type == "networks") {
		$slug_name = getNetworkDisplayNameFromSlug($channel);
	} else {
		$slug_name = getNetworkDisplayNameFromSlug($slug);
		$title_append = $slug_name;
	}

	$channels[] = $channel;
	$channel_names[$channel] = $slug_name;
}

?>
<!DOCTYPE html>
<html lang="en-us" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>qtpi - Command Directory<?php echo $title_append; ?></title>
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
				<li><a href="/stats/">Stats</a></li>
				<li class="active"><a href="/commands/">Commands</a></li>
			</ul>
			<h3>Commands - Directory<?php echo $title_append; ?></h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name">If you want custom commands in your channel, contact Kashike in #qtpi on EsperNet</div>
					</div>
				</div>
				<div class="m">
					<table class="table" id="cmd">
						<thead>
						<tr>
							<?php echo ($display_type === "networks") ? "<th>Network</th>" : "<th>Channel</th>"; ?>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($channels as $channel) {
							if ($display_type === "networks") {
								echo "<tr><td><a href='/commands/" . $channel . "'>" . $channel_names[$channel] . "</a></td></tr>";
							} else {
								echo "<tr><td><a href='/commands/" . $slug . "/" . $channel . "'>#" . $channel . "</a></td></tr>";
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
	<script src="/assets/js/bootstrap.min.js<?php echo $assetVersion; ?>"></script>
	<script>
		$('#cmdTab a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});
	</script>
</body>
</html>
