<?php
function core_db_affected($stmt) {
  if ($stmt instanceOf PDOStatement) {
    return $stmt->rowCount();
  }
  return FALSE;
}

function core_db_check($name = 'default') {
  global $_databases;
  if (isset($_databases[$name])) {
    return TRUE;
  }
  elseif (core_db_open($name)) {
    return TRUE;
  }
  return FALSE;
}

function core_db_error($resource, $query = '') {
  if ($resource instanceOf PDOException) {
    echo "core_db_error() called - " . $resource->getMessage() . "\n";
    if (!empty($query)) {
      echo "query: $query\n";
    }
  }
  elseif ($resource instanceOf PDO || $resource instanceOf PDOStatement) {
    echo "core_db_error() called - " . $resource->errorInfo()[2] . "\n";
    if (!empty($query)) {
      echo "query: $query\n";
    }
  }
  else {
echo "generic db error: $resource\n";
  }
//  core_log('database', 'error message: "' . $message . '" query: "' . $query . '"', 'error');
}

function core_db_free($stmt) {
  if ($stmt instanceOf PDOStatement) {
    return $stmt->closeCursor();
  }
  return FALSE;
}

function core_db_last($name = '') {
  global $_databases;
  if (core_db_check($name)) {
    return $_databases[$name]->lastInsertId;
  }
  return FALSE;
}

// not 100% portable for SELECT statements
function core_db_numrows($stmt) {
  if ($stmt instanceOf PDOStatement) {
    return $stmt->rowCount();
  }
  return FALSE;
}

function core_db_open($name = 'default') {
  global $config, $_databases;

  if (!isset($config['databases'][$name])) {
    core_db_error("database configuration '{$name}' not defined");
    return FALSE;
  }

  try {
    $db = new PDO(
      $config['databases'][$name]['type'] . ':' .
      'host=' . $config['databases'][$name]['hostname'] . ';' . 
      'dbname='. $config['databases'][$name]['database'] . ';' .
      'charset='. $config['databases'][$name]['charset'],
      $config['databases'][$name]['username'],
      $config['databases'][$name]['password']
    );
// PDO::ERRMODE_EXCEPTION, ERRMODE_WARNING, ERRMODE_SILENT
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // array
    $db->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, FALSE);
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES UTF8');
    $_databases[$name] = $db;
    return TRUE;
  }
  catch (PDOException $e) {
    core_db_error($e->getMessage());
  }
  return FALSE;
}

// specific to mysql
function core_db_paginate($query = '', $current_page = 1, $items_per_page = 15) {
  // not tested yet
  if (core_db_check()) {
    $start = $items_per_page * ($current_page - 1);
    if ($results = core_db_query(preg_replace('/^SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $query) . " LIMIT $start, {$items_per_page}")) {
      $q = core_db_query('SELECT FOUND_ROWS()');
      list($total_items) = core_db_rows($q);
      core_db_free($q);
      $end = $start + core_db_numrows($results);
      $total_pages = ceil($total_items / $items_per_page);
      $return = array(
        'results' => $results,
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'start_item' => $start + 1,
        'end_item' => $end,
      );
      return $return;
    }
  }
  return FALSE;
}
 
function core_db_query($query = '', $params = array(), $name = 'default') {
  global $_databases;
  if (core_db_check($name)) {
    try {
      if ($stmt = $_databases[$name]->prepare($query)) {
        if ($stmt->execute($params)) {
          return $stmt;
        }
      }
    }
    catch (PDOException $e) {
      core_db_error($stmt, $query);
    }
  }
  return FALSE;
}

function core_db_rows($stmt) {
  if ($stmt instanceOf PDOStatement) {
    return $stmt->fetch(PDO::FETCH_NUM);
  }
  return FALSE;
}

function core_db_rows_assoc($stmt) {
  if ($stmt instanceOf PDOStatement) {
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  return FALSE;
}
