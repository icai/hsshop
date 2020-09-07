<?php

if (!function_exists('appsuccess')) {
    function appsuccess($errMsg = '操作成功', $data = [])
    {
        appIntToString($data);
        $content = [
            'errCode' => 40000,
            'errMsg' => $errMsg,
            'data' => $data
        ];

        $content = json_encode($content);
        $uri = \Route::current()->getUri();
        if (in_array($uri, config('sellerapp.exceptionUri'))) {
            //\Log::info($content);
            header('Content-Type:application/json');
            exit($content);
        }
        $content = (new \App\Module\RSAModule())->encrypt($content);
        exit($content);
    }
}

if (!function_exists('apperror')) {
    function apperror($errMsg = '操作失败', $code = 40001, $data = [])
    {
        appIntToString($data);
        $content = [
            'errCode' => $code,
            'errMsg' => $errMsg,
            'data' => $data
        ];
        $content = json_encode($content);
        $uri = \Route::current()->getUri();
        if (in_array($uri, config('sellerapp.exceptionUri'))) {
            //\Log::info($content);
            header('Content-Type:application/json');
            exit($content);
        }
        $content = (new \App\Module\RSAModule())->encrypt($content);
        exit($content);
    }
}


if (!function_exists('appIntToString')) {
    function appIntToString(&$data)
    {
        foreach ($data as $key => &$itemVal) {
            if (is_array($itemVal)) {
                appIntToString($itemVal);
            } else {
                if (!is_string($itemVal)) {
                    $itemVal = strval($itemVal);
                } elseif (is_null($itemVal)) {
                    $itemVal = '';
                }
            }
        }
    }
}

/**
 * 小程序接口返回规范
 */
if (!function_exists('xcxsuccess')) {
    function xcxsuccess($hint = '操作成功', $data = null)
    {
        $content = [
            'code' => 40000,
            'hint' => $hint,
            'list' => $data
        ];

        header('Content-Type:application/json');
        exit(json_encode($content));
    }
}

if (!function_exists('xcxerror')) {
    function xcxerror($hint = '操作失败', $code = -100, $data = null)
    {
        $content = [
            'code' => $code,
            'hint' => $hint,
            'list' => $data
        ];

        header('Content-Type:application/json');
        exit(json_encode($content));
    }
}


if (!function_exists('mysuccess')) {
    function mysuccess($info = '操作成功', $url = '', $data = null)
    {

        $content = array('status' => 1, 'info' => $info, 'url' => $url, 'data' => $data);
        if (app('request')->expectsJson()) {
            return response()->json($content);
        } else {
            return response()->view('errors.hint', $content)->send();
        }
    }
}

if (!function_exists('myerror')) {
    function myerror($info = '操作失败', $url = '', $data = null)
    {
        $content = array('status' => 0, 'info' => $info, 'url' => $url, 'data' => $data);

        if (app('request')->expectsJson()) {
            return response()->json($content);
        } else {
            return response()->view('errors.hint', $content)->send();
        }
    }
}


/**
 * 返回错误信息 自动判断请求是否需要返回json，不需要则跳转至提示页
 *
 * @param string $info 提示信息
 * @param string $url 跳转url
 * @param null $data 返回数据
 *
 *
 * @throws \App\Exceptions\CommonException
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年4月1日 17:06:47
 *
 * @update: 梅杰[meijie3169@dingtalk.com] at 2019年08月22日 11:42:35
 */

if (!function_exists('error')) {
    function error($info = '操作失败', $url = '', $data = null)
    {
        throw new \App\Exceptions\CommonException($info, $data, $url);
    }
}

if (!function_exists('success')) {
    /**
     * 返回错误信息
     *
     * 自动判断请求是否需要返回json，不需要则跳转至提示页
     *
     * @param  string $info 提示信息
     * @param  string $url 跳转url
     * @param  mixed $data 返回数据
     * @return mixed
     */
    function success($info = '操作成功', $url = '', $data = null)
    {
        $content = array('status' => 1, 'info' => $info, 'url' => $url, 'data' => $data);

        if (app('request')->expectsJson()) {
            header('Content-Type:application/json');
            exit(json_encode($content));
        } else {
            response()->view('errors.hint', $content)->send();
            exit;
        }
    }
}

