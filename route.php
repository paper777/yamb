<?php
$base = c('modules.yamb.base');
//$export[] = array($base, array('controller' => 'index', 'module' => 'yamb'));

$api = $base . '/b';
$export[] = array($api . '/topten', array('controller' => 'home', 'module' => 'yamb', 'action' => 'index'));
$export[] = array($api . '/timeline', array('controller' => 'home', 'module' => 'yamb', 'action' => 'timeline'));
$export[] = array($api . '/auth/:action', array('controller' => 'auth', 'module' => 'yamb'));
$export[] = array($api . '/user/profile', array('controller' => 'user', 'module' => 'yamb', 'action' => 'profile'));
$export[] = array($api . '/user/query/:username', array('controller' => 'user', 'module' => 'yamb', 'action' => 'query'));

$export[] = array($api . '/article/:name/:action/:gid', array('controller' => 'article', 'module' => 'yamb', 'action' => null), array('gid' => '\d+'));
$export[] = array($api . '/article/:name/:action', array('controller' => 'article', 'module' => 'yamb'));

$export[] = array($base, array('controller' => 'index', 'module' => 'yamb'));
$export[] = array($base . '/:vue', array('controller' => 'index', 'module' => 'yamb', array('vue' => '[\/\w\.-]*')));
