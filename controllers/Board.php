<?php
class BoardController extends NF_YambController {

    private $_board;

    public function init(){
        parent::init();
        if (isset($this->params['name'])) {
            $bName = trim($this->params['name']);
        } else {
            $this->abort();
        }

        try {
            load('model/board');
            $this->_board = Board::getInstance($bName);
            if($this->_board->isDir()) {
                throw new BoardNullException();
            }
        } catch(BoardNullException $e) {
            return $this->fail('不存在的版面');
        }

        if (! $this->_board->hasReadPerm(User::getInstance())) {
            return $this->fail('无此权限');
        }
        $this->_board->setOnBoard();
    }

    public function indexAction(){
        $u = User::getInstance();
        $p = isset($this->params['url']['p']) ? $this->params['url']['p'] : 1;
        
        load('inc/pagination');
        try {
            $page = new Pagination($this->_board, c("pagination.threads"));
            $threads = $page->getPage($p);
        } catch(BoardNullException $e) {
            return $this->fail('不存在的版面');
        }

        $posts = [];
        $pageArticle = c("pagination.article");
        foreach($threads as $v){
            $pages = ceil($v->articleNum / $pageArticle);
            $last = $v->LAST;
            //$postTime = $this->formatTime($v->POSTTIME);
            $replyTime = $this->formatTime($last->POSTTIME);
            $posts[] = [
                //"postTime" => $postTime,
                "title" => nforum_html($v->TITLE),
                "poster" => $v->isSubject() ? $v->OWNER : "原帖已删除",
                "gid" => $v->ID,
                "last" => $last->OWNER,
                "replyTime" => $replyTime,
                "replyCount" => $v->articleNum,
                "num" => $v->articleNum - 1,
                "page" => $pages,
                "tag" => $v->isTop() ? "top" : (($v->isM() || $v->isG())? "m" : false),
            ];
        }
        $data = [
            'description' => "版面-{$this->_board->DESC}({$this->_board->NAME})",
            'posts' => $posts,
            'pagination' => [
                'current' => $page->getCurPage(),
                'total' => $page->getTotalPage(),
            ],
            'name' => $this->_board->NAME,
            'canPost' => $this->_board->hasPostPerm($u),
            'isBM' => $u->isBM($this->_board),
            'isAdmin' => $u->isAdmin()
        ];
        return $this->success($data);
    }
}
