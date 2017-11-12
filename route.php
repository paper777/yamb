<?php
$base = c('modules.yamb.base');

$api = $base . '/b';

/** home page */
$export[] = array($api . '/banner', array('controller' => 'home', 'module' => 'yamb', 'action' => 'banner'));
$export[] = array($api . '/home/topten', array('controller' => 'home', 'module' => 'yamb', 'action' => 'index'));
$export[] = array($api . '/home/timeline', array('controller' => 'home', 'module' => 'yamb', 'action' => 'timeline'));
$export[] = array($api . '/home/fav/:level', array('controller' => 'home', 'module' => 'yamb', 'action' => 'fav'));
$export[] = array($api . '/back-to-nforum', array('controller' => 'home', 'module' => 'yamb', 'action' => 'backToNforum'));
$export[] = array($api . '/back-to-yamb', array('controller' => 'home', 'module' => 'yamb', 'action' => 'backToYamb'));

/** user */
$export[] = array($api . '/auth/:action', array('controller' => 'auth', 'module' => 'yamb'));
$export[] = array($api . '/user/profile', array('controller' => 'user', 'module' => 'yamb', 'action' => 'profile'));
$export[] = array($api . '/user/query/:username', array('controller' => 'user', 'module' => 'yamb', 'action' => 'query'));

/** article */
$export[] = array($api . '/article/:name/:action/:gid', array('controller' => 'article', 'module' => 'yamb', 'action' => null), array('gid' => '\d+'));
$export[] = array($api . '/article/:name/:action', array('controller' => 'article', 'module' => 'yamb'));

// attachement
$export[] = array($api . '/attachment/:name/:action/:id', array('controller' => 'attachment', 'module' => 'yamb', 'action' => null, 'id' => null), array('id' => '\d+'));

/** section */
$export[] = array($api. '/section', array('controller' => 'section', 'module' => 'yamb', 'action' => 'index'));
$export[] = array($api. '/section/:name', array('controller' => 'section', 'module' => 'yamb', 'action' => 'search'));

/** board */
$export[] = array($api . '/board/:name', array('controller' => 'board', 'module' => 'yamb'));

/** fav */
$export[] = array($api . '/fav/op', array('controller' => 'fav', 'module' => 'yamb', 'action' => 'change'));

/** refer */
$export[] = array($api . '/refer/:type/:action', array('controller' => 'refer', 'module' => 'yamb', 'action' => null));

/** mail */
$export[] = array($api . '/mail/send', array('controller' => 'mail', 'module' => 'yamb', 'action' => 'send'));
$export[] = array($api . '/mail/:type/:action/:num', array('controller' => 'mail', 'module' => 'yamb', 'num' => null), array('num'=> '\d+'));
$export[] = array($api . '/mail/:type', array('controller' => 'mail', 'module' => 'yamb', 'action' => 'index', 'type' => null));

/** vue */
$export[] = array($base, array('controller' => 'index', 'module' => 'yamb'));
$export[] = array($base . '/:vue', array('controller' => 'index', 'module' => 'yamb', array('vue' => '[\/\w\.-]*')));
