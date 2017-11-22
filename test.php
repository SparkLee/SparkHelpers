<?php

/* 简单的测试脚本 */

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

header("Content-type: text/html; charset=utf-8");
include 'helpers.php';

// spk_dd(spk_get_http_response_get('http://local.ts.huaray.com/api.php?mod=Public&act=x'));
spk_dd(spk_get_http_response_post('http://local.ts.huaray.com/api.php?mod=Public&act=x', [], ['timeout' => '200ms','return_error'=> 1]));
