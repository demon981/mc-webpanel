<?php // Konfiguracja panelu zarzadzania

// Adres IP serwera xD
define('KT_LOCAL_IP','127.0.0.1');

define('KT_SCREEN_NAME_PREFIX','mchp-');

define('KT_UPDATE_URL_MC','http://s3.amazonaws.com/MinecraftDownload/launcher/minecraft_server.jar');
define('KT_UPDATE_URL_CB','http://dl.bukkit.org/latest-rb/craftbukkit.jar');

// komendy do konsoli z Screen'a 
define('KT_SCREEN_CMD_START','/usr/bin/screen -dmS %s /usr/bin/java -Xincgc -Xms%sM -Xmx%sM -jar craftbukkit.jar nogui');
define('KT_SCREEN_CMD_EXEC','/usr/bin/screen -S %s -p 0 -X stuff "%s$(printf \\\\r)"');
define('KT_SCREEN_CMD_KILL','/usr/bin/screen -X -S %s quit');
define('KT_SCREEN_CMD_KILLALL','killall /usr/bin/screen');
define('KT_SCREEN_CMD_KILLALL_USER','for session in $(/usr/bin/screen -ls | /bin/grep -o \'[0-9]*\\.%s\'); do /usr/bin/screen -S "${session}" -X quit; done');
