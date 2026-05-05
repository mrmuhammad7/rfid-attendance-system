<?php
// Returns the last scanned UID that hasn't been "consumed" yet
// Used by admin Add/Edit pages to auto-fill the UID field
require_once __DIR__ . '/../includes/db.php';
corsHeaders();

$cacheFile = sys_get_temp_dir() . '/rfid_last_scan.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 60) {
        header('Content-Type: application/json');
        echo file_get_contents($cacheFile);
    } else {
        jsonResponse(['uid' => null]);
    }
    exit;
}

// DELETE: consume/clear the pending scan
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (file_exists($cacheFile)) unlink($cacheFile);
    jsonResponse(['status' => 'cleared']);
}
