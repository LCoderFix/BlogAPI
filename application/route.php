
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
Route::post('api/v1/user/login','api/v1.user/login');
//用户获取验证码
Route::get('api/v1/code/:time/:token/:username/:is_exist','api/v1.code/get_code');
//用户注册
Route::post('api/v1/user/register','api/v1.user/register');
//用户上传头像
Route::post('api/v1/user/icon','api/v1.user/upload_head_img');
//用户修改密码
Route::post('api/v1/user/change_pwd','api/v1.user/change_pwd');
//用户找回密码
Route::post('api/v1/user/find_pwd','api/v1.user/find_pwd');
//用户绑定手机/邮箱
Route::post('api/v1/user/bind_username','api/v1.user/bind_username');
//用户修改昵称
Route::post('api/v1/user/nickname','api/v1.user/set_nickname');
/**********************article**************************/
//新增文章
Route::post('api/v1/article','api/v1.article/add_article');
//查看文章列表
Route::get('api/v1/articles/:time/:token/:article_uid/[:num]/[:page]','api/v1.article/article_list');
//查看单条文章
Route::get('api/v1/article/:time/:token/:article_id','api/v1.article/article_detail');
//修改文章
Route::put('api/v1/article','api/v1.article/update_article');
//删除文章
Route::delete('api/v1/article/:time/:token/:article_id','api/v1.article/del_article');