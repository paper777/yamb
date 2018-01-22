<?php

/*
 * Yamb - A module for NForum, a replacement of Mobile Module
 *
 * @auther    paper777 <wuzhyy@163.com>
 *
 */

class ArticleController extends NF_YambController
{
    /**
     * board.
     *
     * @var inc/model/board
     */
    private $board;

    private $fromType = 2;

    private $tex = 0;

    public function init()
    {
        load('model/article');
        parent::init();

        if (!isset($this->params['name'])) {
            $this->abort();
        }

        try {
            load('model/board');
            $boardName = $this->params['name'];
            $this->board = Board::getInstance($boardName);
        } catch (BoardNullException $e) {
            $this->abort();
        }

        if (!$this->board->hasReadPerm(User::getInstance())) {
            $this->fail('啊呀，没有权限呃');
        }

        $this->board->setOnBoard();
    }

    public function indexAction()
    {
        try {
            load('model/threads');
            $gid = $this->params['gid'];
            $threads = Threads::getInstance($gid, $this->board);
        } catch (TreadsNullException $e) {
            $this->abort();
        }

        load(['inc/pagination', 'inc/astro']);
        $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 1;
        $pagination = new Pagination($threads, c('pagination.article'));
        $articles = $pagination->getPage($page);

        $u = User::getInstance();
        $bm = $u->isBM($this->board) || $u->isAdmin();

        load(['inc/db', 'inc/wrapper']);
        $wrapper = Wrapper::getInstance();

        $parseContent = function ($article) {
            $content = $article->getPlant();

            $content = preg_replace('/&nbsp;/', ' ', $content);
            $content = preg_replace('/  /', '&nbsp;&nbsp;', $content);

            // get source
            preg_match('|※ 来源:(.*)FROM:.*|', $content, $f);
            $source = empty($f) ? '北邮人论坛' : $f[1];

            // remove bottom lines
            $s = (($pos = strpos($content, '<br/><br/>')) === false) ? 0 : $pos + 10;
            $e = (($pos = strpos($content, '<br/>--<br/>')) === false)
                ? strlen($content)
                : $pos + 7;
            $content = preg_replace(
                ["'^(<br/>)+'", '|(<br/>)+--$|'], ['', '<br>--'], substr($content, $s, $e - $s)
            );

            // parse attachments
            $content = $article->parseAtt($content, 'middle');
            if (c('ubb.parse')) {
                load('inc/ubb');
                $content = XUBB::parse($content);
            }

            return $content;
        };

        foreach ($articles as $v) {
            $content = $parseContent($v);
            bbs_brcaddread($this->board->NAME, $v->ID);

            $db = DB::getInstance();
            //modified by lj
            load('model/article');
            $likesum = Article::findVoteupNum($v->ID, $this->board->NAME);
            $liked = Article::findHisVoteupNum($v->ID, $u->userid, $this->board->NAME);
            $promed = $db->one('select flag from dianzan_stat where articleid = ? and bname = ?', [$v->ID, $this->board->NAME]);

            $votedown_sum = Article::findVotedownNum($v->ID, $this->board->NAME);
            $unliked = Article::findHisVotedownNum($v->ID, $u->userid, $this->board->NAME);

            $meta = [
                'id'             => $v->ID,
                'op'             => ($v->OWNER == $u->userid || $bm) ? true : false,
                'time'           => $this->formatTime($v->POSTTIME),
                'pos'            => $v->getPos(),
                'content'        => $content,
                'subject'        => $v->isSubject(),
                'voted'          => $liked > 0 ? true : false,
                'promed'         => $promed['flag'],
                'voteup_count'   => $likesum,
                'voteddown'      => $unliked > 0 ? true : false,
                'votedown_count' => $votedown_sum,
                'votedown_min'   => c('article.votedown_min'),
            ];

            try {
                $meta['poster'] = $wrapper->user(User::getInstance($v->OWNER));
            } catch (Exception $e) {
                $meta['poster'] = ['id' => $v->OWNER];
            }
            $info[] = $meta;
        }

        // popular replies, modified by lj
        // @see app\controllersr\Article
        if ($page == 1) {
            $link = DB::getInstance();
            $bname = $this->board->NAME;

            $relative_up = Article::niceComment($gid, $bname);

            // now wrap popular articles
            $popularReplies = [];
            foreach ($relative_up as $meta) {
                $article = $threads->getArticleById((int) $meta['articleid']);

                $data = [
                    'content'        => $parseContent($article),
                    'time'           => $this->formatTime($article->POSTTIME),
                    'pos'            => $article->getPos(),
                    'id'             => (int) $meta['articleid'],
                    'flag'           => $meta['flag'],
                    'voteup_count'   => $meta['likesum'],
                    'votedown_count' => $meta['csum'],
                ];

                // check whether user has voted up or down
                $user = User::getInstance();
                $voted_up = Article::findHisVoteupNum($meta['articleid'], $user->userid, $bname);
                $voted_down = Article::findHisVotedownNum($meta['articleid'], $user->userid, $bname);
                $data['voted'] = $voted_up > 0 ? true : false;
                $data['voteddown'] = $voted_down > 0 ? true : false;

                try {
                    $data['poster'] = $wrapper->user(User::getInstance($article->OWNER));
                } catch (Exception $e) {
                    $data['poster'] = ['id' => $article->OWNER];
                }

                $popularReplies[] = $data;
//                unset($popularReplies['likesum']);
            }
        }

        try {
            $head = Article::getInstance($gid, $this->board);
        } catch (ArticleNullException $e) {
            $head = null;
        }

        $main = null;
        if ($head) {
            $db = DB::getInstance();
            $likesum = Article::findVoteupNum($head->ID, $this->board->NAME);
            $votedown_sum = Article::findVotedownNum($head->ID, $this->board->NAME);
            $liked = Article::findHisVoteupNum($head->ID, $u->userid, $this->board->NAME);
            $promed = $db->one('select flag from dianzan_stat where articleid = ? and bname = ?', [$head->ID, $this->board->NAME]);
            $unliked = Article::findHisVotedownNum($v->ID, $u->userid, $this->board->NAME);

            $main = [
                'id'             => $head->ID,
                'time'           => $this->formatTime($head->POSTTIME),
                'voted'          => $liked > 0 ? true : false,
                'voteddown'      => $unliked > 0 ? true : false,
                'promed'         => $promed['flag'],
                'voteup_count'   => $likesum,
                'votedown_count' => $votedown_sum,

            ];

            try {
                $main['poster'] = $wrapper->user(User::getInstance($head->OWNER));
            } catch (Exception $e) {
                $main['poster'] = ['id' => $head->OWNER];
            }
        }

        $data = [
            'gid'            => $threads->GROUPID,
            'anony'          => $this->board->isAnony(),
            'reid'           => $threads->ID,
            'time'           => $this->formatTime($threads->POSTTIME),
            'title'          => nforum_html($threads->TITLE),
            'board'          => $wrapper->board($this->board),
            'articles'       => $info,
            'head'           => $main,
            'popularReplies' => isset($popularReplies) ? $popularReplies : false,
            'pagination'     => [
                'current' => $pagination->getCurPage(),
                'total'   => $pagination->getTotalPage(),
            ],
        ];

        return $this->success($data);
    }

