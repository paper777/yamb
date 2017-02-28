<?php
class UserController extends NF_YambController {
    public function init() {
        parent::init();
        $this->requestLogin();
    }

    public function queryAction() {
        $id = trim($this->params['username']);
        try {
            $u = User::getInstance($id);
        } catch(UserNullException $e) {
            return $this->fail('No such user');
        }

        load("inc/wrapper");
        $wrapper = Wrapper::getInstance();
        $data = $wrapper->user($u);
        $data['status'] = $u->getStatus();
        return $this->success($data);
    }

    public function profileAction() {
        $u = User::getInstance();
        load("inc/wrapper");
        $wrapper = Wrapper::getInstance();
        $data = $wrapper->user($u);
        $data['status'] = $u->getStatus();
        return $this->success($data);
    }
}
