<?php

/*
 * Yamb - A module for NForum, a replacement of Mobile Module
 *
 * @auther    paper777 <wuzhyy@163.com>
 *
 */

class IndexController extends NF_Controller
{
    public function init()
    {
        c('application.encoding', 'utf-8');
        parent::init();
        $this->getRequest()->front = true;
    }

    public function beforeRender()
    {
    }

    public function afterRender()
    {
    }

    public function indexAction()
    {
    }
}