    public function voteupAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->abort();
        }

        if (empty($this->params['gid'])) {
            return $this->fail('article id is not defined');
        }
        $id = (int) $this->params['gid'];

        try {
            $article = Article::getInstance($id, $this->board);
        } catch (ArticleNullException $e) {
            return $this->fail('article null');
        }

        // if ($article->isSubject()) {
        //     return $this->fail('subject is not allowed');
        // }

        $user = User::getInstance();

        $gid = $article->GROUPID;
        $bname = $this->board->NAME;
        load('inc/db');

        //modified by lj
        load('model/article');
        if ($id == $gid) {
            $voteup_result = Article::supportInsert($id, $bname, $this->board, $this);
        } else {
            $voteup_result = Article::voteupInsert($id, $bname, $this->board, $this);
        }
        if ($voteup_result['ajax_code'] == '1601' || $voteup_result['ajax_code'] == '1604'
            || $voteup_result['ajax_code'] == '1607' || $voteup_result['ajax_code'] == '0309') {
            return $this->success(['up_count' => $voteup_result['data']['lsum'], 'down_count' => $voteup_result['data']['csum']]);
        } else {
            return $this->fail($voteup_result['ajax_code']);
        }
    }

    //add by lj
    public function votedownAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->abort();
        }

        if (empty($this->params['form']['id'])) {
            return $this->fail('article id is not defined');
        }
        $id = $this->params['form']['id'];

        try {
            $article = Article::getInstance($id, $this->board);
        } catch (ArticleNullException $e) {
            return $this->fail('article null');
        }

        // if ($article->isSubject()) {
        //     return $this->fail('subject is not allowed');
        // }

        $user = User::getInstance();

        $gid = $article->GROUPID;
        $bname = $this->board->NAME;

        load('model/article');
        if ($id == $gid) {
            $votedown_result = Article::opposeInsert($id, $bname, $this->board, $this);
        } else {
            $votedown_result = Article::votedownInsert($id, $bname, $this->board, $this);
        }

        if ($votedown_result['ajax_code'] == '1609') {
            return $this->success(['up_count' => $votedown_result['data']['lsum'], 'down_count' => $votedown_result['data']['csum']]);
        } else {
            return $this->fail($votedown_result['ajax_code']);
        }
    }

    public function prereplyAction()
    {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (!$this->board->hasPostPerm(User::getInstance())) {
            return  $this->fail('缺少权限或者未登录');
        }

        if (empty($this->params['gid'])) {
            return $this->fail('文章不存在');
        }

        if ($this->board->isNoReply()) {
            return $this->fail('只读版面');
        }

        $reID = (int) $this->params['gid'];

        try {
            $article = Article::getInstance($reID, $this->board);
        } catch (ArticleNullException $e) {
            return $this->fail('文章不存在');
        }
        if ($article->isNoRe()) {
            return $this->fail('目标帖不可回复');
        }
        $data = [
            'attachment' => $this->board->isAttach(),
        ];

        return $this->success($data);
    }

    public function prepostAction()
    {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (!$this->board->hasPostPerm(User::getInstance())) {
            return  $this->fail('缺少权限或者未登录');
        }

        if ($this->board->isTmplPost()) {
            return $this->fail('版面仅限模板发帖');
        }
        $data = [
            'attachment' => $this->board->isAttach(),
        ];

        return $this->success($data);
    }

    public function postAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->abort();
        }

        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (!$this->board->hasPostPerm(User::getInstance())) {
            return  $this->fail('缺少权限');
        }

        $article = false;
        // reply mode
        if (isset($this->params['gid'])) {
            if ($this->board->isNoReply()) {
                return $this->fail('只读版面');
            }

            $reID = (int) $this->params['gid'];

            try {
                $article = Article::getInstance($reID, $this->board);
            } catch (ArticleNullException $e) {
                return $this->fail('目标帖未找到');
            }
            if ($article->isNoRe()) {
                return $this->fail('目标帖不可回复');
            }
        } else {
            if ($this->board->isTmplPost()) {
                return $this->fail('版面仅限模板发帖');
            }
            $reID = 0;
        }

        if (empty($this->params['form']['subject'])) {
            return $this->fail('标题不能为空');
        }
        if (empty($this->params['form']['content'])) {
            return $this->fail('内容不能为空');
        }

        $subject = trim($this->params['form']['subject']);
        $content = trim($this->params['form']['content']);
        $subject = nforum_iconv($this->encoding, 'GBK', $subject);
        $content = nforum_iconv($this->encoding, 'GBK', $content);

        //$subject = rawurldecode($subject);
        $sig = User::getInstance()->signature;
        $email = 0;
        $anony = null;
        $outgo = 0;
        if (isset($this->params['form']['anony']) && $this->board->isAnony()) {
            $anony = 1;
        }
        if (isset($this->params['form']['outgo']) && $this->board->isOutgo()) {
            $outgo = 1;
        }

        try {
            if (false === $article) { // new post
                Article::post($this->board, $subject, $content, $sig, $email, $anony, $outgo, $this->tex, $this->fromType);
            } else { // reply
                $article->reply($subject, $content, $sig, $email, $anony, $outgo, $this->tex, $this->fromType);
            }
        } catch (ArticlePostException $e) {
            return $this->fail('操作失败');
        }

        return $this->success();
    }

    public function preditAction()
    {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }

        if ($this->board->isNoReply()) {
            return $this->fail('只读版面');
        }

        if (!$this->board->hasPostPerm(User::getInstance())) {
            return  $this->fail('缺少权限');
        }

        $article = false;
        // reply mode
        if (!isset($this->params['gid'])) {
            return $this->fail('未知的帖子');
        }

        $gid = (int) $this->params['gid'];

        try {
            $article = Article::getInstance($gid, $this->board);
        } catch (ArticleNullException $e) {
            return $this->fail('目标帖未找到');
        }

        if ($article->isNoRe()) {
            return $this->fail('目标帖不可回复');
        }

        if (!$article->hasEditPerm(User::getInstance())) {
            return $this->fail('无编辑权限');
        }

        $atts = $article->getAttList();
        foreach ($atts as &$att) {
            $iconv = nforum_iconv($this->encoding, 'GBK', $att['name']);
            if ($iconv !== false) {
                $att['name'] = $iconv;
            }
        }

        return $this->success([
            'title'       => $article->TITLE,
            'content'     => $article->getContent(),
            'attachment'  => $this->board->isAttach(),
            'attachments' => $atts,
        ]);
    }

    public function editAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->abort();
        }
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }

        if ($this->board->isNoReply()) {
            return $this->fail('只读版面');
        }

        if (!$this->board->hasPostPerm(User::getInstance())) {
            return  $this->fail('缺少权限');
        }

        $article = false;
        // reply mode
        if (!isset($this->params['gid'])) {
            return $this->fail('未知的帖子');
        }

        $gid = (int) $this->params['gid'];

        try {
            $article = Article::getInstance($gid, $this->board);
        } catch (ArticleNullException $e) {
            return $this->fail('目标帖未找到');
        }

        if ($article->isNoRe()) {
            return $this->fail('目标帖不可回复');
        }

        if (!$article->hasEditPerm(User::getInstance())) {
            return $this->fail('无编辑权限');
        }

        $subject = trim($this->params['form']['subject']);
        $content = trim($this->params['form']['content']);
        $subject = nforum_iconv($this->encoding, 'GBK', $subject);
        $content = nforum_iconv($this->encoding, 'GBK', $content);
        if (!$article->update($subject, $content)) {
            return $this->fail('操作失败');
        }

        return $this->success();
    }
}
