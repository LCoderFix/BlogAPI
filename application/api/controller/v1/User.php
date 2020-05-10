<?php

namespace app\api\controller\v1;

class User extends Common
{
    public function index($id)
    {
        echo 'controller: User';
        echo '<br/>';
        echo $id;
    }

    public function register()
    {
        $data = $this->params;
        //检查验证码
        $this->check_code($data['user_name'], $data['code']);
        //检查用户名
        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 0);
                $data['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 0);
                $data['user_emial'] = $data['user_name'];
                break;

        }
        unset($data['user_name']);
        $data['user_rtime'] = time();
        $res = db('user')->insert($data);
        if (!$res) {
            $this->return_msg(400, '用户注册失败!');
        } else {
            $this->return_msg(200, '用户注册成功!');
        }
        echo 'register';
    }

    public function login()
    {
        $data = $this->params;
        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $db_res = db('user')
                    ->field('user_id,user_name,user_phone,user_email,user_pwd,user_rtime')
                    ->where('user_phone', $data['user_name'])
                    ->find();
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $db_res = db('user')
                    ->field('user_id', 'user_name', 'user_pwd',
                        'user_phone', 'user_email', 'user_rtime')
                    ->where('user_email', $data['user_name'])
                    ->find();
                break;
        }
        if ($db_res['user_pwd'] !== $data['user_pwd']) {
            $this->return_msg(400, '密码不正确!');
        } else {
            unset($db_res['user_pwd']);
            $this->return_msg(200, '登录成功!', $db_res);
        }

    }

    public function upload_head_img()
    {
        $data = $this->params;

        $head_img_path = $this->upload_file($data['user_icon'], 'head_image');
        $res = db('user')->where('user_id',
            $data['user_id'])->setField('user_icon', $head_img_path);
        if ($res) {
            $this->return_msg(200, '头像上传成功!', $head_img_path);
        } else {
            $this->return_msg(400, "上传头像失败!");
        }
    }

    public function change_pwd()
    {
        $data = $this->params;
        $user_name_type = $this->check_username($data['user_name']);

        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $where['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $where['user_email'] = $data['user_name'];
                break;
        }

        $res = db('user')->where($where)->value('user_pwd');
        if ($res !== $data['user_ini_pwd']) {
            $this->return_msg(400, '原密码错误!');
        }
        $res = db('user')->where($where)->setField('user_pwd', $data['user_pwd']);
        if ($res !== false) {
            $this->return_msg(200, '修改密码成功!');
        } else {
            $this->return_msg(400, '密码修改失败!');
        }
    }

    public function find_pwd()
    {
        $data = $this->params;
        $username = $data['user_name'];
        $code = $data['code'];
        $this->check_code($username, $code);
        $user_name_type = $this->check_username($username);
        //检测用户是否存在
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $where['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $where['user_email'] = $data['user_name'];
                break;
        }

        $res = db('user')->where($where)->setField('user_pwd', $data['user_pwd']);

        if ($res !== false) {
            $this->return_msg(200, '密码找回成功!');
        } else {
            $this->return_msg(400, '密码找回失败!');
        }
    }

    public function bind_username()
    {
        //获取参数
        $data = $this->params;
        //检查验证码
        $this->check_code($data['user_name'], $data['code']);

        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $updata['user_phone'] = $data['user_name'];
                $val = '手机号';
                break;
            case 'email':
                $updata['user_email'] = $data['user_name'];
                $val = '邮箱';
                break;
        }
        $res = db('user')->where('user_id', $data['user_id'])->update($updata);

        if ($res !== false) {
            $this->return_msg(200, $val . "绑定成功!");
        } else {
            $this->return_msg(400, $val . "绑定失败!");
        }
    }

    public function set_nickname()
    {
        $data = $this->params;

        $res = db('user')->where('user_nickname', $data['user_nickname'])->find();
        if ($res) {
            $this->return_msg(400, "该昵称已被占用!");
        }

        $res = db('user')
            ->where('user_id', $data['user_id'])->setField('user_nickname', $data['user_nickname']);

        if (!$res) {
            $this->return_msg(400,'昵称修改失败!');
        }else{
            $this->return_msg(200,'昵称修改成功!');
        }
    }
}