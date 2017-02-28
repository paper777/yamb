<?php
class HomeController extends NF_YambController {

    public function init() {
        parent::init();
        //$this->requestLogin();
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
        $user = User::getInstance();

        if ($user->isVisitor()) {
            return $this->fail('这里会显示收藏版面的最新帖子，登录后才能看哦');
        }

        $data = [
            'articles' => [],
            'pagination' => ''
        ];

        load('inc/pagination');
        $count = c("pagination.threads");
        $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 1;
        $page = intval($page);

        $fav = new FavorPost($user, 20);
        $pagination = new Pagination($fav, $count);
        $articles = $pagination->getPage($page);

        load('inc/wrapper');
        $wrapper = Wrapper::getInstance();
        $data['pagination'] = $wrapper->page($pagination);
        
        foreach($articles as $v) {
            $data['articles'][] = $wrapper->feed($v);
        }
        $this->set('data', $data);
    }
}

