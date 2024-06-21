<?php
require_once 'inc/lib.php'; // To jest include do poprawnego dzialania strony

if (!empty($_POST['user'])) {
	session_start();
	user_add($_POST['user'], $_POST['pass'], 'admin', $_POST['dir'], $_POST['ram'], $_POST['port']);
	file_put_contents(".installed", "");
	$_SESSION['user'] = clean_alphanum($_POST['user']);
}

?><!doctype html>
<html lang="pl" >
<head>
	<title>Uaktywnianie panelu MC2U</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<meta name="author" content="Maciej Więcek <contact@mwf.pl>">
</head>
<body>
<?php if (is_file(".installed")) { ?>
	<div class="modal">
		<div class="modal-header">
			<h3>Instalacja</h3>
		</div>
		<div class="modal-body">
			<p>Panel jest zainstalowany</p>

			<p class="alert alert-info">Jesteś pewny, że nie jest zainstalowany? <code>.installed</code> Usuń ten plik.</p>
		</div>
		<div class="modal-footer">
			<a class="btn btn-success" href="dashboard.php">Przejdz dalej</a>
		</div>
	</div>
<?php } elseif (!empty($_POST['user'])) { ?>
	<div class="modal">
		<div class="modal-header">
			<h3>Instalacja</h3>
		</div>
		<div class="modal-body">
			<p>Panel został zainstalowany, teraz się zaloguj!.</p>
		</div>
		<div class="modal-footer">
			<a class="btn btn-success" href="dashboard.php">Przejdz dalej</a>
		</div>
	</div>
<?php } else { ?>
	<form class="modal form-horizontal" action="install.php" method="post">
		<div class="modal-header">
			<h3>Instalacja</h3>
		</div>
		<div class="modal-body">
			<legend>Admin</legend>
			<div class="control-group">
				<label class="control-label" for="user">Nazwa użytkownika</label>

				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input class="span2" type="text" name="user" id="user">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pass">Hasło</label>

				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-lock"></i></span>
						<input class="span2" type="password" name="pass" id="pass">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dir">Z:?</label>

				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-folder-open"></i></span>
						<input class="span2" type="text" name="dir" id="dir" value="<?php echo strtr(dirname(__FILE__), '\\', '/'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="ram">Pamięć</label>

				<div class="controls">
					<div class="input-append">
						<input class="span3" type="number" name="ram" id="ram" value="512">
						<span class="add-on">MB</span>
					</div>
					<span class="text-info">0 MB = Brak serwera</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="port">Port Minecraft</label>

				<div class="controls">
					<input class="span3" type="number" name="port" id="port" value="25565">
					<span class="text-info">0 = Błąd</span>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="submit">Instaluj i loguj mnie.</button>
		</div>
	</form>
<?php } ?>
</body>
