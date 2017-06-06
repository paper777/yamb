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

        foreach ($articles as $v) {
            $content = $v->getPlant();

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
            $content = $v->parseAtt($content, 'middle');
            if(c("ubb.parse")){
                load('inc/ubb');
                $content = XUBB::parse($content);
            }
            bbs_brcaddread($this->board->NAME, $v->ID);

            $db = DB::getInstance();
            $likesum = $db->one('select count(*) as sum from dianzan_usr where articleid = ? and bname = ?',array($v->ID,$this->board->NAME));
            $liked = $db->one('select count(*) as liked from dianzan_usr where articleid = ? and userid_like = ? and bname = ?',array($v->ID,$u->userid,$this->board->NAME));
            $promed = $db->one('select flag from dianzan_stat where articleid = ? and bname = ?',array($v->ID,$this->board->NAME));

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

        $data = [
            'gid' => $threads->GROUPID,
            'anony' => $this->board->isAnony(),
            'reid' => $threads->ID,
            'time' => $this->formatTime($threads->POSTTIME),
            'title' => nforum_html($threads->TITLE),
            'board' => $wrapper->board($this->board),
            'articles' => $info,
            'pagination' => [
                'current' => $pagination->getCurPage(),
                'total' => $pagination->getTotalPage()
            ],
        ];
        return $this->success($data);
    }

    
}
