<?php
class NF_YambController extends NF_Controller {

    protected $_exts = array('json'=> 'application/json');

    public function init(){
        c('application.encoding', 'utf-8');
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
}
