<?php
function _like($uid) {
  if ($result = _request('/like/' . $uid)) {
var_dump($result);
    json_decode($result);

  }
}

function _superlike($uid) {
  if ($result = _request('/like/' . $uid . '/super')) {
var_dump($result);
  }
}

function _user($uid) {
  if ($result = _request('/user/' . $uid)) {
var_dump($result);
  }
}

function _pass($uid) {
  if ($result = _request('/pass/' . $uid)) {
var_dump($result);
  }
}

// set location
function _ping($lat = '', $lon = '') {
  if ($result = _request('/user/ping', TRUE, array('lat' => $lat, 'lon' => $lon))) {
var_dump($result);
  }
  return FALSE;
}

// get recommendations
function _recs() {
  if ($result = _request('/user/recs')) {
    if ($results = json_decode($result, TRUE)) {
      if ($results['status'] == 200) {
        return $results['results'];
      }
    }
  }
  return FALSE;
}

// set profile match preferences (right?)
function _profile($filters = array()) {
  $filters['distance_filter'] = 50;
  $filters['gender_filter'] = 1;
  $filters['age_filter_min'] = 18;
  $filters['age_filter_max'] = 37;
  $filters['discoverable'] = 1;
  if ($result = _request('/profile', TRUE, $filters)) {
var_dump($result);
  }
  return FALSE;
}

function _token() {
  $q = core_db_query("SELECT cache_value FROM cache WHERE cache_key='token'");  
  if (core_db_numrows($q) == 1) {
    list($token) = core_db_rows($q);
    core_db_free($q);
    return $token;
  }
  core_db_free($q);
  if ($token = _login()) {
    return $token;
  }
  echo "FATAL: _token(): no token could be found" . PHP_EOL;
  exit(255);
}

function _request($uri = '', $post = FALSE, $params = array()) {
  $headers = array(
    'platform: android',
    'User-Agent: Tinder Android Version 4.3.5',
    'os_version: 22',
    'app-version: 833',
  );
  if ($uri != '/auth') {
    $headers[] .= 'X-Auth-Token: ' . _token();
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.gotinder.com' . $uri);
  if ($post) {
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);
  if ($status == 200) {
    return $result;
  }
  echo "ERROR: _request(): got HTTP $status" . PHP_EOL;
  return FALSE;
}

function _login() {
  global $config;
  $params = array(
    'facebook_token' => $config['facebook_token'],
    'locale' => $config['locale'],
  );
  if ($result = _request('/auth', TRUE, $params)) {
    if ($json = json_decode($result, TRUE)) {
      if (isset($json['token'])) {
        core_db_query("REPLACE INTO cache (cache_key, cache_value) VALUES(:key, :value)", array(':key' => 'token', ':value' => $json['token']));
        return $json['token'];
      }
    }
  }
  return FALSE;
}
