<?php
/**
 * 上传图片到微博图床
 * @author mengkun  http://mkblog.cn
 * @param $file 图片文件/图片url
 * @param $multipart 是否采用multipart方式上传
 * @return 返回的json数据
 */
function upload($file, $multipart = true) {
    $cookie = '';    // 微博cookie
    $url = 'http://picupload.service.weibo.com/interface/pic_upload.php'
    .'?mime=image%2Fjpeg&data=base64&url=0&markpos=1&logo=&nick=0&marks=1&app=miniblog';
    if($multipart) {
        $url .= '&cb=http://weibo.com/aj/static/upimgback.html?_wv=5&callback=STK_ijax_'.time();
        if (class_exists('CURLFile')) {     // php 5.5
            $post['pic1'] = new CURLFile(realpath($file));
        } else {
            $post['pic1'] = '@'.realpath($file);
        }
    } else {
        $post['b64_data'] = base64_encode(file_get_contents($file));
    }
    // Curl提交
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_VERBOSE => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("Cookie: $cookie"),
        CURLOPT_POSTFIELDS => $post,
    ));
    $output = curl_exec($ch);
    curl_close($ch);
    // 正则表达式提取返回结果中的json数据
    preg_match('/({.*)/i', $output, $match);
    if(!isset($match[1])) return '';
    return $match[1];
}
?>