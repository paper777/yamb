<?php

/*
 * Yamb - A module for NForum, a replacement of Mobile Module
 *
 * @auther    paper777 <wuzhyy@163.com>
 *
 */

class MailController extends NF_YambController
{
    public function init()
    {
        parent::init();
        $this->requestLogin();
        load('model/mail');
    }

    public function indexAction()
    {
        $type = MailBox::$IN;
        if (isset($this->params['type'])) {
            $type = $this->params['type'];
        }

        try {
            $mailBox = new MailBox(User::getInstance(), $type);
        } catch (MailBoxNullException $e) {
            return $this->fail('mailbox is not available');
        }

        $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 1;

        load('inc/pagination');

        try {
            $pagination = new Pagination($mailBox, 10);
            $mails = $pagination->getPage($page);
        } catch (MailDataNullException $e) {
            return $this->fail('mailbox is not available');
        }

        $info = [];
        if ($mailBox->getTotalNum() > 0) {
            foreach ($mails as $v) {
                $info[] = [
                    'read'   => $v->isRead(),
                    'num'    => $v->num,
                    'sender' => $v->OWNER,
                    'title'  => nforum_html($v->TITLE),
                    'time'   => $this->formatTime($v->POSTTIME),
                    'size'   => $v->EFFSIZE,
                ];
            }
        }
        $mails = $info;
        unset($info);

        $data = [
            'mails'      => $mails,
            'pagination' => [
                'current' => $pagination->getCurPage(),
                'total'   => $pagination->getTotalPage(),
            ],
        ];

        return $this->success($data);
    }

    public function showAction()
    {
        if (!isset($this->params['type'])) {
            return $this->fail('mailbox type not undefined');
        }

        if (!isset($this->params['num'])) {
            return $this->fail('mailbox mail number not defined');
        }

        $type = $this->params['type'];
        $num = $this->params['num'];

        try {
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        } catch (Exception $e) {
            return $this->fail('mailbox not available');
        }

        $mail->setRead();

        $content = $mail->getHtml();
        preg_match("|来&nbsp;&nbsp;源:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
        $f = empty($f) ? '' : "<br />FROM {$f[1]}";
        $s = (($pos = strpos($content, '<br/><br/>')) === false) ? 0 : $pos + 10;
        $e = (($pos = strpos($content, '<br/>--<br/>')) === false) ? strlen($content) : $pos + 7;
        $content = substr($content, $s, $e - $s).$f;
        if (c('ubb.parse')) {
            load('inc/ubb');
            $content = XUBB::parse($content);
        }
        $data = [
            'num'     => $mail->num,
            'title'   => nforum_html($mail->TITLE),
            'sender'  => $mail->OWNER,
            'time'    => $this->formatTime($mail->POSTTIME),
            'content' => $content,
        ];

        return $this->success($data);
    }

    public function sendAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->abort();
        }

        if (!Mail::canSend()) {
            return $this->fail('mail sender component not available');
        }

        $u = User::getInstance();
        $mail = false;

        // reply mode
        if (isset($this->params['type']) && isset($this->params['num'])) {
            $type = $this->params['type'];
            $num = $this->params['num'];

            try {
                $mail = MAIL::getInstance($num, new MailBox($u, $type));
            } catch (Exception $e) {
                return $this->fail('mail sender component not available');
            }
        }

        $title = $content = '';

        if (isset($this->params['form']['title'])) {
            $title = trim($this->params['form']['title']);
        }

        if (isset($this->params['form']['content'])) {
            $content = $this->params['form']['content'];
        }

        $sig = 0;
        $bak = isset($this->params['form']['backup']) ? 1 : 0;
        $title = nforum_iconv($this->encoding, 'GBK', $title);
        $content = nforum_iconv($this->encoding, 'GBK', $content);

        if ($mail === false) { // send new
            if (!isset($this->params['form']['id'])) {
                return $this->fail('sender id not defined');
            }
            $id = trim($this->params['form']['id']);

            try {
                Mail::send($id, $title, $content, $sig, $bak);
            } catch (MailSendException $e) {
                return $this->fail($e->getMessage());
            }

            return $this->success();
        }

        // reply
        try {
            $mail->reply($title, $content, $sig, $bak);
        } catch (MailSendException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success();
    }

    public function forwardAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->abort();
        }

        if (!isset($this->params['type'])) {
            return $this->fail('mailbox type not undefined');
        }

        if (!isset($this->params['num'])) {
            return $this->fail('mailbox mail number not defined');
        }

        $type = $this->params['type'];
        $num = $this->params['num'];

        try {
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        } catch (Exception $e) {
            return $this->fail('mailbox not available');
        }

        if (!isset($this->params['form']['target'])) {
            return $this->fail('target id not defined');
        }

        $target = trim($this->params['form']['target']);
        $noansi = isset($this->params['form']['noansi']);
        $big5 = isset($this->params['form']['big5']);

        try {
            $mail->forward($target, $noansi, $big5);
        } catch (MailSendException $e) {
            return $this->fail($e->getMessage());
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success();
    }

    public function deleteAction()
    {
        if (!isset($this->params['type'])) {
            return $this->fail('mailbox type not undefined');
        }

        if (!isset($this->params['num'])) {
            return $this->fail('mailbox mail number not defined');
        }

        $type = $this->params['type'];
        $num = $this->params['num'];

        try {
            $box = new MailBox(User::getInstance(), $type);
        } catch (MailBoxNullException $e) {
            return $this->fail('mailbox not available');
        }

        try {
            $mail = Mail::getInstance($num, $box);
            if (!$mail->delete()) {
                return $this->fail('delete failed');
            }
        } catch (Exception $e) {
            return $this->fail('delete failed');
        }

        return $this->success();
    }
}
