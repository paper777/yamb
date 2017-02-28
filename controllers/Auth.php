<?php
class AuthController extends NF_YambController {

    public function loginAction() { 
        if (! $this->getRequest()->isPost()) {
            $this->abort();
        }
        if (! isset($this->params['form']['username'])) {
            return $this->fail();
        }

        $id = trim($this->params['form']['username']);
        @$pwd = $this->params['form']['password'];
        $time = 31536000;

        try {
            NF_Session::getInstance()->login($id, $pwd, false, $time);
        } catch(LoginException $e) {
            $this->fail();
        }
        $u = User::getInstance($id);
        load("inc/wrapper");
        $wrapper = Wrapper::getInstance();
        $data = $wrapper->user($u);
        return $this->success($data);
    }

    public function logoutAction() {
        $this->cache(false);
        NF_Session::getInstance()->logout();
        return $this->success();
    }
}
