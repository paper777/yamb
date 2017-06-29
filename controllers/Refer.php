<?php
class ReferController extends NF_YambController {

    public function init(){
        parent::init();
        if (! c('refer.enable')) {
            return $this->fail('Refer component disabled.');
        }
        $this->requestLogin();
    }

    public function indexAction() {
        load(['model/refer', 'inc/pagination']);

        if (! isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch(ReferNullException $e) {
            return $this->fail('no refer found');
        }

        $count = c('pagination.mail');
        $page = isset($this->params['url']['page']) ? (int) $this->params['url']['page'] : 1;

        $pagination = new Pagination($refer, $count);
        $articles = $pagination->getPage($page);

        $info = [];
        foreach($articles as $v) {
            $info[] = [
                    "index" => $v['INDEX'],
                    "id" => $v['ID'],
                    "board" => $v['BOARD'],
                    "user" => $v['USER'],
                    "title" => nforum_html($v['TITLE']),
                    "time" => $this->formatTime($v['TIME']),
                    "read" => ($v['FLAG'] === Refer::$FLAG_READ)
                ];
        }
        $article = $info;
        return $this->success(compact('article', 'pagination', 'description'));
    }

    public function readAction() {
        load(['model/refer']);

        if (! isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        if (! isset($this->params['url']['index'])) {
            return $this->fail('index not defined');
        }
        $index = $this->params['url']['index'];

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch(ReferNullException $e) {
            return $this->fail('no refer found');
        }

        if ('all' == $index) {
            $refer->setRead();
            return $this->success(true);
        } 

        $index = intval($index);
        $r = $refer->getRefer($index);
        if (null !== $r) {
            $refer->setRead($index);
            return $this->success(true);
        } 

        return $this->fail('refer not found');
    }

    public function deleteAction() {
        load(['model/refer']);

        if (! isset($this->params['type'])) {
            $type = 'reply';
        } else {
            $type = $this->params['type'];
        }

        if (! isset($this->params['url']['index'])) {
            return $this->fail('index not defined');
        }
        $index = $this->params['url']['index'];

        try {
            $refer = new Refer(User::getInstance(), $type);
        } catch(ReferNullException $e) {
            return $this->fail('no refer found');
        }

        $refer->delete(intval($index));
        return $this->success(true);
    }


}

