<html>
	<head>
		<link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

		<style>
			body {
                background-color: #34495e;
				margin: 0;
				padding: 0;
				width: 100%;
				height: 100%;
				color: #eee;
				display: table;
				font-weight: 100;
				font-family: 'Lato',sans-serif;
			}

			.container {
				text-align: center;
				display: table-cell;
				vertical-align: middle;
			}

			.content {
				text-align: center;
				display: inline-block;
			}

			.title {
				font-size: 96px;
				margin-bottom: 40px;
			}

			.quote {
				font-size: 24px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="title">Welcome to {{{ trans("branding.project-name") }}}</div>
				<div class="quote">Running {{{ $hash }}} on {{{ $branch }}}</div>
			</div>
		</div>
	</body>
</html>
