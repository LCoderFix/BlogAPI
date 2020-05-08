<?php


namespace app\api\controller;


class Article extends Common
{
    public function add_article()
    {
        $data = $this->params;
        $data['article_ctime'] = time();
        $res = db('article')->insertGetId($data);
        if ($res) {
            $this->return_msg(200, "新增文章成功!" . $res);
        } else {
            $this->return_msg(400, "新增文章失败!");
        }
    }

    public function article_list()
    {
        $data = $this->params;

        if (!isset($data['num'])) {
            $data['num'] = 10;
        }
        if (!isset($data['page'])) {
            $data['page'] = 1;
        }

        $where['article_uid'] = $data['article_uid'];
        $where['article_isdel'] = 0;
        $count = db('article')->where($where)->count();
        $page_num = ceil($count / $data['num']);
        $field = "article_id,article_ctime,article_title,article_content,user_nickname";
        $join = [['api_user u', 'u.user_id=a.article_uid']];
        $res = db('article')->alias('a')->field($field)->join($join)->where($where)->page($data['page'], $data['num'])->select();
        if ($res === false) {
            $this->return_msg(400, "查询失败!");

        } elseif (empty($res)) {
            $this->return_msg(200, '暂无数据!');
        } else {
            $return_data['articles'] = $res;
            $return_data['page_num'] = $page_num;
            $this->return_msg(200, '查询成功!', $return_data);
        }
    }

    public function article_detail()
    {
        $data = $this->params;
        $where['article_id'] = $data['article_id'];
        $field = 'article_id,article_uid,article_title,article_content,article_ctime';
        $join = [['api_user u', 'u.user_id=a.article_uid']];
        $res = db('article')->alias('a')->join($join)->field($field)->where($where)->find();
        $res['article_content'] = htmlspecialchars_decode($res['article_content']);
        if ($res) {
            $this->return_msg(200, '查询成功!', $res);
        } else {
            $this->return_msg(400, '查询失败');
        }
    }

    public function update_article()
    {
        $data = $this->params;

        $where['article_id'] = $data['article_id'];

        $res = db('article')->where($where)->update($data);

        if ($res !== false) {
            $this->return_msg(200, '更新成功!');
        } else {
            $this->return_msg(400, '更新失败!');
        }
    }

    public function del_article()
    {
        $data = $this->params;

        $res=db('article')->where('article_id',$data['article_id'])->setField('article_isdel',1);
        if($res){
            $this->return_msg(200,'删除成功!');
        }else{
            $this->return_msg(400,'删除失败!');
        }
    }
}