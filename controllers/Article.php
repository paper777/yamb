<?php
class ArticleController extends NF_YambController {

    /**
     * board
     *
     * @var inc/model/board
     */
    private $board;

    private $fromType = 2;

    private $tex = 0;

    public function init() {
        load('model/article');
        parent::init();

        if (! isset($this->params['name'])) {
            $this->abort();
        }

        try {
            load('model/board');
            $boardName = $this->params['name'];
            $this->board = Board::getInstance($boardName);
        } catch(BoardNullException $e) {
            $this->abort();
        }

        if (! $this->board->hasReadPerm(User::getInstance())) {
            $this->fail('啊呀，没有权限呃');
        }

        $this->board->setOnBoard();
    }

    public function indexAction() {
        try {
            load('model/threads');
            $gid = $this->params['gid'];
            $threads = Threads::getInstance($gid, $this->board);
        } catch (TreadsNullException $e) {
            $this->abort();
        }

        load(["inc/pagination", "inc/astro"]);
        $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 1;
        $pagination = new Pagination($threads, c("pagination.article"));
        $articles = $pagination->getPage($page);

        $u = User::getInstance();
        $bm = $u->isBM($this->board) || $u->isAdmin();

        load(["inc/db", "inc/wrapper"]);
        $wrapper = Wrapper::getInstance();

        $parseContent = function ($article) {
            $content = $article->getPlant();

            $content = preg_replace("/&nbsp;/", " ", $content);
            $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);

            // get source
            preg_match("|※ 来源:(.*)FROM:.*|", $content, $f);
            $source = empty($f) ? "北邮人论坛" : $f[1];

            // remove bottom lines
            $s = (($pos = strpos($content, "<br/><br/>")) === false) ? 0 : $pos + 10;
            $e = (($pos = strpos($content, "<br/>--<br/>")) === false) 
               ? strlen($content)
               : $pos + 7;
            $content = preg_replace(
                array("'^(<br/>)+'", "|(<br/>)+--$|")
                ,array("", "<br>--")
                ,substr($content, $s, $e - $s)
            );

            // parse attachments
            $content = $article->parseAtt($content, 'middle');
            if (c("ubb.parse")) {
                load('inc/ubb');
                $content = XUBB::parse($content);
            }

            return $content;
        };

        foreach ($articles as $v) {
            $content = $parseContent($v);
            bbs_brcaddread($this->board->NAME, $v->ID);

            $db = DB::getInstance();

            $likesum = $db->one('select count(*) as sum from dianzan_usr where articleid = ? and bname = ?',array($v->ID, $this->board->NAME));
            $liked = $db->one('select count(*) as liked from dianzan_usr where articleid = ? and userid_like = ? and bname = ?',array($v->ID, $u->userid, $this->board->NAME));
            $promed = $db->one('select flag from dianzan_stat where articleid = ? and bname = ?',array($v->ID, $this->board->NAME));
            //2017-9 lj add
            $votedown_sum = $db->one('select count(*) as sum from votedown_usr where articleid = ? and bname = ?',array($v->ID, $this->board->NAME));
            $user_choose = $db->one('select count(*) as votedown from votedown_usr where articleid = ? and userid = ? and bname = ?',array($v->ID, $u->userid, $this->board->NAME));

            $meta = [
                "id" => $v->ID,
                "op" => ($v->OWNER == $u->userid || $bm) ? true : false,
                "time" => $this->formatTime($v->POSTTIME),
                "pos" => $v->getPos(),
                "content" => $content,
                "subject" => $v->isSubject(),
                "voted" => $liked['liked'] ? true : false,
                "promed" => $promed['flag'],
                "voteup_count" => $likesum['sum'],
                //2017-9 lj add
                "votedown" => $user_choose['votedown'],
                "votedown_count" => $votedown_sum['sum']
            ];
            try {
                $meta['poster'] = $wrapper->user(User::getInstance($v->OWNER));
            } catch(Exception $e) {
                $meta['poster'] = [ 'id' => $v->OWNER ];
            }
            $info[] = $meta;
        }


