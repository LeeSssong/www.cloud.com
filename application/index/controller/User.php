<?php


namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Request;
use think\Session;

class User extends Base
{
    //登陆
    public function login(){
        $this->alreadyLogin();
        return $this->view->fetch();
    }

    //验证登陆
    /*
     * @param $status 状态码，成功则1，失败则0
     * @param $message 提示信息
     * */
    public function checkLogin(Request $request)
    {
        $status = 0;
        $result = '验证失败';
        $data = $request->param();

        //验证规则
        $rule = [
            'name|用户名' => 'require',
            'password|密码'=>'require',
        ];

        //验证数据 $this->validate($data, $rule, $msg)
        $result = $this -> validate($data, $rule);

        //通过验证后,进行数据表查询
        //此处必须全等===才可以,因为验证不通过,$result保存错误信息字符串,返回非零
        if (true === $result) {
            //查询条件
            $map = [
                'name' => $data['name'],
                'password' => $data['password']
            ];

            //数据表查询,返回模型对象
            $user = UserModel::get($map);
            if (null === $user) {
                $result = '没有该用户,请检查';
            } else {
                $status = 1;
                $result = '验证通过,点击[确定]后进入';

                //创建2个session,用来检测用户登陆状态和防止重复登陆
                Session::set('user_id', $user -> id);
                Session::set('user_info', $user -> getData());

            }
        }

        return ['status'=>$status, 'message'=>$result, 'data'=>$data];
    }

    //注册界面
    public function register()
    {
        return $this->view->fetch();
    }

    //注册逻辑
    //1.验证注册的用户名是否已被占用
    public function checkUserName(Request $request)
    {
        $userName = trim($request->param('name'));
        $status = 1;
        $message = '用户名不可用';
        if (!empty($userName)) {
            if (UserModel::get(['name' => $userName])) {
                //如果查询到该用户名
                $status = 0;
                $message = '用户名重复，请重新输入';
            } else {
                $message = '用户名可用';
            }
        } else {
            $message = '用户名不可为空';
        }

        return ['status'=>$status, 'message'=>$message];
    }

    //2.添加至数据库
    public function addUser(Request $request)
    {
        $status = 0;
        $result = '验证失败';
        $message = 'null';
        $swapMessage = '';
        $data = $request -> param();
        //验证规则
        $rule = [
            'name|用户名' => 'require',
            'password|密码'=>'require',
        ];

        //验证数据 $this->validate($data, $rule, $msg)
        $result = $this -> validate($data, $rule);

        if ($result === true) {
            $swapMessage = '此时result为true';
            $user = new UserModel($_POST);

            $user->allowField(true)->save();
            if ($user === null) {
                $status = 0;
                $message = '添加失败';
                $swapMessage = $message;
            } else {
                $status = 1;
                $message = '添加成功，点击[确定]返回登陆界面';
                $swapMessage = $message;
            }
        } else {
            $status = 0;
            $swapMessage = $result;
        }
        return ['status'=>$status, 'message'=>$swapMessage];    //传回register.html的data属性及其值
    }

    //个人中心
    /*
     * 1.个人信息：id、用户名、已上传图片数
     * 2.显示所有已上传图片
     * 选做：
     * 3.删除已上传照片
     * */
    public function index()
    {
        //TODO
    }

}