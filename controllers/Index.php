<?php
/**
 * This Controller if for index.html
 */
class IndexController extends NF_Controller {
    
    public function init(){
        c('application.encoding', 'utf-8');
        parent::init();
        $this->getRequest()->front = true;
    }

    public function beforeRender() {
    }

    public function afterRender() {

    }

    public function indexAction() { 

    }
}

