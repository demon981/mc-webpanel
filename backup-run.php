<?php 

require_once dirname(__FILE__) . '/data/config.php';

if (PHP_SAPI !== 'cli') {
	error_log("Blad polaczenia..");
	exit("Nie mam uprawnien lol \r\n");
}

if(!isset($argv[2])) {
	error_log("Blad polaczenia..");
	exit("Nie mozna zrobic backupa!\r\n");
}

if(!isset($argv[1])) {
	error_log("Blad polaczenia..");
	exit("Nie mozna zrobic backupa!\r\n");
}

if(!isset($argv[3])) {
	error_log("Blad autoryzacji");
	exit("Chyba nie jestes zalogowany!!\r\n");
}

$name = $argv[1];
$secret = $argv[2];
$delete = $argv[3];

if(is_file(dirname(__FILE__) . '/data/users/' . strtolower(preg_replace('/([^A-Za-z0-9\- ])/','',$name)) . '.json')) {
	$user = json_decode(file_get_contents(dirname(__FILE__) . '/data/users/' . strtolower(preg_replace('/([^A-Za-z0-9\- ])/','',$name) . '.json')), true);
} else {
	$user = false;
}

if (!$user) {
	$user = preg_replace('/[^A-Za-z0-9\- ]/', '', $name);
	error_log("X-Backup: '" . $user . "' nie odnaleziono!");
	exit('Not Authorized\r\n');
}

if($secret != hash("sha256", $user['pass'])) {
	error_log("...");
	exit('Brak autoryzacji\r\n');
}

$running = !!strpos(`screen -ls`, KT_SCREEN_NAME_PREFIX . $name);

if(!$running) {
	exit('Serwer nie jest uruchomiony\r\n');
}

server_cmd($user['user'], "save-all");
sleep(30); // usypanie serwera na 30 sekund
server_cmd($user['user'], "save-off");
// tego nie edytuj bo zjebiesz chuju 
server_cmd($user['user'], 'tellraw @a ["",{"text":"[MCBackup]","color":"yellow"},{"text":" "},{"text":"Starting backup!","bold":true,"hoverEvent":{"action":"show_text","value":"You may experience slight performance drops while this takes place."}}]');

if(!is_dir($user['home'] . "/" . "backups")){
	mkdir($user['home'] . "/" . "backups");
}

$timeout = intval($delete) * 60 * 60; // obliczanie sekund

if($timeout !== 0) {
	$backups = array_diff(scandir($user['home'] . "/" . "backups/"), array('.', '..'));
	
	foreach($backups as $backup) {
		$timeCreated = filectime($user['home'] . "/" . "backups/" . $backup);
		//Times up!
		if($timeout + $timeCreated <= time()) {
			unlink($user['home'] . "/" . "backups/" . $backup);
		}
	}
}
try {
	$archiveFile = date('Y-m-d') . " - " . time() . " - mc2uBackup.tar";

	$phar = new PharData($user['home'] . "/" . "backups/" . $archiveFile);
	
	$phar->buildFromDirectory($user['home'], '/^((?!backups).)*$/');
	$phar->compress(Phar::GZ);
	
	unlink($user['home'] . "/" . "backups/" . $archiveFile);
	
} catch (Exception $e) {
	error_log("X-Backup: '" . $user . "' Blad!\r\nPowody: : " . $e);
	exit("Exception : " . $e . "\r\n");
}

server_cmd($user['user'], 'tellraw @a ["",{"text":"[MCBackup] ","color":"yellow"},{"text":"Backup finished!"}]');

server_cmd($user['user'], "save-on");

echo "X-Backup: Pomyslnie stworzono backup glownego serwera MC2U\r\n";

function server_cmd($name,$cmd) {
	shell_exec(
		sprintf(
			KT_SCREEN_CMD_EXEC,
			KT_SCREEN_NAME_PREFIX.$name, 
			str_replace(array('\\','"'),array('\\\\','\\"'),(get_magic_quotes_gpc() ? stripslashes($cmd) : $cmd)) 
		)
	);
}

?>
