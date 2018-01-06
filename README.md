## Introduction

Yamb is a module for [nforum](https://github.com/xw2423/nForum), a replacement of [Mobile Module](https://github.com/xw2423/nForum/tree/2.0/app/modules).

<!--
Yamb is also a project for BYRs to learn front end programming
-->

see https://bbs.byr.cn/n

![yamb-screenshot](http://ovnevollz.bkt.clouddn.com/yamb-screenshot.png)



## Installation


``` bash
$ cd /path/to/nforum

### 1. vim config/nforum.php, add yamb configurations
<?php
$export['modules']['install'] = array('index', 'yamb'/** , other moduels */);
$export['modules']['yamb']['base'] = 'mobile';
$export['modules']['yamb']['domain'] = 'm';

### 2. add redirect rule to app/plugins/Redirect.php

### 3. add submoduel
$ git submodule add git@github.com:paper777/yamb.git app/modules/Yamb

### 4. build
$ ./app/moduels/Yamb/scripts/build

### 5. add asset link
cd www && link -sf ../app/modules/Yamb/www/dist/yamb .

```

## TODOs

### Thread(artticle) page

forward article

...

### User profile page

say hello (mail) to sb.

add to friend list (optional)

...


### Search

article search (in a specific board)

...


## Contributing

Thank you for considering contributing to the yamb module! 

Any bugs just open an [issue](https://github.com/paper777/yamb/issues/new) or submit a PR

1. Front End Guide

Clone the repo and run ```npm install && npm run dev```. 

Back end api requests will be proxied to `http://m.also777.com (10.3.18.65)`, please ensure you are in BUPT Network(10.0.0.0/8).

2. Back End

connect paper777#qq(dot)com
