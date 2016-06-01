#!/usr/bin/env php
<?php
require __DIR__ . '/config.php';
require __DIR__ . '/database.php';
require __DIR__ . '/tinder.php';

$recs = _recs();
if (!empty($recs)) {
  foreach ($recs as $rec) {
    echo $rec['_id'] . PHP_EOL;
    core_db_query("INSERT INTO profiles (profile_id, profile_json, profile_refreshed) VALUES(:id, :json, UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE profile_json=:json, profile_refreshed=UNIX_TIMESTAMP(NOW())", array(':id' => $rec['_id'], ':json' => json_encode($rec)));
  }
}

