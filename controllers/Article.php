<?php
load('model/article');
class ArticleController extends NF_YambController {

    /**
     * board
     *
     * @var inc/model/board
     */
    private $board;

    public function init() {
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

            $meta = [
                "id" => $v->ID,
                "op" => ($v->OWNER == $u->userid || $bm) ? true : false,
                "time" => $this->formatTime($v->POSTTIME),
                "pos" => $v->getPos(),
                "content" => $content,
                "subject" => $v->isSubject(),
                "voted" => $liked['liked'] ? true : false,
                "promed" => $promed['flag'],
                "voteup_count" => $likesum['sum']
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
            $articleLike1 = $link->all("select * from dianzan_stat where threadid = ? and bname = ? and ( flag = 1 or ( flag = 0  and likesum > ?) ) order by flag desc, likesum desc ",array($gid, $bname, c("article.like_bor")));
            $sum = count($articleLike1);
            if($sum < c("article.like_lim")) {
                $articleLike2 = $link->all("select * from dianzan_stat where threadid = ? and bname = ? and flag = 0 and ( likesum between ? and ? ) order by flag desc, likesum desc limit ".(c("article.like_lim") - $sum), array($gid, $bname, c("article.like_min"), c("article.like_bor")));
                shuffle($articleLike2);
                $likes = array_merge($articleLike1, $articleLike2);
            } else {
                $likes = array_slice($articleLike1, 0, c("article.like_lim"));
            }

            // now wrap popular articles
            $popularReplies = [];
            foreach ($likes as $meta) {
                $article = $threads->getArticleById((int)$meta['articleid']);

                $data = [
                    'content' => $parseContent($article),
                    'time' => $this->formatTime($article->POSTTIME),
                    'pos' => $article->getPos(),
                    'id' => (int) $meta['articleid'],
                    'flag' => $meta['flag'],
                    'voteup_count' => $meta['likesum'],
                ];

                // check whether user has voted up 
                $user = User::getInstance();
                $voted = $link->one('SELECT COUNT(*) AS liked FROM `dianzan_usr` WHERE `articleid` = ? and `userid_like` = ? and `bname` = ?', array($meta['articleid'], $user->userid, $bname));
                $data['voted'] = ! empty($voted['liked']);

                try {
                    $data['poster'] = $wrapper->user(User::getInstance($article->OWNER));
                } catch(Exception $e) {
                    $data['poster'] = [ 'id' => $v->OWNER ];
                }

                $popularReplies[] = $data;
                unset($popularReplies['linkesum']);
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
        if ($isliked['liked'] >= c("article.like_per")) {
            return $this->fail('you have voted up');
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

}
