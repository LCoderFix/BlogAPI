<?php

namespace app\index\controller;
use think\Db;
use think\Validate;

class Index
{
    public function index()
    {
//
//        $request = Request::instance();
//        echo "请求方法:" . $request->method() . '<br/>';
//        echo "请求地址:" . $request->ip() . '<br/>';
//        echo "请求参数：";
//        dump($request->param());
//        echo '请求参数仅包含name';
//        dump($request->only(['name']));
//        echo "请求参数排除name";
//        dump($request->except(['name']));


/*
        $rule=[
            'name'=>'require|max:25',
            'age'=>'number|between:1,120',
            'email'=>'email',
        ];
        $msg=[
            'name.require'=>'name',
            'name.max'=>'名称最多不能超过25个字符',
            'age.number'=>'年龄必须是数字',
            'age.between'=>'年龄只能在1-120之间',
            'email'=>'邮箱格式错误',
        ];
        $data=input('post.');
        dump($data);
        $validate=new Validate($rule,$msg);
        $result=$validate->check($data);
        if(!$result){
            dump($validate->getError());
        }

*/

    $res=Db::query("select version()");
    return $res;
    }
}
