<?php


namespace app\api\controller\v1;


use think\Controller;
use think\Image;
use think\Request;
use think\Validate;

class Common extends Controller
{
    protected $request;
    protected $validate;
    protected $params;

    protected $val;//过滤多余控制器+方法参数
    protected $val1;//过滤多余控制器方法

    protected $rules = array(
        'user' => array(
            'login' => array(
                'user_name' => 'require',
                'user_pwd' => 'require|length:32',
            ),
            'register' => array(
                'user_name' => 'require',
                'user_pwd' => 'require|length:32',
                'code' => 'require|number|length:6',
            ),
            'upload_head_img' => array(
                'user_id' => 'require|number',
                'user_icon' => 'require|image|fileSize:5000000|fileExt:jpg,png,bmp,jpeg',
            ),
            'change_pwd' => array(
                'user_name' => 'require',
                'user_ini_pwd' => 'require|length:32',
                'user_pwd' => 'require|length:32',
            ),
            'find_pwd' => array(
                'user_name' => 'require',
                'user_pwd' => 'require|length:32',
                'code' => 'require|number|length:6',
            ),
            'bind_username' => array(
                'user_id' => 'require|number',
                'user_name' => 'require',
                'code' => 'require|number|length:6',
            ),
            'set_nickname' => array(
                'user_id' => 'require|number',
                'user_nickname' => 'require|chsDash',
            )
        ),
        'code' => array(
            'get_code' => array(
                'username' => 'require',
                'is_exist' => 'require|number|length:1'
            )
        ),
        'article' => array(
            'add_article' => array(
                'article_uid' => 'require|number',
                'article_title' => 'require|chsDash',
            ),
            'article_list' => array(
                'article_uid' => 'require|number',
                'num' => 'number',
                'page' => 'number',
            ),
            'article_detail' => array(
                'article_id' => 'require|number',
            ),
            'update_article' => array(
                'article_id' => 'require|number',
                'article_title'=>'chsDash',
            ),
            'del_article' => array(
                'article_id' => 'require|number',
            ),
        )
    );

    protected function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
        $conrtoller = strtolower($this->request->controller());
        $this->val = '/' . $conrtoller;

      //  $this->check_time($this->request->only(['time']));
       // $this->check_token($this->request->param());
        $this->check_params($this->filterParam($this->request->param(true)));

    }

    /**
     * 验证请求是否超时
     * @param $arr
     */
    public function check_time($arr)
    {
        if (!isset($arr['time']) || intval($arr['time']) <= 1) {
            $this->return_msg(400, "时间戳不存在");
        }
        if (time() - intval($arr['time']) > 60) {
            $this->return_msg(400, "请求超时");

        }
    }

    /**
     *
     * @param $code  返回状态码
     * @param string $msg
     * @param array $data
     */
    public function return_msg($code, $msg = '', $data = [])
    {
        $return_data['code'] = $code;
        $return_data['msg'] = $msg;
        $return_data['data'] = $data;
        echo json_encode($return_data);
        die;
    }

    public function check_token($arr)
    {
        if (!isset($arr['token']) || empty($arr['token'])) {
            $this->return_msg(400, 'token不能为空');
        }
        $app_token = $arr['token'];
        unset($arr['token']);
        $service_token = '';
        foreach ($arr as $key => $value) {
            $service_token .= md5($value);

        }
        $service_token = md5('api_' . $service_token . '_api');
        if ($app_token !== $service_token) {
            $this->return_msg(400, "token值不正确!");
        }
    }

    /**
     * 参数统一过滤
     * @param $arr
     */
    public function check_params($arr)
    {
       // var_dump(substr($this->request->controller(),3).$this->request->action());
        $rule = $this->rules[substr($this->request->controller(),3)][$this->request->action()];
        $this->validate = new Validate($rule);
        if (!$this->validate->check($arr)) {
            $this->return_msg(400, $this->validate->getError());
        }
        $this->params = $arr;
    }

    public function check_exist($value, $type, $exist)
    {
        $type_num = $type == 'phone' ? 2 : 4;
        $flag = $type_num + $exist;
        $phone_res = db('user')->where('user_phone', $value)->find();
        $email_res = db('user')->where('user_email', $value)->find();
        switch ($flag) {
            case 2:
                if ($phone_res)
                    $this->return_msg(400, "此手机号已被占用");
                break;
            case 3:
                if (!$phone_res)
                    $this->return_msg(400, "此手机号不存在");
                break;
            case 4:
                if ($email_res)
                    $this->return_msg(400, "此邮箱已被占用");
                break;
            case 5:
                if (!$email_res)
                    $this->return_msg(400, "此邮箱不存在");
                break;

        }
    }

    public function check_code($user_name, $code)
    {
        /**********************检测是否超时****************************/
        $last_time = session($user_name . '_last_send_time');
        if (time() - $last_time > 60) {
            $this->return_msg(400, '请在60秒内验证');
        }
        /******************检测验证码是否正确****************/
        $md5_code = md5($user_name . '_' . md5($code));
        if (session($user_name . '_code') !== $md5_code) {
            $this->return_msg(400, '验证码不正确');
        }
        /**********************不论是否成功，验证码只验证一次****************************/
        session($user_name . '_code', null);
    }

    public function filterParam($arr)
    {
        foreach ($arr as $key => $value) {
            if (preg_match("^/.*^", $key))
                unset($arr[$key]);
        }
        return $arr;
    }

    /**
     * 检查用户名类型
     * @param $username
     * @return string
     */
    public function check_username($username)
    {
        $is_email = Validate::is($username, 'email') ? 1 : 0;
        $is_phone = preg_match('/^1[34578]\d{9}$/', $username) ? 4 : 2;
        $flag = $is_email + $is_phone;
        switch ($flag) {
            case 2:
                $this->return_msg(400, "邮箱或手机号不正确");
                break;
            case 3:
                return 'email';
                break;

            case 4:
                return 'phone';
                break;

        }
    }

    /**
     * 上传文件
     * @param $file 文件
     * @param $type 文件处理类型
     * @return mixed 存储位置
     */
    public function upload_file($file, $type)
    {
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        dump($info->getSaveName());
        if ($info) {
            $path = '/uploads/' . $info->getSaveName();
            //裁剪图片
            if (!empty($type)) {
                $this->image_edit($path, $type);
            }
            return str_replace('\\', '/', $path);
        } else {
            $this->return_msg(400, $info->getError());
        }
    }

    public function image_edit($path, $type)
    {
        $image = Image::open(ROOT_PATH . 'public' . $path);

        switch ($type) {
            case 'head_image':
                $image->thumb(200, 200, Image::THUMB_CENTER)->save(ROOT_PATH . 'public' . $path);
                break;
        }
    }

}