if (!function_exists('is_empty')) {
    /**
     * check if is empty
     *
     * @param mixed $var
     * @return bool
     */
    function is_empty($var)
    {
        return empty($var);
    }
}

if (!function_exists('getClassPath')) {
    /**
     * 获取类文件所在路径
     *
     * @param  string $className 类名
     * @return string  $path      类文件所在路径
     */
    function getClassPath($className)
    {
        $reflection = new ReflectionClass($className);

        return $reflection->getFileName();
    }
}

if (!function_exists('get_numeric')) {
    /**
     * 数字类型数据转化
     *
     * @param  mixed $val [要转化的数据]
     * 示例：
     * get_numeric('3'); // int(3)
     * get_numeric('1.2'); // float(1.2)
     * get_numeric('3.0'); // float(3)
     * @return mixed      [转化后的数据]
     */
    function get_numeric($val)
    {
        if (is_numeric($val)) {
            return $val + 0;
        }
        return false;
    }
}

if (!function_exists('numFormat')) {
    /**
     * 数字型数据格式化
     *
     * @param  mixed $number [要转化的数据]
     * @return mixed
     */
    function numFormat($number)
    {
        $fm = number_format($number, 2);
        if (strval($fm) == '0.00') {
            $fm = number_format($number, 4);
            if (strval($fm) == '0.0000') {
                return '0.00';
            }
        }
        return $fm;
    }
}

if (!function_exists('idencrypt')) {
    /**
     * 数据id加解密
     *
     * @param  mixed $string [需要加解密的数据id]
     * @param  boolean $operation [操作类型：true代表加密；false代表解密]
     * @return string
     */
    function idencrypt($string, $operation = true)
    {
        $key = md5(config('app.key'));
        $key_length = strlen($key);
        $string = str_replace('{}', '/', $string);
        $string = $operation === false ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = [];
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == false) {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace(['=', '/'], ['', '{}'], base64_encode($result));
        }
    }
}

if (!function_exists('urlencrypt')) {
    /**
     * url加密
     * @param  mixed $id [需要加解密的数据id]
     * @param  id $uri [跳转路由uri，注意开头不要加反斜杠，因为在环境配置中项目url在最后加了反斜杠]
     * @param  boolean $operation [操作类型：true代表加密；false代表不加密]
     * @param  array $paramList [url参数数组，由于微商城前台路由不统一，有些wid放到url参数中获取，故强制加上wid参数]
     * @param  integer $wid [店铺id，默认取session中的wid]
     * @return id
     */
    function urlencrypt($id = 0, $uri = '', $operation = true, $paramList = [], $wid = 0)
    {
        if ($wid === 0 && empty(session('wid'))) {
            error('url生成失败');
        }
        $wid = $wid ?: session('wid');
        if ($operation === true) {
            $token = $id ? '/' . idencrypt($id, $operation) : '';
        } else {
            $token = $id ? '/' . $id : '';
        }
        $param = '?wid=' . $wid;
        if (count($paramList)) {
            foreach ($paramList as $key => $value) {
                $param .= '&' . $key . '=' . $value;
            }
        }
        return config('app.url') . 'shop' . $uri . '/' . $wid . $token . $param;
    }
}

if (!function_exists('specialEncode')) {
    /**
     * 转义特殊字符
     *
     * @param  string $str [需要被转义的字符串]
     * @return string      [转义后的字符串]
     */
    function specialEncode($str)
    {
        $search = array("'<script[^>]*?>.*?</script>'si", // 去掉 javascript
            "'<[\/\!]*?[^<>]*?>'si",      // 去掉 HTML 标记
            "'([\r\n])[\s]+'",         // 去掉空白字符
            "'&(quot|#34);'i",         // 替换 HTML 实体
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'e");

        $replace = array("",
            "",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\\1)");

        return preg_replace($search, $replace, $str);
    }
}

if (!function_exists('getJsonCurl')) {
    /**
     * json返回值的curl请求
     *
     * @param  string $url [请求地址]
     * @param  array $datas [请求数据，为空发送get请求，反之发送post请求]
     * @param  integer $second [超时时间，单位：秒]
     * @return array           [请求成功返回请求返回的数据数组，否则直接调用失败方法]
     */
    function jsonCurl($url, $datas = [], $second = 30)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '119.29.29.29');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 80);
        if (stripos($url, 'https://') !== false) {
            // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 自动设置Referer
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        if (!empty($datas)) {
            // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POST, true);
            // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errNo = curl_error($ch);
        curl_close($ch);

        if (intval($info['http_code']) == 200) {
            return json_decode($result, true);
        } else {
            error('curl请求失败(' . $errNo . ")");
        }
    }
}

