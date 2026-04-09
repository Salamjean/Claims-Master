<?php
$res = file_get_contents('models_raw.json');
$data = json_decode($res, true);

echo "Searching for '1.5' anywhere in model names...\n";
foreach ($data['models'] as $m) {
    if (strpos($m['name'], '1.5') !== false) {
        echo " - " . $m['name'] . "\n";
    }
}

echo "\nSearching for '1.0' anywhere in model names...\n";
foreach ($data['models'] as $m) {
    if (strpos($m['name'], '1.0') !== false) {
        echo " - " . $m['name'] . "\n";
    }
}
