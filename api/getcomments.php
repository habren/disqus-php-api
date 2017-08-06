<?php
/**
 * 获取评论列表
 * 暂以 50 条每页，倒序为准
 *
 * @param link   页面链接
 * @param cursor 当前评论位置
 *
 * @author   fooleap <fooleap@gmail.com>
 * @version  2017-08-06 16:11:41
 * @link     https://github.com/fooleap/disqus-php-api
 *
 */
namespace Emojione;
require_once('init.php');

$fields_data = array(
    'api_key' => DISQUS_PUBKEY,
    'cursor' => $_GET['cursor'],
    'limit' => 50,
    'forum' => DISQUS_SHORTNAME,
    'order' => 'desc',
    'thread' => 'link:'.$website.$_GET['link']
);
$curl_url = '/api/3.0/threads/listPostsThreaded?'.http_build_query($fields_data);
$data = curl_get($curl_url);

$fields_data = array(
    'api_key' => DISQUS_PUBKEY,
    'forum' => DISQUS_SHORTNAME,
    'thread' => 'link:'.$website.$_GET['link']
);
$curl_url = '/api/3.0/threads/details.json?'.http_build_query($fields_data);
$detail = curl_get($curl_url);

$posts = array();
foreach ( $data -> response as $key => $post ) {
    $posts[$key] = post_format($post);
}

$isauth = strpos($session, 'session') !== false ? true : false;
$data -> auth = $isauth;

$output = $data -> code == 0 ? array(
    'auth' => $isauth,
    'code' => $detail -> code,
    'cursor' => $data -> cursor,
    'link' => 'https://disqus.com/home/discussion/'.DISQUS_SHORTNAME.'/'.$detail -> response -> slug.'/?l=zh',
    'posts' => $detail -> response -> posts,
    'response' => $posts,
    'thread' => $detail -> response -> id
) : $data;

print_r(json_encode($output));
