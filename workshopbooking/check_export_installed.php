<?php
define('NO_OUTPUT_BUFFERING', true);
require(__DIR__ . '/../../config.php');
echo "OK - workshopbooking/exportuserics.php install check\n";
echo "File exists: " . (file_exists(__DIR__ . "/exportuserics.php") ? "yes" : "no") . "\n";
echo "CMID param required. Example: /mod/workshopbooking/exportuserics.php?id=358\n";
