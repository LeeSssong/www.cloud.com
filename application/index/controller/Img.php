<?php


namespace app\index\controller;

use OSS\Core\OssException;
use OSS\OssClient;
use think\Request;

class Img extends Base
{
    //公共首页
    public function index()
    {
        return $this->fetch();
    }

    //上传：仅限图片
    /*
     * 1.判断文件是否符合oss规定图片格式
     * 2.生成文件名，格式：bucketName-Endpoint-userName-imgName-imgType
     * 3.上传
     * */
    public function uploadFile(Request $request)
    {
        //判断图片格式的合法性
        //$file = $request->file('file');  //获取到上传的文件
        $file = $_FILES['file'];
        $user_id = '201741413330';//后期用session
        if ($file) {
            //获取文件名
            $name = $file['name'];
            $format = strrchr($name, '.');//截取文件后缀名如 (.jpg)
            /*判断图片格式*/
            $allow_type = ['.jpg', '.jpeg', '.gif', '.bmp', '.png','.webp'];
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
            //生成格式化的文件名,https://bucket.endpoint/$user_id/Y-m-d/name.ext
            $fileName = $user_id . '/'. date("Y-m-d") . '/' . sha1(date('YmdHis', time()) . uniqid()) . $format;
            //执行阿里云上传,bucket,object名字,文件
            $result = $ossClient->uploadFile($config['Bucket'], $fileName, $file['tmp_name']);

            $arr = [
                'url' => $result['info']['url'],  //上传资源地址
                'relative_path' => $fileName
            ];
        } catch (OssException $e) {
            return $e->getMessage();
        }
        dump($arr);
    }
}