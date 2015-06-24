#!/usr/bin/php

<?php

$end = $argv[1];
$data = array();

function cache_set($key, $value) {
  global $data;
  $data[$key] = $value;
}

function cache_get($key) {
  global $data;
  return (isset($data[$key])) ? $data[$key] : NULL;
}

function memoize($prefix, $func) {
  return function () use ($prefix, $func) {
    $args = func_get_args();
    $key = $prefix . "_" . md5(serialize($args));

    $result = cache_get($key);

    if (is_null($result)) {
      $result = call_user_func_array($func, $args);
      cache_set($key, $result);
    }

    return $result;
  };
}

function log_func() {
  return function ($int) {
    $i = 0;
    $sum = 0;
    while ($i < $int) {
      $sum += log($i);
      ++$i;
    }
    return $sum;
  };
}

function exponentiate() {
  return function ($int) {
    $i = 0;
    $sum = 0;
    while ($i < $int) {
      $sum += pow($i, $i);
      ++$i;
    }
    return $sum;
  };
}

function test($func, $end) {
  $i = 0;
  $time = microtime(true);
  $md5 = "";
  while ($i <= $end) {
    $md5 = md5($md5 . $func($i));
    ++$i;
  }
  return sprintf("Time: %f MD5: %s", microtime(true) - $time, $md5);
}

$memoized = memoize('exp', exponentiate());
echo test($memoized, $end) . "\t";
echo test($memoized, $end) . "\n";

$logmemoized = memoize('log', log_func());
echo test($logmemoized, $end) . "\t";
echo test($logmemoized, $end) . "\n";

?>
