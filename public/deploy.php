<?php

// Forked from https://gist.github.com/1809044
// Available from https://gist.github.com/nichtich/5290675#file-deploy-php

$TITLE   = 'Git Deployment with webhook';
$VERSION = '0.11';

echo <<<EOT
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>$TITLE</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
  o-o    $TITLE
 /\\"/\   v$VERSION
(`=*=') 
 ^---^`-.


EOT;

// Check whether client is allowed to trigger an update

$allowed_ips = array(
    // GitHub webhook IPs (as of 2026)
    '140.82.112.', '143.55.64.', '192.30.252.', '185.199.108.', '140.82.115.',
    '143.55.65.', '143.55.66.', '143.55.67.', '143.55.68.', '143.55.69.',
	'104.34.226.62', // jk home
);
$allowed = false;

$headers = apache_request_headers();

if (@$headers["X-Forwarded-For"]) {
    $ips = explode(",",$headers["X-Forwarded-For"]);
    $ip  = $ips[0];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

foreach ($allowed_ips as $allow) {
    if (stripos($ip, $allow) !== false) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
	header('HTTP/1.1 403 Forbidden');
 	echo "<span style=\"color: #ff0000\">Sorry, no hamster - better convince your parents!</span>\n";
    echo "</pre>\n</body>\n</html>";
    exit;
}

flush();

// Actually run the update11

$commands = array(
    'echo $PWD',
	'whoami',
    'cd .. && php artisan down',
	'cd .. && git reset --hard HEAD',
	'cd .. && git pull',
	'cd .. && git status',
	// 'git submodule sync',
	// 'git submodule update',
	// 'git submodule status',
    // 'test -e /usr/share/update-notifier/notify-reboot-required && echo "system restart required"',
    // 'git pull origin main',
    // 'export COMPOSER_HOME="$HOME/.config/composer"',
    'export COMPOSER_HOME="$HOME/.config/composer" && cd .. && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader',
    'cd .. && npm install',
    'cd .. && ./node_modules/.bin/vite build',
    'cd .. && php artisan migrate --force',
    'cd .. && php artisan config:clear',
    'cd .. && php artisan route:clear',
    'cd .. && php artisan view:clear',
    'cd .. && php artisan cache:clear',
    // 'cd .. && php artisan queue:restart',
    // 'cd .. && php artisan queue:work --daemon &',
    'cd .. && php artisan up'
);

$output = "\n";

$log = "####### ".date('Y-m-d H:i:s'). " #######\n";

foreach($commands AS $command){
    // Run it
    $tmp = shell_exec("$command 2>&1");
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";

    $log  .= "\$ $command\n".trim($tmp)."\n";
}

$log .= "\n";

file_put_contents ('deploy-log.txt',$log,FILE_APPEND);

echo $output; 

?>
</pre>
</body>
</html>
