<?php
class NF_YambController extends NF_Controller {

    protected $_exts = array('json'=> 'application/json');

    public function init(){
        c('application.encoding', 'UTF-8');
        parent::init();
        $this->getRequest()->front = true;
    }

    public function beforeRender() {
        $this->getRequest()->html = false;
        $this->set('no_html_data', $this->get('data'));
    }

    public function afterRender() {

    }

    protected function fail($message = 'ops') {
        return $this->set('data', [
            'success' => false,
            'message' => $message
        ]);
    }

    protected function success($data = null) {
        return $this->set('data', [
            'success' => true,
            'data' => $data
        ]);
    }

    protected function abort() {
        header('HTTP/1.1 404 Not Found');
        exit();
    }

    public function requestLogin() {
        if (! NF_Session::getInstance()->isLogin) {
            header('HTTP/1.1 401 Unauthorized');
            exit();
        }
    }

    public function formatTime($timestamp) {
        $gap = time() - $timestamp;
        $today = strtotime(
            date("Y-m-d 00:00:00", time())
        );
        $yestoday = $today - 86400;
        if ($gap < 300) {
            $r = '刚刚';
        } else if ($gap < 3600) {
            $r = (int) ($gap / 60) . '分钟前';
        } else if ($timestamp > $today) {
            $r = '今天 '  . date("H:i", $timestamp);
        } else if ($timestamp > $yestoday) {
            $r = date("Y-m-d H:i", $timestamp);
        } else {
            $r = date("Y-m-d", $timestamp);
        }

        return $r;
    }
}
