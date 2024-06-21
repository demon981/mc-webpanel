<?php
require_once 'inc/lib.php';

session_start();

if (!empty($_SESSION['user'])) {

	if (!$user = user_info($_SESSION['user'])) {
		header('Location: .');
		exit('Not Authorized');
	}

} elseif (!empty($_POST['user']) && !empty($_POST['pass'])) {
	$user = user_info($_POST['user']);

	$_SESSION['is_admin'] = $user['role'] == 'admin';

	if (!$user || !bcrypt_verify($_POST['pass'], $user['pass'])) {
		header('Location: ./?error=badlogin');
		exit('Not Authorized');
	}
	$_SESSION['user'] = $user['user'];

} else {

	header('Location: .');
	exit('Brak autoryzacji, logowania');

}
?><!doctype html>
<html>
<head>
	<title>Zarzadzanie serwerem | MC2U.PL</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Maciej Więcek <contact@mwf.pl>">
	<style type="text/css">
		#cmd {
			height: 30px;
		}
		form {
			margin: 0;
		}
	</style>
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function updateStatus(once) {
			$.post('ajax.php', {
				req: 'server_running'
			}, function (data) {
				if (data) {
					$('#lbl-status').text('Running').addClass('label-success').removeClass('label-important');
					$('#btn-srv-start').prop('disabled', true);
					$('#btn-srv-stop,#btn-srv-restart').prop('disabled', false);
					$('#cmd').prop('disabled', false);
				} else {
					$('#lbl-status').text('Stopped').addClass('label-important').removeClass('label-success');
					$('#btn-srv-start').prop('disabled', false);
					$('#btn-srv-stop,#btn-srv-restart').prop('disabled', true);
					$('#cmd').prop('disabled', true);
				}
			}, 'json');
			if (!once)
				window.setTimeout(updateStatus, 5000);
		}
		function updatePlayers() {
			$.post('ajax.php', {
				req: 'players'
			}, function (data) {
				if (data.error) {
					$('#lbl-players').text('Unknown').attr('title', 'Enable Query in server.properties to see player information').tooltip();
				} else {
					try{
						console.log(data);
					} catch(ex) {}

					if(data.players === false) {
						$('#lbl-players').text(0 + '/' + data.info.MaxPlayers);
					} else {
						$('#lbl-players').text(data.players.length + '/' + data.info.MaxPlayers);
						$('#lbl-players').append('<br/><br/>');
						$('#lbl-players').append('<legend>Player List</legend>');
					}
					$.each(data.players, function (i, val) {
						console.log(val);
						$('#lbl-players').append('<img src="inc/getFace.php?username=' + val + '&amp;size=24"> ' + val + '<br>');
					});
				}
			}, 'json').error(function(){
				$('#lbl-players').text('Error');
			});
		}
		function server_start() {
			$.post('ajax.php', {
				req: 'server_start'
			}, function () {
				updateStatus(true);
			});
		}
		function server_stop(callback) {
			$.post('ajax.php', {
				req: 'server_stop'
			}, function () {
				updateStatus(true);
				if (callback)
					callback();
			});
		}
		function set_jar() {
			$.post('ajax.php', {
				req: 'set_jar',
				jar: $('#server-jar').val()
			});
		}
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
				window.setTimeout(refreshLog, 3000);
			});
		}
		function refreshLogOnce() {
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
			});
		}
		$(document).ready(function () {
			updateStatus();
			updatePlayers();
			$('button.ht').tooltip();
			$('#btn-srv-start').click(function () {
				server_start();
				$(this).prop('disabled', true).tooltip('hide');
			});
			$('#btn-srv-stop').click(function () {
				server_stop();
				$(this).prop('disabled', true).tooltip('hide');
			});
			$('#btn-srv-restart').click(function () {
				server_stop(server_start);
				$('').prop('disabled', true).tooltip('hide');
			});

			// Send commands with form onSubmit
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

			// Handle JAR change
			$('#server-jar').change(set_jar);

			// Fix sizing
			$('#log').css('height', $(window).height() - 200 + 'px');

			// Initialize log
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				window.setTimeout(refreshLog, 3000);
			});

			// Keep sizing correct
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
			<div class="row-fluid">
				<div class="span5">
					<div class="well">
						<legend>Kontrolowanie Serwera</legend>
						<div class="btn-toolbar">
							<div class="btn-group">
								<button class="btn btn-large btn-primary ht" id="btn-srv-start" title="Start" disabled><i class="icon-play"></i></button>
								<button class="btn btn-large btn-danger ht" id="btn-srv-stop" title="Stop" disabled><i class="icon-stop"></i></button>
							</div>
							<div class="btn-group">
								<button class="btn btn-large btn-warning ht" id="btn-srv-restart" title="Restart" disabled><i class="icon-refresh"></i></button>
							</div>
						</div>
						<br>
						<p>Server JAR</p>
						<select id="server-jar">
							<?php
								$jars = scandir($user['home']);
								foreach($jars as $file) {
									if(substr($file, -4) == '.jar') {
										if((!empty($user['jar']) && $user['jar'] == $file) || (empty($user['jar']) && $file == 'craftbukkit.jar')) {
											echo "<option value=\"$file\" selected>$file</option>";
										} else {
											echo "<option value=\"$file\">$file</option>";
										}
									}
								}
							?>
						</select>
					</div>
					<div class="well">
						<legend>Informacje o Serwerze:</legend>
						<p><b>Status:</b> <span class="label" id="lbl-status">Sprawdzanie..&hellip;</span><br>
							<b>Adres IP:</b> <?php echo KT_LOCAL_IP . ':' . $user['port']; ?><br>
							<b>Aktualny RAM</b> <?php echo $user['ram'] . 'MB'; ?><br>
							<b>Gracze:</b> <span id="lbl-players">Sprawdzanie..&hellip;</span>
						</p>
						<div class="player-list"></div>
					</div>
					<footer class="muted">&copy; <?php echo date('Y'); ?> by MC2U.PL</footer>
				</div>
				<div class="span7">
					<pre id="log" class="well well-small"></pre>
					<form id="frm-cmd">
						<input type="text" id="cmd" name="cmd" maxlength="250" placeholder="Enter a command" autofocus>
					</form>
				</div>
			</div>
		<?php
		} else
			echo '
			<p class="alert alert-info">Blad autoryzacji.</p>
			<footer class="muted">&copy; ' . date('Y') . ' by MC2U.PL</footer>
';
		?>
	</div>
</div>
</body>
</html>
