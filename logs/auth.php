<?php
// -----------------------
// ---- begin render ----
// -----------------------
?>
<!DOCTYPE html>
<html lang="en-us" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>qtpi - Error</title>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono'>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css?<?php echo ASSET_VERSION; ?>">
	<link rel="stylesheet" href="/assets/css/qtpi.min.css?<?php echo ASSET_VERSION; ?>">
</head>
<body>
	<div class="container qtpiwrap">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="/">Home</a></li>
				<?php /* <li><a href="/commands/">Commands</a></li> */ ?>
				<li class="active"><a href="/logs/">Logs</a></li>
				<li><a href="/stats/">Stats</a></li>
			</ul>
			<h3>Error</h3>
		</div>
		<div class="qtpientries">
			<div class="qtpi" id="km1">
				<div class="t">
					<div class="pull-left">
						<div class="avatar"><img src="/assets/images/avvy.jpg?<?php echo ASSET_VERSION; ?>" height="32" width="32" alt="avatar"></div>
						<div class="name">mew</div>
					</div>
				</div>
				<div class="m">
					The channel log you are attempting to view requires a security code.
<?php
if ($invalid_security_key) {
	echo ' The security code you provided was invalid.';
}
?>
				</div>
			</div>
		</div>
	</div>

	<script src="/assets/js/jquery-2.0.3.min.js?<?php echo ASSET_VERSION; ?>"></script>
	<script src="/assets/js/bootstrap.min.js?<?php echo ASSET_VERSION; ?>"></script>
</body>
</html>
