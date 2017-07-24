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

        load('model/refer');
        try {
            if ($u->getCustom('userdefine1', 2)) {
                $refer = new Refer($u, Refer::$AT);
                $data['new_at'] = $refer->getNewNum();
            }
            if ($u->getCustom('userdefine1', 3)) {
                $refer = new Refer($u, Refer::$REPLY);
                $data['new_reply'] = $refer->getNewNum();
            }
        } catch (ReferNullException $e) {
            // pass
        }

        load("model/mail");
        $info = MailBox::getInfo($u);
        $data['new_mail'] = $info['newmail'];
        $data['full_mail'] = $info['full'];

        return $this->success($data);
    }
}
