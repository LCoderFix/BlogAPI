<?php


namespace app\api\controller;

use PHPMailer\PHPMailer\PHPMailer;
use think\response\Json;
use think\Validate;

class Code extends Common
{
    function get_code()
    {
        $username = $this->params['username'];
        $exist = $this->params['is_exist'];
        $username_type = $this->check_username($username);
        switch ($username_type) {
            case 'phone':
                $this->get_code_by_username($username, 'phone', $exist);
                break;
            case 'email':
                $this->get_code_by_username($username, 'email', $exist);
                break;
        }
    }



    /**
     * @param $username
     * @param $type
     * @param $exist
     */
    public function get_code_by_username($username, $type, $exist)
    {
        if ($type == 'phone') {
            $type_name = '手机';
        } else {
            $type_name = '邮箱';
        }
        /*1.检测手机号是否存在*/
        $this->check_exist($username, 'phone', $exist);
        /*2.检查验证码请求频率*/
        if (session("?" . $username . '_last_send_time')) {
            if (time() - session($username . '_last_send_time') < 30) {
                $this->return_msg(400, $type_name . "验证码请求过于频繁");
            }
        }
        /*3.生成验证码*/
        $code = $this->make_code(6);
        /*4.使用session存储验证码*/
        $md5_code = md5($username . '_' . md5($code));
        session($username . '_code', $md5_code);
        /*5.使用session存储验证码发送时间*/
        session($username . '_last_send_time', time());
        /*6.发送验证码*/
        if ($type == 'phone') {
            $this->send_code_to_phone($username, $code);
        } else {
            $this->send_code_to_email($username, $code);
        }
    }

    /**
     * @param $num
     * @return int
     */
    public function make_code($num)
    {
        $max = pow(10, $num) - 1;
        $min = pow(10, $num - 1);
        return rand($min, $max);
    }

    public function send_code_to_phone($phone, $code)
    {
    //    echo 'send_code_to_phone($username,$code)';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://api.feige.ee/SmsService/Template');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        $data =[
            'Account'=>'',
            'Pwd'=>'bbcdca7f23d2bdc68e27b3212',
            'Content'=>$code.'||'.'60',
            'Mobile'=>$phone,
            'TemplateId'=>142796,
            'SignId'=>139005,
        ];
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        $res=curl_exec($curl);
        curl_close($curl);
        $res=json_decode($res);
       if($res->Code!=0){
            $this->return_msg(400,$res->Message);
        }else{
            $this->return_msg(200,'手机验证码已发送!');
        }

    }

    public function send_code_to_email($email, $code)
    {
        echo 'send_code_to_email($username,$code)';
        $mail = new \PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'utf-8';
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = "507085831@qq.com";
        $mail->Password = "ffrtrruljbkvbiie";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('507085831@qq.com', '接口测试');
        $mail->addAddress($email, 'test');
        $mail->addReplyTo('507085831@qq.com', "Reply");
        $mail->Subject = "您有新的验证码";
        $mail->Body = "这是一个测试邮件，您的验证码是$code,验证码的有效期为一分钟，本邮件请勿回复！";
        if (!$mail->send()) {
            $this->return_msg(400, $mail->ErrorInfo);
        } else {
            $this->return_msg(200, "验证码已经发送成功，请注意查收");
        }

    }
}