        // popular replies
        // @see app\controllersr\Article
        if ($page == 1) {
            $link = DB::getInstance();
            $bname = $this->board->NAME;
            //2017-9 lj noted
//            $articleLike1 = $link->all("select * from dianzan_stat where threadid = ? and bname = ? and
//                                            ( flag = 1 or ( flag = 0  and likesum > ?) ) order by flag desc, likesum desc ",
//                                        array($gid, $bname, c("article.like_bor")));
//            $sum = count($articleLike1);
//            if($sum < c("article.like_lim")) {
//                $articleLike2 = $link->all("select * from dianzan_stat where threadid = ? and bname = ? and flag = 0 and ( likesum between ? and ? ) order by flag desc, likesum desc limit ".(c("article.like_lim") - $sum), array($gid, $bname, c("article.like_min"), c("article.like_bor")));
//                shuffle($articleLike2);
//                $likes = array_merge($articleLike1, $articleLike2);
//            } else {
//                $likes = array_slice($articleLike1, 0, c("article.like_lim"));
//            }
            //2017-9 lj add
            $up_query = $link->all("SELECT articleid FROM `dianzan_stat` WHERE threadid = ? and bname = ?  and 
                                      ( flag = 1 or ( flag = 0  and likesum > ? ) )",
                array($gid, $bname));
            $up_articles = [];
            foreach ($up_query as $up_one){
                $up_articles[] = $up_one['articleid'];
            }
            $up_articles = implode(",",$up_articles);

            $relative_up_1 = $link->all("select * from (SELECT up.articleid, ifnull(up.likesum,0)-ifnull(down.unlikesum,0) as relative_sum,
ifnull(up.flag,0) as flag,ifnull(up.likesum,0) as upsum,ifnull(down.unlikesum,0) as downsum FROM  `dianzan_stat` AS up LEFT JOIN  `votedown_stat`
 AS down ON up.articleid = down.articleid WHERE up.articleid IN ? ) AS test WHERE test.relative_sum>?  or test.flag >0 order by test.flag desc, test.relative_sum desc",
                array($up_articles,c("article.like_bor")));

            $sum = count($relative_up_1);

            if($sum < c("article.like_lim")) {
                $relative_up_2 = $link->all("select * from (SELECT up.articleid, ifnull(up.likesum,0)-ifnull(down.unlikesum,0) as relative_sum,
ifnull(up.flag,0) as flag,ifnull(up.likesum,0) as upsum,ifnull(down.unlikesum,0) as downsum FROM  `dianzan_stat` AS up LEFT JOIN  `votedown_stat`
 AS down ON up.articleid = down.articleid WHERE up.articleid IN ? ) AS test WHERE test.flag = 0 and (test.relative_sum between ? and ? ) 
 order by test.relative_sum desc limit ".(c("article.like_lim") - $sum), array(c("article.like_min"), c("article.like_bor")));
                shuffle($relative_up_2);
                $relative_up = array_merge($relative_up_1, $relative_up_2);
            } else {
                $relative_up = array_slice($relative_up_1, 0, c("article.like_lim"));
            }

            // now wrap popular articles
            $popularReplies = [];
            foreach ($relative_up as $meta) {
                $article = $threads->getArticleById((int)$meta['articleid']);

                $data = [
                    'content' => $parseContent($article),
                    'time' => $this->formatTime($article->POSTTIME),
                    'pos' => $article->getPos(),
                    'id' => (int) $meta['articleid'],
                    'flag' => $meta['flag'],
                    'voteup_count' => $meta['upsum'],
                    'votedown_count' => $meta['downsum'],
                ];

                // check whether user has voted up or down
                $user = User::getInstance();
                $voted_up = $link->one('SELECT COUNT(*) AS up FROM `dianzan_usr` WHERE `articleid` = ? and `userid_like` = ? 
                              and `bname` = ?', array($meta['articleid'], $user->userid, $bname));
                $voted_down = $link->one('SELECT COUNT(*) AS down FROM `votedown_usr` WHERE `articleid` = ? and `userid` = ? 
                              and `bname` = ?', array($meta['articleid'], $user->userid, $bname));
                $data['voted'] = ! empty($voted_up['liked']);
                $data['voteddown'] = ! empty($voted_down['down']);

                try {
                    $data['poster'] = $wrapper->user(User::getInstance($article->OWNER));
                } catch(Exception $e) {
                    $data['poster'] = [ 'id' => $article->OWNER ];
                }

                $popularReplies[] = $data;
                unset($popularReplies['linkesum']);
            }
        }

        try {
            $head = Article::getInstance($gid, $this->board);
        } catch(ArticleNullException $e) {
            $head = null;
        }
        if ($head) {
            $db = DB::getInstance();
            $likesum = $db->one('select count(*) as sum from dianzan_usr where articleid = ? and bname = ?',array($head->ID, $this->board->NAME));
            $liked = $db->one('select count(*) as liked from dianzan_usr where articleid = ? and userid_like = ? and bname = ?',array($head->ID, $u->userid, $this->board->NAME));
            $promed = $db->one('select flag from dianzan_stat where articleid = ? and bname = ?',array($head->ID, $this->board->NAME));

            $main = [
                "id" => $head->ID,
                "time" => $this->formatTime($head->POSTTIME),
                "voted" => $liked['liked'] ? true : false,
                "promed" => $promed['flag'],
                "voteup_count" => $likesum['sum']
                ];
            try {
                $main['poster'] = $wrapper->user(User::getInstance($head->OWNER));
            } catch(Exception $e) {
                $main['poster'] = [ 'id' => $head->OWNER ];
            }
        }

        $data = [
            'gid' => $threads->GROUPID,
            'anony' => $this->board->isAnony(),
            'reid' => $threads->ID,
            'time' => $this->formatTime($threads->POSTTIME),
            'title' => nforum_html($threads->TITLE),
            'board' => $wrapper->board($this->board),
            'articles' => $info,
            'head' => $main,
            'popularReplies' => isset($popularReplies) ? $popularReplies : false,
            'pagination' => [
                'current' => $pagination->getCurPage(),
                'total' => $pagination->getTotalPage()
            ],
        ];
        return $this->success($data);
    }

