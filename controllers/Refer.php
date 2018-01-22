<?php

/*
 * Yamb - A module for NForum, a replacement of Mobile Module
 *
 * @auther    paper777 <wuzhyy@163.com>
 *
 */

class ReferController extends NF_YambController
{
    public function init()
    {
        parent::init();
        if (!c('refer.enable')) {
            return $this->fail('Refer component disabled.');
        }
        $this->requestLogin();
    }

    public function indexAction()
    {
        load(['model/refer', 'inc/pagination']);

        if (!isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch (ReferNullException $e) {
            return $this->fail('no refer found');
        }

        $count = c('pagination.mail');
        $page = isset($this->params['url']['page']) ? (int) $this->params['url']['page'] : 1;

        $pagination = new Pagination($refer, $count);
        $articles = $pagination->getPage($page);

        $info = [];
        foreach ($articles as $v) {
            $data = [
                'index'    => $v['INDEX'],
                'id'       => $v['ID'],
                'group_id' => $v['GROUP_ID'],
                'board'    => $v['BOARD'],
                'user'     => $v['USER'],
                'title'    => nforum_html($v['TITLE']),
                'time'     => $this->formatTime($v['TIME']),
                'read'     => ($v['FLAG'] === Refer::$FLAG_READ),
            ];

            try {
                load(['model/threads', 'model/board', 'model/board']);
                $threads = Threads::getInstance($v['GROUP_ID'], Board::getInstance($v['BOARD']));
                $article = $threads->getArticleById((int) $v['ID']);
            } catch (Exception $e) {
                continue;
            }

            if ($article == null) {
                $data['content'] = false;
                $data['pos'] = -1;
                continue;
            }

            $data['pos'] = $article->getPos();

            $content = $article->getContent();
            //$content = preg_replace("/&nbsp;/", " ", $content);
            //$content = preg_replace("/  /", "&nbsp;&nbsp;", $content);

            //// get source
            //preg_match("|※ 来源:(.*)FROM:.*|", $content, $f);
            //$source = empty($f) ? "北邮人论坛" : $f[1];

            //// remove bottom lines
            //$s = (($pos = strpos($content, "<br/><br/>")) === false) ? 0 : $pos + 10;
            //$e = (($pos = strpos($content, "<br/>--<br/>")) === false)
            //    ? strlen($content)
            //    : $pos + 7;
            //$content = preg_replace(
            //    array("'^(<br/>)+'", "|(<br/>)+--$|")
            //    ,array("", "\n--")
            //    ,substr($content, $s, $e - $s)
            //);

            //// parse attachments
            //$content = $article->parseAtt($content, 'middle');
            if (c('ubb.parse')) {
                load('inc/ubb');
                $content = XUBB::parse($content);
            }

            $data['content'] = $content;
            $info[] = $data;
            unset($data);
        }
        $article = $info;

        return $this->success(compact('article', 'pagination', 'description'));
    }

    public function readAction()
    {
        load(['model/refer', 'inc/wrapper']);

        if (!isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        if (!isset($this->params['url']['index'])) {
            return $this->fail('index not defined');
        }
        $index = $this->params['url']['index'];

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch (ReferNullException $e) {
            return $this->fail('no refer found');
        }

        if ('all' == $index) {
            $refer->setRead();

            return $this->success(true);
        }

        $index = intval($index);
        $r = $refer->getRefer($index);
        if (null === $r) {
            return $this->fail('refer not found');
        }
        $refer->setRead($index);

        return $this->success(true);
    }

    public function deleteAction()
    {
        load(['model/refer']);

        if (!isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        if (!isset($this->params['url']['index'])) {
            return $this->fail('index not defined');
        }
        $index = $this->params['url']['index'];

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch (ReferNullException $e) {
            return $this->fail('no refer found');
        }

        $refer->delete(intval($index));

        return $this->success(true);
    }
}
