<?php


namespace app\index\controller;


use think\Request;

class Img extends Base
{
    //公共首页
    public function index()
    {
        return $this->fetch();
    }

    //上传：仅限图片
    public function uploadFile(Request $request)
    {
        /*
         * 1.判断文件是否符合oss规定图片格式
         * 2.生成文件名，格式：bucketName-Endpoint-userName-imgName-imgType
         * 3.上传
         * */


        //获得图像对象
        //TODO:判断图像的合法性
        //$resResult = Image::open($file);

        //判断图片格式的合法性
        $file = $request->file('file');  //获取到上传的文件
        $file = $_FILES['file'];
        if ($file) {
            $name = $file['name'];
            $format = strrchr($name, '.');//截取文件后缀名如 (.jpg)
            /*判断图片格式*/
            $allow_type = ['.jpg', '.jpeg', '.gif', '.bmp', '.png'];
            if (!in_array($format, $allow_type)) {
                return ['status'=>0, 'message'=>'文件格式不在允许范围内'];
            }
        }



        // 尝试执行
        try {
            $config = config('aliyun_oss'); //获取Oss的配置
            //实例化对象 将配置传入
            $ossClient = new OssClient($config['KeyId'], $config['KeySecret'], $config['Endpoint']);
            //这里是有sha1加密 生成文件名 之后连接上后缀
            //TODO：生成格式化的文件名
            $fileName = sha1(date('YmdHis', time()) . uniqid()) . '.' . $resResult->type();
            //执行阿里云上传
            $result = $ossClient->uploadFile($config['Bucket'], $fileName, $file->getInfo()['tmp_name']);

            $arr = [
                '图片地址:' => $result['info']['url'],
                '数据库保存名称' => $fileName
            ];
        } catch (OssException $e) {
            return $e->getMessage();
        }
        //将结果输出

//        return $arr;
        //列举指定bucket下的所有object
        $objectsList = $ossClient->listObjects('lisongsheng');


        dump($objectsList);
//        dump($arr);
    }
}