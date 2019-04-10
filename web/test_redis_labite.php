<?php

/*
$redis = new Redis();
try {
  $redis->connect('node6982-sauvabelin.jcloud.ik-server.com');
    $redis->auth('AMXepc96725');
  $redis->set('yolololo', 'swag');
  var_dump($redis->get('yolololo'));
} catch (Exception $e) {
  echo $e->getMessage();
}

