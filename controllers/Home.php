<?php
class HomeController extends NF_YambController {

    const PAGE_SIZE = 20;

    public function init() {
        parent::init();
        $this->requestLogin();
    }

    public function indexAction() { 
        load(['model/widget', 'inc/wrapper']);
        try {
            $widget = Widget::getInstance('topten');
        } catch(WidgetNullException $e) {
            $this->fail($e->getMessage());
        }

        $wrapper = Wrapper::getInstance();
        $data = $wrapper->widget($widget);

        $list = $widget->wGetList();

        if (! array($list['v'])) {
            return $this->fail('内部错误');
        }

        $articles = [];
        load(array('model/board', 'model/threads'));
        foreach ($list['v'] as $v) {
            if (empty($v['url'])) {
                continue;
            }
            
            $ret = [];
            preg_match("|^/article/(.*?)/(.*?)$|", $v['url'], $ret);

            if(empty($ret[1]) || empty($ret[2])) {
                continue;
            }

            $board = rawurldecode($ret[1]);
            $id = (int)$ret[2];

            $text = $v['text'];
            $text = preg_replace("|<[^>]*?>|", '', $text);
            if(preg_match("/\((\d+)\)$/", $text, $counter)) {
                $counter = $counter[1];
            } else {
                $counter = 0;
            }

            try {
                $article = Threads::getInstance($id, Board::getInstance($board));
                $thread = $wrapper->feed($article);
                $articles[] = $thread;
            } catch(Exception $e) {
                continue;
            }
        }
        return $this->success($articles);
    }

    public function timelineAction() {
        load(['model/favorpost', 'inc/pagination', 'inc/wrapper', 'inc/ubb']);

        $count = c("pagination.threads");
        $page = isset($this->params['url']['page']) ? (int) $this->params['url']['page'] : 1;

        $u = User::getInstance();

        $fav = new FavorPost($u, static::PAGE_SIZE);

        $pagination = new Pagination($fav, $count);

        $wrapper = Wrapper::getInstance();
        $data = [
            'article' => [],
            'pagination' => $wrapper->page($pagination)
        ];

        $articles = $pagination->getPage($page);
        foreach($articles as $article){
            $thread = $wrapper->feed($article);
            $data['article'][] = $thread;
        }

        return $this->success($data);
    }

    public function favAction() {
        load("model/favor");

        if (! empty($this->params['level'])) {
            $level = (int) $this->params['level'];
        } else {
            $level = 0;
        }

        try {
            $favBoards = Favor::getInstance($level);
        } catch (FavorNullException $e) {
            $this0>fail('内部错误');
        }

        $parent = $favBoards->getParent();
        $parent = $parent ? $parent->getLevel() : -1;

        $boards = [];
        if ($favBoards->isNull()) {
            return $this->success(compact('parent', 'boards'));
        }

        $_boards = $favBoards->getAll();
        foreach ($_boards as $key => $board) {
            $boards[] = [
                'name' => $board->NAME,
                'desc' => $board->DESC,
                'dir' => $board->isDir(),
                'pos' => $board->NPOS,
                'new' => $board->getTodayNum(),
                'level' => $board->BID
            ];
        }
        return $this->success(compact('parent', 'boards'));
    }

    public function bannerAction() {
        load('model/adv');
        $res = Adv::getMobileBanner();

        $banners = [];
        $base = c('site.static');

        foreach($res as $b) {
            $banners[] = [
                'image_url'=> $base . $b['file'],
                'intro' => $b['remark'],
                'url' => $b['url']
            ];
        }
        $data = [
            'banners' => $banners
        ];
        return $this->success($data);
    }

}
