<?php
class Utility {
    const POST_TIMEOUT   = 5;
    const GET_TIMEOUT    = 300;

    /**
     * parseWhere 
     * 解析where 为 yii update where
     * @param Array $where 
     * @param string $operator 
     * @return void
     */
    public static function parseWhere(Array $where, $operator = 'and') {
        $condition = array();
        foreach ($where as $key => $val) {
            if (strpos($key, '%') !== FALSE) {
                $key = substr($key, 0, -1);
                $condition[] = array('like', $key . ' = ' . $val);
                continue;
            }
            $condition[] = $key . ' = ' . $val;
        }
        array_unshift($condition, 'and');
        return $condition;
    }
    /**
     * quote 
     * 转移字符串，用于sql查询
     * @param mixed $str 
     * @return void
     */
    public static function quote($str) {
        return "'" . addcslashes($str, "\000\n\r\\'\"\032") . "'";
    }
    /**
     * 获取字节长度
     * @param string $str
     * @param string $encode
     */
    public static function byteLen($str, $encode="UTF-8") {
        $str = empty($str) ? '' : strval($str);
        return mb_strlen($str, $encode);
    }
    /**
     * convert 
     * 转换字符集
     * @param mixed $string 
     * @param mixed $toEncoding 
     * @param mixed $fromEncoding 
     * @return void
     */
    public static function convert($string, $toEncoding, $fromEncoding) {
        return mb_convert_encoding($string, $toEncoding, $fromEncoding);
    }
    /**
     * 加密算法
     */
    public static function hash($pw) {
        return md5($pw);
    }
    /**
     * base_decode 
     * base64解码函数
     * @param $str 
     * @return $string
     */
    public static function base_decode($str) {
        return base64_decode($str);
    }
    /**
     * base_encode 
     * base64编码函数
     * @param array $str
     * @return string
     */
    public static function base_encode($str) {
        return base64_encode($str);
    }
    /**
     * httpPost 
     * http post的实现
     * @return void
     */
    public static function httpPost($url, $post) {
		if(is_array($post)) {
			$post = http_build_query($post);
		}
		$ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::POST_TIMEOUT);
        $response = self::_getCurlResponse($url, $ch);
        curl_close($ch);
        return $response;
    }
    /**
     * httpGetResponse
     * http get请求
     * @param mixed $url 
     * @param array $get 
     * @return void
     */
    public static function httpGetResponse($url, $get = array()) {
        $ch = curl_init();
        if (! empty($get)) {
            $getString = http_build_query($get);
            $url = $url . '?' . $getString;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::GET_TIMEOUT);
        $response = self::_getCurlResponse($url, $ch);
        return $response;
    }
    /**
     * httpGet 
     * http get请求
     * @param mixed $url 
     * @param array $get 
     * @return void
     */
    public static function httpGet($url, $get = array()) {
        $ch = curl_init();
        if (! empty($get)) {
            $getString = '';
            $getString = http_build_query($get);
            $url = $url . '?' . $getString;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::GET_TIMEOUT);
        $response = self::_getCurlResponse($url, $ch);
        if ($response['status']) {
            $result = $response['message'];
        } else {
            $result = false;
        }
        curl_close($ch);
        return $result;
    }
    /**
     * 获取response响应信息
     * @param string $url
     * @param resource $ch
     */
    private function _getCurlResponse($url, $ch) {
        $response['status']  = false;
        $response['message'] = '';
        if (is_resource($ch === false)) {
            return $response;
        }
        $output  = curl_exec($ch);
        $info    = curl_getinfo($ch);
        if ($output === false || $info['http_code'] != 200) {
            if (curl_error($ch)) {
                $response['message'] = curl_error($ch);
            } else {
                $response['message'] = "No cURL data returned for $url [". $info['http_code']. "]";
            }
        } else {
            $response['status']  = true;
            $response['message'] = $output;
        }
        return $response;
    }

    /** 
     * outputResult-
     * 接口统一输出方法
     * @param array $ret-
     * @param string $callback-
     * @return void
     */
    public static function outputResult($ret, $callback='') {/*{{{*/
        $output = json_encode($ret);
        header("Content-Type: application/json;charset=UTF-8");
        if($callback) {
            $callback = htmlspecialchars($callback);
            echo 'try{'.$callback.'('.$output.');}catch(e){}';
        } else {
            echo $output;
        }   
        exit;
    }/*}}}*/
    public static function fetchUrl($url, $timeout=1) {/*{{{*/
        $info = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("DNT: 1"));
        curl_exec($ch);
        if(!curl_errno($ch)) {
            $info = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $info;
    }/*}}}*/
}
