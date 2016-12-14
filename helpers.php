<?php
/* =========================================== */
/*   常用的扩展函数                            */
/*                                             */
/*   Author: Spark Lee                         */
/*                                             */
/*   Email:  liweijsj@163.com                  */
/*                                             */
/*   Since:  2016/07/25 16:15                  */
/*                                             */
/* =========================================== */

!defined('SPK_DO_LOG') && define('SPK_DO_LOG', TRUE); // 是否开户日志记录
!defined('SPK_LOG_DIR') && define('SPK_LOG_DIR', dirname(__FILE__) . '/logs/'); // 日志文件存储的根目录

if (!function_exists('spk_log')) {
    /**
     * 写日志
     * @author SparkLee
     * @since 2015/07/31 10:28
     * @param unknown $logfile_name 带子目录的文件名（如：ad/ad.log），注：文件扩展名必须.log
     * @param unknown $content 日志内容
     * @param string $flags 内容写入方式（覆盖，追加等）
     *  Available flags
     *   Flag Description
     *   FILE_USE_INCLUDE_PATH  Search for filename in the include directory. See include_path for more information.
     *   FILE_APPEND  If file filename already exists, append the data to the file instead of overwriting it.
     *   LOCK_EX  Acquire an exclusive lock on the file while proceeding to the writing.
     */
    function spk_log($logfile_name, $data, $title='', $log_level = 'INFO',  $flags = FILE_APPEND) {
        if (SPK_DO_LOG) {
            $index_of_last_slash = strrpos($logfile_name, '/');// 最后一个斜杠的位置
            $dir = SPK_LOG_DIR . substr($logfile_name, 0, $index_of_last_slash);            
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true); // 设置第三个参数$recursive为true，递归创建目录
            }
            $file_path = str_replace('.log', '-' . date('Y-m-d') . '.log', SPK_LOG_DIR . $logfile_name); // 把文件名a.log替换成a-2015-10-09.log

            // 非字符串的，一律转换为json字符串
            if(!is_string($data)) {
            	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }

            $data = date('Y-m-d H:i:s') . " [{$log_level}] {$title} - " . $data . "\r\n";

            // sdk目录下使用spk_log时（sdk客户端发HTTP请求打日志），日志文件中若有中文则会乱码（使用mb_http_input('I')可以检测HTTP输入字符编码），采用以下方式可以解决乱码问题
            // 如果你在 out_charset 后添加了字符串 //TRANSLIT，将启用转写（transliteration）功能。这个意思是，当一个字符不能被目标字符集所表示时，它可以通过一个或多个形似的字符来近似表达。 如果你添加了字符串 //IGNORE，不能以目标字符集表达的字符将被默默丢弃。 否则，str 从第一个无效字符开始截断并导致一个 E_NOTICE。
            $data = iconv('UTF-8', "GB2312//IGNORE", $data);
            $data = iconv('GB2312', "UTF-8//IGNORE", $data);

            file_put_contents($file_path, $data, $flags);
        }
    }
}

if (!function_exists('spk_dd')) {
    /**
     * 打印给定内容并结束脚本
     */
	function spk_dd() {
	    $args = func_get_args();
	    foreach ($args as $arg) {
	        var_dump($arg);
	    }
	    die;
	}
}

if (!function_exists('spk_float_cut')) {
    /**
     * 保留若干位小数，但不四舍五入
     *
     * @param $number 符点数
     * @param $decimals 要保留的小数位数
     */
    function spk_float_cut($number, $decimals) {
        return floatval(substr(sprintf("%." . strval(intval($decimals) + 1) . "f", $number), 0, -1));
    }
}

if(!function_exists('spk_human_filesize')) {
    /**
     * 由字节格式的文件大小转换为可读的文件大小
     *
     * @param $bytes 文件大小[字节数]
     * @param $decimals 保留小数位
     * @return 文件大小[M,G等可读格式]
     */
    function spk_human_filesize($bytes, $decimals = 2) {
      $sz = 'BKMGTP';
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}

if(!function_exists('spk_get_http_response_get')) {
    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * @param $url 指定URL完整路径地址
     * return 远程输出的数据
     */
    function spk_get_http_response_get($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 );        // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);  // 显示输出结果
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        
        return $responseText;
    }
}

if(!function_exists('spk_get_http_response_post')) {
    /**
     * 远程获取数据，POST模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * @param $url 指定URL完整路径地址
     * @param $para 请求的数据
     * @param $input_charset 编码格式。默认值：空值
     * return 远程输出的数据
     */
    function spk_get_http_response_post($url, $para, $input_charset = '') {
        if (trim($input_charset) != '') {
            $url = $url."_input_charset=".$input_charset;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST,true); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        
        return $responseText;
    }
}