if (!function_exists('takeRepeatVal')) {

    /**
     * 获取重复数据的数组
     * @param  array $arr 原数组
     * @return array 原数组中重复的值
     */
    function takeRepeatVal($arr)
    {
        // 获取去掉重复数据的数组
        $unique_arr = array_unique($arr);

        // 获取重复数据的数组
        $repeat_arr = array_diff_assoc($arr, $unique_arr);

        return $repeat_arr;
    }
}

if (!function_exists('L')) {
    /**
     * 文件日志调试方法
     *
     * 将数据保存至txt文件中并将文件输出至指定目录
     * 1、线上环境调试时
     * 2、无法打断点调试时
     *
     * @param mixed $datas [打印数据，如果是数组会自动转为json]
     * @param string $fileName [输出文件名]
     * @param boolean $appendFlag [是否文件末尾追加式写入数据，true追加，false覆盖]
     * @param string $filePath [输出文件目录，默认在根目录下]
     */
    function L($datas, $fileName = 'test', $appendFlag = true, $filePath = '.')
    {
        if (is_array($datas)) {
            $datas = json_encode($datas);
        }

        $now = date('Y-m-d H:i:s');

        $content = '------- ' . $now . ' -------' . "\n";
        $content .= $datas . "\n";
        $content .= '------- ' . $now . ' -------' . "\n";

        $path = $filePath . '/' . $fileName . '.txt';

        if ($appendFlag === true) {
            file_put_contents($path, $content, FILE_APPEND);
        } else {
            file_put_contents($path, $content);
        }
    }
}

if (!function_exists('D')) {
    /**
     * 设计一个服务类
     */
    function D()
    {
        return (new \App\Services\Lib\Service())->make(...func_get_args());
    }
}

if (!function_exists('source')) {
    /**
     * 判断访问来源
     */

    function source()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //判断各种来源
        $rs = '';
        if (strpos($agent, 'windows nt')) {
            $rs = 'windows-PC端';
        } else if (strpos($agent, 'mac os')) {
            $rs = 'mac-PC端';
        } else if (strpos($agent, 'iphone')) {
            $rs = 'iphone-移动端';
        } else if (strpos($agent, 'android')) {
            $rs = 'android-移动端';
        } else if (strpos($agent, 'ipad')) {
            $rs = 'ipad-移动端';
        }

        return $rs;
    }
}

if (!function_exists('imgUrl')) {
    function imgUrl($path = "")
    {
        return config('app.source_img_url') . $path;
    }
}

if (!function_exists('videoUrl')) {
    function videoUrl($path = "")
    {
        return config('app.source_video_url') . $path;
    }
}

if (!function_exists('sortArr')) {
    function sortArr($idArr, $sortArr, $sort = "id")
    {
        $return = [];
        foreach ($idArr as $key => $value) {
            if (!isset($sortArr[$value])) {
                continue;
            }
            $return[$key] = $sortArr[$value];
        }
        return $return;
    }
}


// 定义一个函数getIP()
if (!function_exists('getIP')) {
    function getIP()
    {
        global $ip;

        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknow";

        return $ip;
    }
}

if (!function_exists('array_remove_value')) {
    /**
     * 移除数组中某个值
     * @param $array array|mixed 待操作的原数组
     * @param $value mixed 要移除的元素
     * @return $array array|other 移除后的数组或者非数组原始数据
     * @author Herry
     * @since 2017/12/01
     */
    function array_remove_value($array, $value)
    {
        if (is_array($array)) {
            if (in_array($value, $array)) {
                foreach ($array as $k => $v) {
                    if ($v === $value) {
                        unset($array[$k]);
                    }
                }
            }
        }
        return $array;
    }

}


//新增   判断是手机访问还是电脑访问  fuguowei  20180102
if (!function_exists('is_mobile')) {
    function is_mobile()
    {
        //先對微信瀏覽器進行判斷，如果是直接返回true
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        if (strpos($user_agent, 'MicroMessenger') !== false) {
            return true;
        }

        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

        $regex_match .= ")/i";

        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));

    }
}

