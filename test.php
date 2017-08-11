<?php

/* 简单的测试脚本 */

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

header("Content-type: text/html; charset=utf-8");
include 'helpers.php';

spk_dd(spk_get_address_by_ip('61.158.147.122'));