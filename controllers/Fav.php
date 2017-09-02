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

    public function changeAction() {
        if(!isset($this->params['form']['ac']) || !isset($this->params['form']['v']))
            $this->fail('缺少参数');

        load("model/favor");
        $action = $this->params['form']['ac'];
        $val = $this->params['form']['v'];
        try{
            $fav = Favor::getInstance($this->level);
        }catch(FavorNullException $e){
            $this->fail('内部错误');
        }
        if($val == "")
            $this->fail('缺少参数');

        switch($action){
            case "ab":
                try{
                    $val = Board::getInstance($val);
                    if(!$fav->add($val, Favor::$BOARD))
                        $this->fail('收藏失败');
                }catch(Exception $e){
                    $this->fail('版面不存在');
                }
                break;
            case "ad":
                if(!$fav->add(nforum_iconv("utf-8", $this->encoding, $val), Favor::$DIR))
                    $this->fail('添加目录失败');
                break;
            case "db":
                try{
                    $val = Board::getInstance($val);
                    if(!$fav->delete($val, Favor::$BOARD))
                        $this->fail('取消收藏失败');
                }catch(Exception $e){
                    $this->fail('版面不存在');
                }
                break;
            case "dd":
                if(!$fav->delete($val, Favor::$DIR))
                    $this->fail('删除目录失败');
                break;
        }

        return $this->success();
    }
}
