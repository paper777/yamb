<?php
$base = c('modules.yamb.base');

$api = $base . '/b';

/** home page */
$export[] = array($api . '/topten', array('controller' => 'home', 'module' => 'yamb', 'action' => 'index'));
$export[] = array($api . '/timeline', array('controller' => 'home', 'module' => 'yamb', 'action' => 'timeline'));
$export[] = array($api . '/fav/:level', array('controller' => 'home', 'module' => 'yamb', 'action' => 'fav'));

/** user */
$export[] = array($api . '/auth/:action', array('controller' => 'auth', 'module' => 'yamb'));
$export[] = array($api . '/user/profile', array('controller' => 'user', 'module' => 'yamb', 'action' => 'profile'));
$export[] = array($api . '/user/query/:username', array('controller' => 'user', 'module' => 'yamb', 'action' => 'query'));

/** article */
$export[] = array($api . '/article/:name/:action/:gid', array('controller' => 'article', 'module' => 'yamb', 'action' => null), array('gid' => '\d+'));
$export[] = array($api . '/article/:name/:action', array('controller' => 'article', 'module' => 'yamb'));

// attachement
$export[] = array($api . '/attachment/:name/:action/:id', array('controller' => 'attachment', 'module' => 'yamb', 'action' => null, 'id' => null), array('id' => '\d+'));

/** board */
$export[] = array($api . '/board/:name', array('controller' => 'board', 'module' => 'yamb'));

/** refer */
$export[] = array($api . '/refer/:type/:action', array('controller' => 'refer', 'module' => 'yamb', 'action' => null));

/** mail */
$export[] = array($api . '/mail/send', array('controller' => 'mail', 'module' => 'yamb', 'action' => 'send'));
$export[] = array($api . '/mail/:type/:action/:num', array('controller' => 'mail', 'module' => 'yamb', 'num' => null), array('num'=> '\d+'));
$export[] = array($api . '/mail/:type', array('controller' => 'mail', 'module' => 'yamb', 'action' => 'index', 'type' => null));

/** vue */
$export[] = array($base, array('controller' => 'index', 'module' => 'yamb'));
$export[] = array($base . '/:vue', array('controller' => 'index', 'module' => 'yamb', array('vue' => '[\/\w\.-]*')));
