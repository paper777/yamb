<?php
class FavController extends NF_YambController 
{

    private $level = 0;

    public function init() {
        parent::init();
        $this->requestLogin();

        if (! empty($this->params['level'])) {
            $this->level = (int) $this->params['level'];
        }
    }

    public function indexAction() {
        load("model/favor");

        try {
            $favBoards = Favor::getInstance($this->level);
        } catch (FavorNullException $e) {
            $this->fail('内部错误');
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
}
