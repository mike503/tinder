<?php
function _like($uid = '') {
  if ($result = _request('/like/' . $uid)) {
// do some magic here to determine if success?
var_dump($result);
  }
  return FALSE;
}

function _superlike($uid = '') {
  if ($result = _request('/like/' . $uid . '/super')) {
// do some magic here to determine if success?
var_dump($result);
  }
  return FALSE;
}

function _pass($uid = '') {
  if ($result = _request('/pass/' . $uid)) {
// do some magic here to determine if success?
var_dump($result);
  }
  return FALSE;
}

function _user($uid = '') {
  return _request('/user/' . $uid);
}

// set location
function _ping($lat = '', $lon = '') {
  return _request('/user/ping', TRUE, array('lat' => $lat, 'lon' => $lon));
}

// get recommendations
function _recs() {
  return _request('/user/recs');
}

// set profile match preferences
function _profile($filters = array()) {
// trying to force these for the moment...
  $filters['distance_filter'] = 50;
  $filters['gender_filter'] = 1;
  $filters['age_filter_min'] = 18;
  $filters['age_filter_max'] = 37;
  $filters['discoverable'] = 1;
//
  if ($result = _request('/profile', TRUE, $filters)) {
var_dump($result);
  }
  return FALSE;
}

function _token() {
  $token = FALSE;
  $q = core_db_query("SELECT cache_value FROM cache WHERE cache_key='token'");  
  if (core_db_numrows($q) == 1) {
    list($token) = core_db_rows($q);
  }
  core_db_free($q);
  if (!$token) {
    $token = _login();
  }
  if ($token) {
    return $token;
  }
  echo "FATAL: _token(): no token could be found" . PHP_EOL;
  exit(255);
}

// this might create a loop; need to be careful.
function _retoken($uri = '', $post = FALSE, $params = array()) {
  core_db_query("DELETE FROM cache WHERE cache_key='token'");
  _login();
  return _request($uri, $post, $params);
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
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $json = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);
  if ($status == 401) {
    echo "NOTICE: request received HTTP 401, probably due to an expired token" . PHP_EOL;
    return _retoken($uri, $post, $params);
  }
  if ($result = json_decode($json, TRUE)) {
    // might check for $result['status']
    if (isset($result['statusCode']) && $result['statusCode'] == 404) {
      echo "NOTICE: request received custom 404, probably due to an expired token" . PHP_EOL;
      return _retoken($uri, $post, $params);
    }
    return isset($result['results']) ? $result['results'] : $result;
  }
  echo "ERROR: _request(): got HTTP $status from $uri" . PHP_EOL;
  return FALSE;
}

function _login() {
  global $config;
  $params = array(
    'facebook_token' => $config['facebook_token'],
    'locale' => $config['locale'],
  );
  if ($result = _request('/auth', TRUE, $params)) {
    if (isset($result['token'])) {
      core_db_query("REPLACE INTO cache (cache_key, cache_value) VALUES (:key, :value)", array(':key' => 'token', ':value' => $result['token']));
      return $result['token'];
    }
  }
  return FALSE;
}
