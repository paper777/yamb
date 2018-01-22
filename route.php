<?php

/*
 * Yamb - A module for NForum, a replacement of Mobile Module
 *
 * @auther    paper777 <wuzhyy@163.com>
 *
 */

$base = c('modules.yamb.base');

$api = $base.'/b';

/* home page */
$export[] = [$api.'/banner', ['controller' => 'home', 'module' => 'yamb', 'action' => 'banner']];
$export[] = [$api.'/home/topten', ['controller' => 'home', 'module' => 'yamb', 'action' => 'index']];
$export[] = [$api.'/home/timeline', ['controller' => 'home', 'module' => 'yamb', 'action' => 'timeline']];
$export[] = [$api.'/home/fav/:level', ['controller' => 'home', 'module' => 'yamb', 'action' => 'fav']];
$export[] = [$api.'/back-to-nforum', ['controller' => 'home', 'module' => 'yamb', 'action' => 'backToNforum']];
$export[] = [$api.'/back-to-yamb', ['controller' => 'home', 'module' => 'yamb', 'action' => 'backToYamb']];

/* user */
$export[] = [$api.'/auth/:action', ['controller' => 'auth', 'module' => 'yamb']];
$export[] = [$api.'/user/profile', ['controller' => 'user', 'module' => 'yamb', 'action' => 'profile']];
$export[] = [$api.'/user/query/:username', ['controller' => 'user', 'module' => 'yamb', 'action' => 'query']];

/* article */
$export[] = [$api.'/article/:name/:action/:gid', ['controller' => 'article', 'module' => 'yamb', 'action' => null], ['gid' => '\d+']];
$export[] = [$api.'/article/:name/:action', ['controller' => 'article', 'module' => 'yamb']];

// attachement
$export[] = [$api.'/attachment/:name/:action/:id', ['controller' => 'attachment', 'module' => 'yamb', 'action' => null, 'id' => null], ['id' => '\d+']];

/* section */
$export[] = [$api.'/section', ['controller' => 'section', 'module' => 'yamb', 'action' => 'index']];
$export[] = [$api.'/section/:name', ['controller' => 'section', 'module' => 'yamb', 'action' => 'search']];

/* board */
$export[] = [$api.'/board/:name', ['controller' => 'board', 'module' => 'yamb']];

/* fav */
$export[] = [$api.'/fav/op', ['controller' => 'fav', 'module' => 'yamb', 'action' => 'change']];

/* refer */
$export[] = [$api.'/refer/:type/:action', ['controller' => 'refer', 'module' => 'yamb', 'action' => null]];

/* mail */
$export[] = [$api.'/mail/send', ['controller' => 'mail', 'module' => 'yamb', 'action' => 'send']];
$export[] = [$api.'/mail/:type/:action/:num', ['controller' => 'mail', 'module' => 'yamb', 'num' => null], ['num'=> '\d+']];
$export[] = [$api.'/mail/:type', ['controller' => 'mail', 'module' => 'yamb', 'action' => 'index', 'type' => null]];

/* vue */
$export[] = [$base, ['controller' => 'index', 'module' => 'yamb']];
$export[] = [$base.'/:vue', ['controller' => 'index', 'module' => 'yamb', ['vue' => '[\/\w\.-]*']]];
