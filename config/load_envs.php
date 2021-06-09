<?php

declare(strict_types=1);

//$projectDir = dirname(__DIR__);
$projectDir = dirname(__DIR__);
$envFiles = array_filter(scandir($projectDir), static function(string $filename): bool {
    // Find .env, .env.local, .env.$APP_ENV & .env.$APP_ENV.local files
    return preg_match('/^\.env(\.\w+)?(\.local)?$/', $filename) === 1;
});

$config = [];
foreach ($envFiles as $envFile) {
    $filename = sprintf('%s/%s', $projectDir, $envFile);

    if ($file = fopen($filename, 'rb')) {
        while(!feof($file)) {
            $line = trim(fgets($file));
            if (!empty($line) && $line[0] !== '#') {
                $configLine = explode('=', $line, 2);
                $config[$configLine[0]] = end($configLine);
            }
        }
        fclose($file);
    }
}

return $config;