    public function voteupAction() {
        if (! $this->getRequest()->isPost()) {
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
        load('inc/db');

        // pre check
        $link = DB::getInstance();
        $isliked = $link->one('SELECT COUNT(*) AS liked FROM `dianzan_usr` WHERE `articleid` = ? and `userid_like` = ? and `bname` = ?', array($id, $user->userid, $bname));
        //lj add
        $isunliked = $link->one('SELECT COUNT(*) AS unliked FROM `votedown_usr` WHERE `articleid` = ? and `userid` = ? and `bname` = ?', array($id, $user->userid, $bname));
        if ($isliked['liked'] >= c("article.like_per")) {
            return $this->fail('you have voted up');
        }
        //lj add
        if( $isunliked['unliked'] > 0){
            return $this->fail('you have voted down');
        }

        $score = $user->score_user;
        if ($score < max(c("article.min_need_score"), c("article.like_sub"))) {
            return $this->fail('score not enough');
        }

        // vote
        $val = [
            'userid_like' => $user->userid,
            'articleid' => $id,
            'bname' => $bname
        ];
        $link->insert('dianzan_usr', $val);

        $sum = $link->one('SELECT COUNT(*) AS likesum FROM `dianzan_usr` WHERE `articleid`= ? and `bname` = ?',array($id, $bname));
        $sum = $sum['likesum'];

        // update state log
        $isblank = $link->one('SELECT COUNT(*) AS blank FROM `dianzan_stat` WHERE `articleid` = ? and `bname` = ?',array($id, $bname));
        if ($isblank['blank'] == 0) {
            $val = [
                'articleid' => $id,
                'threadid' => $gid,
                'bname' => $bname,
                'likesum' => $sum,
            ];
            $link->insert('dianzan_stat', $val);
        } else {
            $link->update('dianzan_stat', array('likesum' => $sum), 'WHERE `articleid` = ? and `bname`= ?', array($id, $bname));
        }

        // modify user socre
        try {
            $poster = User::getInstance($article->OWNER);
            $user->modifyScore(c("article.like_sub") * -1, '为用户 ' . $poster->userid . ' 点赞 ' . $bname . '版 主题帖ID:' . $gid);
            $poster->modifyScore(c("article.like_add"), '用户 ' . $user->userid  . ' 为你点赞 ' . $bname . '版 主题帖ID:' . $gid);
        } catch(UserNullException $e) {
            return $this-fail('failed');
        }

        return $this->success(['count' => $sum]);
    }

    public function prereplyAction() {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (! $this->board->hasPostPerm(User::getInstance())) { 
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
        } catch(ArticleNullException $e) {
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

    public function prepostAction() {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (! $this->board->hasPostPerm(User::getInstance())) { 
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

    public function postAction() {
        if(! $this->getRequest()->isPost()) {
            return $this->abort();
        }

        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }
        if (! $this->board->hasPostPerm(User::getInstance())) { 
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
            } catch(ArticleNullException $e) {
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
        $email = 0; $anony = null; $outgo = 0;
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
        } catch(ArticlePostException $e) {
            return $this->fail('操作失败');
        }
        return $this->success();
    }

    public function preditAction() {
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }

        if ($this->board->isNoReply()) {
            return $this->fail('只读版面');
        }

        if (! $this->board->hasPostPerm(User::getInstance())) { 
            return  $this->fail('缺少权限');
        }

        $article = false;
        // reply mode
        if (! isset($this->params['gid'])) {
            return $this->fail('未知的帖子');
        }

        $gid = (int) $this->params['gid'];
        try {
            $article = Article::getInstance($gid, $this->board);
        } catch(ArticleNullException $e) {
            return $this->fail('目标帖未找到');
        }

        if ($article->isNoRe()) {
            return $this->fail('目标帖不可回复');
        }

        if (! $article->hasEditPerm(User::getInstance())) {
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
            'title' => $article->TITLE,
            'content' => $article->getContent(),
            'attachment' => $this->board->isAttach(),
            'attachments' => $atts
        ]);
    }

    public function editAction() {
        if (! $this->getRequest()->isPost()) {
            $this->abort();
        }
        if ($this->board->isReadOnly()) {
            return  $this->fail('只读版面');
        }

        if ($this->board->isNoReply()) {
            return $this->fail('只读版面');
        }

        if (! $this->board->hasPostPerm(User::getInstance())) { 
            return  $this->fail('缺少权限');
        }

        $article = false;
        // reply mode
        if (! isset($this->params['gid'])) {
            return $this->fail('未知的帖子');
        }

        $gid = (int) $this->params['gid'];
        try {
            $article = Article::getInstance($gid, $this->board);
        } catch(ArticleNullException $e) {
            return $this->fail('目标帖未找到');
        }

        if ($article->isNoRe()) {
            return $this->fail('目标帖不可回复');
        }

        if (! $article->hasEditPerm(User::getInstance())) {
            return $this->fail('无编辑权限');
        }       

        $subject = trim($this->params['form']['subject']);
        $content = trim($this->params['form']['content']);
        $subject = nforum_iconv($this->encoding, 'GBK', $subject);
        $content = nforum_iconv($this->encoding, 'GBK', $content);
        if (! $article->update($subject, $content)) {
            return $this->fail('操作失败');
        }

        return $this->success();
    }

    public function votedownAction() {
        if (! $this->getRequest()->isPost()) {
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
        load('inc/db');

        // pre check
        $link = DB::getInstance();
        $isliked = $link->one('SELECT COUNT(*) AS liked FROM `dianzan_usr` WHERE `articleid` = ? and `userid_like` = ? and `bname` = ?', array($id, $user->userid, $bname));
        $isunliked = $link->one('SELECT COUNT(*) AS unliked FROM `votedown_usr` WHERE `articleid` = ? and `userid` = ? and `bname` = ?', array($id, $user->userid, $bname));
        if ($isunliked['unliked'] >= c("article.unlike_per")) {
            return $this->fail('you have voted down');
        }
        if( $isliked['liked'] > 0){
            return $this->fail('you have voted up');
        }

//        $score = $user->score_user;
//        if ($score < max(c("article.min_need_score"), c("article.like_sub"))) {
//            return $this->fail('score not enough');
//        }

        // vote
        $val = [
            'userid' => $user->userid,
            'articleid' => $id,
            'bname' => $bname
        ];
        $link->insert('votedown_usr', $val);

        $sum = $link->one('SELECT COUNT(*) AS unlikesum FROM `votedown_usr` WHERE `articleid`= ? and `bname` = ?',array($id, $bname));
        $sum = $sum['unlikesum'];

        // update state log
        $isblank = $link->one('SELECT COUNT(*) AS blank FROM `votedown_stat` WHERE `articleid` = ? and `bname` = ?',array($id, $bname));
        if ($isblank['blank'] == 0) {
            $val = [
                'articleid' => $id,
                'threadid' => $gid,
                'bname' => $bname,
                'unlikesum' => $sum,
            ];
            $link->insert('votedown_stat', $val);
        } else {
            $link->update('votedown_stat', array('unlikesum' => $sum), 'WHERE `articleid` = ? and `bname`= ?', array($id, $bname));
        }

        return $this->success(['count' => $sum]);
    }
}
