<?php

$channel = isset($_GET['what']) ? trim($_GET['what']) : '';
//$channel = str_replace('/bnc/commands/', '', $channel);
if (!$channel) {
	header('Location: /commands/');
	exit;
}

$channel = $args[1];

// validate our existance
$cmd_dir = SBNC_ROOT_DIR . "commands/$slug/$channel/";
if (!is_dir($cmd_dir)) {
	print 'Channel ' . $channel . ' not found.';
	exit;
}

// link
$the_link = null;
if (is_link(SBNC_ROOT_DIR . "commands/$slug/$channel")) {
	$the_link = readlink(SBNC_ROOT_DIR . "commands/$slug/$channel");
}

// data
$custom_commands = [];
$command_list = [];

// cycle through
foreach (glob($cmd_dir . '*.alias') as $filename) {
	$command = substr(basename($filename), 0, -6);
	$text = trim(file_get_contents($filename));
	$acommand = substr($text, 0, -4);
	if (isset($custom_commands[$acommand])) {
		$custom_commands[$acommand] .= ', ' . $command;
	} else {
		$custom_commands[$acommand] = $command;
	}
}

$command_list['dynamic'] = '';
foreach (glob($cmd_dir . '*.cmd') as $filename) {
	$command = substr(basename($filename), 0, -4);
	$text = trim(file_get_contents($filename));
	//$text = $format->parse($text);
	//$text = Bouncer::convertColors($text);

	$first_color = true;
	$output = '';
	foreach (explode("\003", $text) as $part) {
		if (!$first_color) {
			//	$output .= '</span';
		} else {
			$first_color = false;
		}

		$fg = 'black';
		$bg = 'none';
		if (strlen($part) > 0) {
			if (is_numeric($part[0])) {
				$fg_length = Color::getColor($part, 0);
				if ($fg_length > 0) {
					$fg = Color::getColorCode(substr($part, 0, $fg_length));
					$part = substr($part, $fg_length);
				}

				if ($part[0] == ",") {
					$bg_length = Color::getColor($part, 1);
					if ($bg_length > 0) {
						$bg = Color::getColorCode(substr($part, 1, $bg_length));
						$part = substr($part, $bg_length + 1);
					}
				}
			}
		}

		$output .= "<span style=\"color: $fg; background-color: $bg\">$part</span>";
	}

	$text = $output;
	$text = str_replace('\n', "<br />", $text);
	$aliases = '';
	if (isset($custom_commands[$command])) {
		$aliases = $custom_commands[$command];
	} else {
		$aliases = '[none]';
	}

	// append data
	$command_list['dynamic'] .= '<tr>';
	$command_list['dynamic'] .= '<td>' . CUSTCMD_PREFIX . $command . '</td>';
	$command_list['dynamic'] .= '<td>' . $aliases . '</td>';
	$command_list['dynamic'] .= '<td>' . $text . '</td>';
	$command_list['dynamic'] .= '</tr>' . "\n";
}

$command_list['internal'] = '';
foreach (glob($cmd_dir . '*.proc') as $filename) {
	$command = substr(basename($filename), 0, -5);
	$text = file_get_contents($filename);

	// append data
	$command_list['internal'] .= '<tr>';
	$command_list['internal'] .= '<td>' . CUSTCMD_PREFIX . $command . '</td>';
	$command_list['internal'] .= '<td>' . $text . '</td>';
	$command_list['internal'] .= '</tr>' . "\n";
}

// empty?
if ($command_list['dynamic'] === '') {
	$command_list['dynamic'] = '<tr><td>No data available</td></tr>';
}

if ($command_list['internal'] === '') {
	$command_list['internal'] = '<tr><td>No data available</td></tr>';
}

// -----------------------
// ---- begin render ----
// -----------------------
?>
<!DOCTYPE html>
<html lang="en-us" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>qtpi - Custom Commands &raquo; #<?php echo $channel; ?> @ <?php echo $slug_name; ?></title>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono'>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css?<?php echo ASSET_VERSION; ?>">
	<link rel="stylesheet" href="/assets/css/qtpi.min.css?<?php echo ASSET_VERSION; ?>">
</head>
	<div class="container qtpiwrap">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="/commands/<?php echo $slug; ?>" id="back">Back</a></li>
				<li class="active"><a href="#custom" data-toggle="tab" id="command-toggle" class="tabbable">Custom</a></li>
				<li><a href="#dynamic" data-toggle="tab" id="command-toggle" class="tabbable">Dynamic</a></li>
			</ul>
			<h3>Commands - #<?php echo $channel; ?> @ <?php echo $slug_name; ?></h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name">If you want custom commands in your channel, contact Kashike in #qtpi on EsperNet.</div>
						<?php if ($the_link !== null) { echo '<span class="label label-success">The commands in this channel are linked to #' . $the_link . '</span>'; } ?>
					</div>
				</div>
				<div class="m">
					<div id="command-toggleContent" class="tab-content">
						<div class="tab-pane fade in active" id="custom">
							<table class="table" id="cmd">
								<thead>
								<tr>
									<th>Command</th>
									<th>Aliases</th>
									<th>Text</th>
								</tr>
								</thead>
								<tbody>
								<?php echo $command_list['dynamic']; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="dynamic">
							<table class="table">
								<thead>
								<tr>
									<th>Command</th>
									<th>Internal Command</th>
								</tr>
								</thead>
								<tbody>
								<?php echo $command_list['internal']; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="/assets/js/jquery-2.0.3.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/bootstrap.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script>
		$('#command-toggle a').click(function(e) {
			e.preventDefault();
			$(this).tab('show');
		});
	</script>
</body>
</html>
