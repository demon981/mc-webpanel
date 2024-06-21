<?php
require_once 'inc/lib.php';

session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	header('Location: .');
	exit('Not Authorized');
}

if (empty($_REQUEST['file'])) {
	header('Location: files.php');
	exit('No file specified');
}

if(strpos($_REQUEST['file'], '..') !== false) {
	exit('Invalid file path.');
}

if (isset($_POST['text']) && !empty($_POST['file'])) {
	$file = $user['home'] . $_POST['file'];
	$text = $_POST['text'];
	if (get_magic_quotes_gpc())
		$text = stripslashes($text);
	$saved = file_put_contents($file, $text);
}

$dir = rtrim($_REQUEST['file'], basename($_REQUEST['file']));
$dir = rtrim($dir, '/');

?><!doctype html>
<html>
<head>
	<title>Edycja plikow | MC2U.PL</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Maciej Więcek <contact@mwf.pl>">
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		var edited = false;
		$(document).ready(function () {
			$('textarea').css('height', $(window).height() - 240 + 'px')
				.on('change', function () {
					window.edited = true;
				});
			$('#cancel').click(function () {
				if (window.edited)
					return confirm('Jestes pewny, ze chcesz anulowac edycje?')
				else
					return true;
			});
			$('#reload').click(function () {
				if (window.edited)
					return confirm('Jestes pewny, ze chcesz przeladowac strone?')
				else
					return true;
			});
			window.setTimeout(function () {
				$('.alert').fadeOut();
			}, 4000);
		});
		$(document).resize(function () {
			$('textarea').css('height', $(window).height() - 240 + 'px');
		});
	</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="container-fluid">
	<form action="edit.php" method="post">
		<div class="row-fluid">
			<h3 style="font-weight:400;" class="pull-left">Edycja plikow <?php echo $_REQUEST['file']; ?></h3>
			<?php if (isset($_POST['text']) && $saved !== false) { ?>
				<p class="alert alert-success pull-right"><i class="icon-ok"></i> Plik zostal zapisany</p>
			<?php } elseif (isset($_POST['text'])) { ?>
				<p class="alert alert-error pull-right"><i class="icon-remove"></i> Plik nie jest zapisany!</p>
			<?php } elseif (isset($_GET['action']) && $_GET['action'] == 'reload') { ?>
				<p class="alert alert-info pull-right">Przeladowano</p>
			<?php } ?>
			<div class="clearfix"></div>
			<input type="hidden" name="file" value="<?php echo $_REQUEST['file']; ?>">
			<textarea name="text" style="width:100%;box-sizing:border-box;-moz-box-sizing:border-box;font-family:monospace;"><?php echo htmlspecialchars(file_get_contents($user['home'] . $_REQUEST['file'])); ?></textarea>

			<div class="btn-toolbar" style="text-align: right;">
				<a href="files.php?dir=<?php echo urlencode($dir); ?>" id="cancel" class="btn">Anuluj</a>
				<a href="edit.php?file=<?php echo urlencode($_REQUEST['file']); ?>&action=reload" id="reload" class="btn btn-danger"><i class="icon-repeat icon-white"></i> Przeladuj</a>
				<button type="submit" class="btn btn-primary"><i class="icon-download-alt icon-white"></i> Zapisz</button>
			</div>
		</div>
	</form>
</div>
</body>
</html>
