<?php
class SectionController extends NF_YambController 
{

    public function init() {
        parent::init();
        $this->requestLogin();
    }


    public function indexAction() {
        $boards = nforum_cache_read('yamb:section:all');
        if (! empty($boards)) {
            return $this->success([
                'boards' => $boards
            ]);
        }

        load("model/section");
        load("model/favor");
        try {
            $favBoards = Favor::getInstance(0);
        } catch (FavorNullException $e) {
            $this->fail('内部错误');
        }
        $favs = $favBoards->getAllBoards();

        $secs = c("section");
        foreach($secs as $k=>$v){
            $t = array(
                "id" => $k,
                "name" => $v[0],
                "desc" => $v[1],
                "dir" => true,
                "first_level" => true,
                'new' => 0,
                'children' => array(),
                'offset' => 0,
                'fav' => in_array($k, $favs) ? true : false,
            );
            $t['children'] = $this->findBoardsByRecursion($k, 1, $favs);
            $ret[] = $t;
        }
        nforum_cache_write('yamb:section:all', $ret);
        return $this->success([
            'boards' => $ret,
        ]);
    }

    public function searchAction() {
        load("model/section");
        load("model/board");
        load("model/favor");
        try {
            $favBoards = Favor::getInstance(0);
        } catch (FavorNullException $e) {
            $this->fail('内部错误');
        }
        $favs = $favBoards->getAllBoards();
        $name = trim($this->params['name']);
        $name = urldecode($name);
        $name = nforum_iconv($this->encoding, 'GBK', $name);
        $brds = Board::search($name);
        $ret = array();
        $sections = array();
        $boards = array();
        foreach($brds as $b) {
            $t = array(
                'id' => $b->NAME,
                "name" => $b->DESC,
                "desc" => $b->NAME,
                "dir" => $b->isDir(),
                "first_level" => ($b->NAME >=0 && $b->NAME <= 9) ? true : false,
                'new' => $b->getTodayNum(),
                'children' => array(),
                'offset' => 0,
                'fav' => in_array($b->NAME, $favs) ? true : false,
            );
            if($b->isDir()) {
                $t['children'] = $this->findBoardsByRecursion($b->NAME, 1, $favs);
                $sections[] = $t;
            } else {
                $boards[] = $t;
            }
        }
        $ret = array_merge($sections, $boards);
        return $this->success([
            'boards' => $ret,
        ]);
    }

    private function findBoardsByRecursion($id, $depth, $favs = array()) {
        $ret = array();
        try {
            $sec = Section::getInstance($id, Section::$ALL);
            $brds = $sec->getAll();
            $sub_sections = array();
            $boards = array();
            foreach($brds as $b){
                $isDir = $b->isDir();
                if($isDir) {
                    $t = array(
                        "id" => $b->NAME,
                        "name" => $b->DESC,
                        "desc" => $b->NAME,
                        "dir" => $isDir,
                        "first_level" => false,
                        'new' => $isDir ? 0 : $b->getTodayNum(),
                        'children' => array(),
                        'offset' => $depth,
                        'fav' => in_array($b->NAME, $favs) ? true : false,
                    );
                    $t['children'] = $this->findBoardsByRecursion($b->NAME, $depth + 1, $favs);
                    $sub_sections[] = $t;
                } else {
                    $boards[] = array(
                        "id" => $b->NAME,
                        "name" => $b->DESC,
                        "desc" => $b->NAME,
                        "dir" => $isDir,
                        "first_level" => false,
                        'new' => $isDir ? 0 : $b->getTodayNum(),
                        'children' => array(),
                        'offset' => $depth,
                        'fav' => in_array($b->NAME, $favs) ? true : false,
                    );
                }
            }
            $ret = array_merge($sub_sections, $boards);
        }catch(SectionNullException $e){
            $this->error(ECode::$SEC_NOSECTION);
        }
        return $ret;
    }
}
