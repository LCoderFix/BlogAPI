
<?php
use \think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/
//用户登录
Route::post('user/login','api/user/login');
//用户获取验证码
Route::get('code/:time/:token/:username/:is_exist','api/code/get_code');
//用户注册
Route::post('user/register','api/user/register');
//用户上传头像
Route::post('user/icon','api/user/upload_head_img');
//用户修改密码
Route::post('user/change_pwd','api/user/change_pwd');
//用户找回密码
Route::post('user/find_pwd','api/user/find_pwd');
//用户绑定手机/邮箱
Route::post('user/bind_username','api/user/bind_username');
//用户修改昵称
Route::post('user/nickname','api/user/set_nickname');
/**********************article**************************/
//新增文章
Route::post('article','api/article/add_article');
//查看文章列表
Route::get('articles/:time/:token/:article_uid/[:num]/[:page]','api/article/article_list');
//查看单条文章
Route::get('article/:time/:token/:article_id','api/article/article_detail');
//修改文章
Route::put('article','api/article/update_article');
//删除文章
Route::delete('article/:time/:token/:article_id','api/article/del_article');