/**
 * 把远程图片下载到本地
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
if (!function_exists('http_get_imgData')) {
    function http_get_imgData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $return_content;
    }
}

/**
 * 获取段时间内每天的日期数组
 */
if (!function_exists('getDateFromRange')) {
    function getDateFromRange($startDate, $endDate)
    {
        // 计算日期段内有多少天
        $days = ($endDate - $startDate) / 86400 + 1;
        // 保存每天日期
        $date = array();
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $startDate + (86400 * $i));
        }
        return $date;
    }
}

/**
 * 根据起点坐标和终点坐标测距离
 * @param  [array]   $from  [起点坐标(经纬度),例如:array(118.012951,36.810024)]
 * @param  [array]   $to    [终点坐标(经纬度)]
 * @param  [bool]    $km        是否以公里为单位 false:米 true:公里(千米)
 * @param  [int]     $decimal   精度 保留小数位数
 * @return [string]  距离数值
 * @author wuxiaoping
 */
if (!function_exists('get_distance')) {
    function get_distance($from, $to, $km = true, $decimal = 2)
    {
        sort($from);
        sort($to);
        $EARTH_RADIUS = 6370.996; // 地球半径系数

        $distance = $EARTH_RADIUS * 2 * asin(sqrt(pow(sin(($from[0] * pi() / 180 - $to[0] * pi() / 180) / 2), 2) + cos($from[0] * pi() / 180) * cos($to[0] * pi() / 180) * pow(sin(($from[1] * pi() / 180 - $to[1] * pi() / 180) / 2), 2))) * 1000;

        if ($km) {
            $distance = $distance / 1000;
        }
        return round($distance, $decimal);
    }
}

/**
 * 商品详情数据错误临时补丁 目前只处理VOA真丝店铺
 */
if (!function_exists('dealWithProductContent')) {
    function dealWithProductContent($wid, $content_old)
    {
        if ($wid == 922) {
            $content_final = '';
            $content = json_decode($content_old, true);

            if (empty($content)) {
                return $content_old;
            }

            foreach ($content as $k => $v) {
                if ($v['type'] == 'store' && !empty($v['content'])) {
                    $content_final = $v['content'];
                    unset($content[$k]);
                }

                if ($content_final && $v['type'] == 'shop_detail') {
                    $content[$k]['content'] = $content_final;
                    break;
                }
            }

            $content = array_values($content);
            return json_encode($content);
        } else {
            return $content_old;
        }
    }
}

/**
 * 小程序商品详情数据错误临时补丁
 * 只返回商品详情 不返回其他组件
 * @param json $content_old 详情内容
 * @return json
 * @author 许立 2018年7月5日
 * @update 许立 2018年09月11日 返回视频和富文本组件
 */
if (!function_exists('dealWithXCXProductContent')) {
    function dealWithXCXProductContent($content_old)
    {
        $content_final = '';
        $content = json_decode($content_old, true);

        if (empty($content)) {
            return $content_old;
        }

        foreach ($content as $k => $v) {
            if ($v['type'] == 'shop_detail') {
                $content_final && $content[$k]['content'] = $content_final;
            } else {
                !empty($v['content']) && $content_final = $v['content'];
                // 图片广告不删除
                if ($v['type'] != 'image_ad' && $v['type'] != 'video' && $v['type'] != 'rich_text') {
                    unset($content[$k]);
                }
            }
        }

        $content = array_values($content);
        return json_encode($content);
    }
}

if (!function_exists('readFiles')) {
    function readFiles($dir)
    {
        if (!is_dir($dir)) return false;
        $handle = opendir($dir);
        $files = [];
        if ($handle) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
                if (is_dir($temp) && $fl != '.' && $fl != '..') {
                    read_all($temp);
                } else {
                    if ($fl != '.' && $fl != '..') {
                        $files[] = $temp;
                    }
                }
            }
        }
        return $files;
    }
}

/**
 * 过滤掉emoji表情
 * @author 何书哲 2018年12月26日
 * @update 许立 2019年02月14日 emoji表情用特殊字符串替换
 */
if (!function_exists('filterEmoji')) {
    function filterEmoji($str, $replace = '')
    {
        return preg_replace_callback('/./u',
            function (array $match) use ($replace) {
                return strlen($match[0]) >= 4 ? $replace : $match[0];
            },
            $str);
    }
}
    

