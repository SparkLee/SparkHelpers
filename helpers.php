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
        echo "<pre>";
        foreach ($args as $arg) {
            var_dump($arg);
        }
        echo "</pre>";
        die;
    }
}

if (! function_exists('spk_with')) {
    /**
     * 返回给定对象，适用于链式操作
     *
     * @param  mixed  $object
     * @return mixed
     */
    function spk_with($object)
    {
        return $object;
    }
}

if (! function_exists('spk_windows_os')) {
    /**
     * 判断当前操作系统是否为Windows
     *
     * @return bool
     */
    function spk_windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
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

if(!function_exists('spk_human_seconds')) {
    /**
     * 格式化时间秒数（此处说的是时间间隔而非时间戳）
     *
     * @param $seconds 秒数
     * @param $format
     *        1: 多少年多少天多少时多少分多少秒
     *        2: 多少年前 OR 多少天前 OR 多少小时前 OR 多少分钟前 OR 多少秒前
     * @return 可读的时间间隔
     */
    function spk_human_seconds($seconds, $format = 1) {
        $seconds = intval($seconds);

        if($seconds == 0) return '刚刚';
        
        $t = [
            31536000 => ['年', '年前'],
            86400    => ['天', '天前'],
            3600     => ['时', '小时前'],
            60       => ['分', '分钟前'],
            1        => ['秒', '秒前'],
        ];
        $s = '';
        switch ($format) {
            case 2:
                foreach ($t as $k => $v) {
                    if($seconds >= $k) {
                        $s = floor($seconds / $k) . $v[1];
                        break;
                    }                 
                }
                break;
            
            default:
                foreach ($t as $k => $v) {
                    if($seconds >= $k) $s .= floor($seconds / $k) . $v[0];
                    $seconds %= $k;
                }
                break;
        }
        
        return $s;
    }
}

if(!function_exists('spk_get_http_response_get')) {
    /**
     * HTTP GET请求
     *
     * 示例：spk_get_http_response_post('http://www.domain.com/', ['timeout' => '200ms','return_error'=> 1])
     */
    function spk_get_http_response_get($url, $opts = []) {
        return spk_get_http_response($url, 'get', [], $opts);
    }
}

if(!function_exists('spk_get_http_response_post')) {
    /**
     * HTTP POST请求
     * 
     * 示例：spk_get_http_response_post('http://www.domain.com/', ['name' => 'sparklee'], ['timeout' => '200ms','return_error'=> 1])
     */
    function spk_get_http_response_post($url, $para = [], $opts = []) {
        return spk_get_http_response($url, 'post', $para, $opts);
    }
}

if(!function_exists('spk_get_http_response')) {
    /**
     * 发起HTTP请求，获取远程数据
     * @param $url    指定URL完整路径地址
     * @param $method HTTP请求方法
     * @param $para   请求的数据
     * @param $opts   定制选项
     * @return 远程输出的数据
     */
    function spk_get_http_response($url, $method = 'get', $para = [], $opts = []) {
        // 1. 创建一个cURL Resource，并且设置请求地址
        $curl = curl_init($url);

        // 2. 设置其他选项
        curl_setopt($curl, CURLOPT_HEADER, 0 );         // TRUE to include the header in the output. 0：过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);  // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 不验证请求地址的https证书

        // 2.1. The maximum number of seconds to allow cURL functions to execute：如果$opts['timeout']设置为0，则相当于cURL执行永不超时
        if(isset($opts['timeout'])) {
            // 毫秒级
            if(substr($opts['timeout'], -2) == 'ms') {
                $_timeout = intval(str_replace('ms', '', $opts['timeout']));
                curl_setopt($curl, CURLOPT_TIMEOUT_MS, $_timeout);

                // libcurl在(Li|U)nix操作系统下如果设置了小于1000ms的超时以后, curl不会发起任何请求, 而直接返回超时错误(Timeout reached 28)。需要添加CURLOPT_NOSIGNAL选项以解决此问题。
                // @see：惠新辰-Curl的毫秒超时的一个Bug：http://www.laruence.com/2014/01/21/2939.html
                // @see: php curl CURLOPT_TIMEOUT_MS 小于1秒 解决方案：https://www.cnblogs.com/sky20081816/archive/2013/05/30/3108657.html
                curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
                
            // 秒级[不带超时单位，默认为秒]
            } else {
                $_timeout = intval(str_replace('s', '', $opts['timeout']));
                curl_setopt($curl, CURLOPT_TIMEOUT, $_timeout);
            }

           unset($_timeout);
        } else {
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        }

        // 2.2. cookie设置
        !empty($opts['cookie']) && curl_setopt($curl, CURLOPT_COOKIE, $opts['cookie']);

        // 2.3 ua设置
        !empty($opts['useragent']) && curl_setopt($curl, CURLOPT_USERAGENT, $opts['useragent']);

        // 2.4 代理设置（一般用于测试，可在本地开启Fiddler抓包调试，Fiddler默认端口是888；示例：$opts['proxy'] = '127.0.0.1:8888'）
        !empty($opts['proxy']) && curl_setopt ($curl, CURLOPT_PROXY, $opts['proxy']);

        // 2.5. POST请求特殊选项
        if(strtolower($method) == 'post') {
            curl_setopt($curl,CURLOPT_POST, true);

            // POST请求数据
            // 如果$para是urlencoded字符串形式即'para1=val1&para2=val2&...'，则请求的Content-Type为application/x-www-form-urlencoded
            // 如果$para是数组，则Content-Type会被设置为multipart/form-data。由于表单上传文件时Content-Type要设置为multipart/form-data，故如果要上传文件则$para必须是数组
            if(!empty($opts['postdata_str'])) {
                $_tmp_para = '';
                foreach ($para as $key => $value) {
                    $_tmp_para .= "{$key}={$value}&";
                }
                $para = trim($_tmp_para, '&');
            }
            curl_setopt($curl,CURLOPT_POSTFIELDS, $para);
        }

        // 2.6 请求头（$opts['httpheader'] = ["Content-Type: text/xml; charset=utf-8", "Expect: 100-continue", "Authorization:APPCODE xxxxxx", ......]）
        !empty($opts['httpheader']) && curl_setopt($curl, CURLOPT_HTTPHEADER, $opts['httpheader']);

        // 3. grab URL and return the transfer as a sting
        $responseText = curl_exec($curl);  // Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, FALSE on failure.
        $lastErrNo    = curl_errno($curl); // Returns the error number or 0 (zero) if no error occurred.[see: https://curl.haxx.se/libcurl/c/libcurl-errors.html]
        $lastErrMsg   = curl_error($curl); // Returns the error message or '' (the empty string) if no error occurred.
        $responseInfo = curl_getinfo($curl); // Get information regarding a specific transfer

        // 4. close cURL resource, and free up system resources
        curl_close($curl);

        if(!empty($opts['return_error'])) {
           if($lastErrNo) return "cURL异常：error_no={$lastErrNo} | error_msg={$lastErrMsg} | ".__FILE__.'->'.__FUNCTION__.'('.__LINE__.')'; // 如：error_no=6，域名无法解析

           if(in_array($responseInfo['http_code'], [400, 403])) return "response_info=".json_encode($responseInfo, JSON_UNESCAPED_UNICODE);
        }

        if(!empty($opts['return_with_info'])) { // 请求响应结果和请求其他响应信息一同返回，方便分析或记录请求的响应详细情况，如：请求响应状态，请求耗时等
            return [
                'responseText' => $responseText,
                'responseInfo' => $responseInfo,
                'responseErr'  => ['lastErrNo' => $lastErrNo, 'lastErrMsg' => $lastErrMsg],
            ];
        }

        return $responseText;
    }
}

if(!function_exists('spk_get_client_ip')) {
    /**
     * 获取客户端IP地址
     * @param int  $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv  是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function spk_get_client_ip($type = 0, $adv = false) {
        $type = $type? 1 : 0;
        if($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // IP地址合法性验证
        $long = sprintf('%u', ip2long($ip));
        $ip = $long? array($ip, $long) : array('0.0.0.0', 0);

        return $ip[$type];
    }
}

if(!function_exists('spk_get_address_by_ip')) {
    /**
     * 根据IP地址获取地理位置（国家、地区、省、市、运营商）
     * @param $ip IP地址
     * @return 地理位置数组
     */
    function spk_get_address_by_ip($ip = null) {
        $ip = $ip? : spk_get_client_ip();
        $address = spk_get_http_response_get("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
        $address = json_decode($address, true);
        return empty($address['data'])? [] : $address['data'];
    }
}

if(!function_exists('spk_is_valide_x')) {
    /**
     * 正则验证指定内容的合法性
     *
     * @param $xtype    待验证内容的类型
     * @param $xcontent 待验证内容
     * @return bool     true:合法 false:非法
     */
    function spk_is_valide_x($xtype, $xcontent) {
        $regx_rule = [
            'phone' => '/^1[3456789][0-9]{1}[0-9]{8}$/',                           // 手机号码验证规则
            'email' => '/[_a-zA-Z\d\-\.]+(@[_a-zA-Z\d\-\.]+\.[_a-zA-Z\d\-]+)+$/i', // 邮箱验证规则
        ];

        if(preg_match($regx_rule[$xtype], $xcontent)) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('spk_gen_md5_sign')) {
    /**
     * 使用MD5算法，生成指定参数数组数据对应的签名字符串
     *
     * @see 签名算法参考微信支付的签名算法：https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_3
     *
     * @param  array  $param 待签名的数据
     * @param  string $token 签名密钥
     * @return string 签名字符串
     */
    function spk_gen_md5_sign($data, $token) {
        // 0、待签名数据必须是非空数组
        if(empty($data) || !is_array($data)) {
            return "";
        }

        // 1、过滤掉空值参数（值恒等于空的参数，亦即值为0或0.00的参数正常参与签名）和签名参数
        $data_filter = [];
        foreach ($data as $key => $val) {
            if($key == "sign" || $val === "") continue;
            $data_filter[$key] = $val;
        }
        unset($key, $val);

        // 2、按参数名ASCII码正序排序
        ksort($data_filter);
        reset($data_filter); // 将排序后数组的内部指针指向第一个单元
        
        // 3、使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串
        $str  = "";
        foreach ($data_filter as $key => $val) {
            $str .= "{$key}={$val}&"; // 注：值没有urlencode，而是原样参与签名
        }
        unset($key, $val);

        // 4、拼接密钥
        $str .= "token={$token}"; // 注：上文的$str的末尾有一个"&"，以处无需再加"&"

        // 5、MD5签名
        $sign = md5($str);

        // 6、签名转大写
        $sign = strtoupper($sign);

        return $sign;
    }
}