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
        load(['model/refer', 'inc/wrapper']);

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
        if (null === $r) {
            return $this->fail('refer not found');
        } 
        $refer->setRead($index);
        $data = [
            'index' => $r['INDEX'],
            'id' => $r['ID'],
            'group_id' => $r['GROUP_ID'],
            'reply_id' => $r['RE_ID'],
            'board_name' => $r['BOARD'],
            'is_read' => true,
            'time' => $this->formatTime($r['TIME']),
            'title' => $r['TITLE'],
        ];

        try {
            load(['model/threads', 'model/board', 'model/board']);
            $threads = Threads::getInstance($r['GROUP_ID'], Board::getInstance($r['BOARD']));
            $article = $threads->getArticleById((int) $r['ID']);
            
        } catch (Exception $e) {
            return $this->fail('operation failed');
        }

        if ($article == null) {
            $data['content'] = false;
            $data['pos'] = -1;
        }

        $data['pos'] = $article->getPos();

        $content = $article->getPlant();
        $content = preg_replace("/&nbsp;/", " ", $content);
        $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);

        // get source
        preg_match("|※ 来源:(.*)FROM:.*|", $content, $f);
        $source = empty($f) ? "北邮人论坛" : $f[1];

        // remove bottom lines
        $s = (($pos = strpos($content, "<br/><br/>")) === false) ? 0 : $pos + 10;
        $e = (($pos = strpos($content, "<br/>--<br/>")) === false) 
           ? strlen($content)
           : $pos + 7;
        $content = preg_replace(
            array("'^(<br/>)+'", "|(<br/>)+--$|")
            ,array("", "<br>--")
            ,substr($content, $s, $e - $s)
        );

        // parse attachments
        $content = $article->parseAtt($content, 'middle');
        if(c("ubb.parse")) {
            load('inc/ubb');
            $content = XUBB::parse($content);
        }

        $data['content'] = $content;
        return $this->success($data);
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

