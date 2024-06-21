<?php
require_once 'inc/lib.php';

session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	header('Location: .');
	exit('Brak autoryzacji, logowania');
}

?><!doctype html>
<html>
<head>
	<title>Konsola | MC2U.PL</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Maciej Więcek <contact@mwf.pl>">
	<style type="text/css">
		form {
			margin: 0;
		}
	</style>
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function refreshLog() {
			updateStatus();
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				if ($('#log').scrollTop() == $('#log')[0].scrollHeight) {
					$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				} else {
					$('#log').html(data);
				}
				window.setTimeout(refreshLog, 1000);
			});
		}

		function refreshLogOnce() {
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
			});
		}

		function updateStatus() {
			$.post('ajax.php', {
				req: 'server_running'
			}, function (data) {
				if (data) {
					$('#cmd').prop('disabled', false);
				} else {
					$('#cmd').prop('disabled', true);
				}
			}, 'json');
		}

		$(document).ready(function () {
			$('#frm-cmd').submit(function () {
				$.post('ajax.php', {
					req: 'server_cmd',
					cmd: $('#cmd').val()
				}, function () {
					$('#cmd').val('').prop('disabled', false).focus();
					refreshLogOnce();
				});
				$('#cmd').prop('disabled', true);
				return false;
			});

			$('#log').css('height', $(window).height() - 200 + 'px');

			updateStatus();

			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				window.setTimeout(refreshLog, 1000);
			});
			$(document).resize(function () {
				$('#log').css('height', $(window).height() - 200 + 'px');
			});

		});
	</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="tab-content">
	<div class="tab-pane active">
		<?php if (!empty($user['ram'])) { ?>
			<pre id="log" class="well well-small"></pre>
			<form id="frm-cmd">
				<input type="text" id="cmd" name="cmd" maxlength="250" autofocus>
			</form>
		<?php
			} else {
				echo '<p class="alert alert-info">Nie odnaleziono serwera! XD</p>';
			}
		?>
	</div>
</div>
</body>
</html>
