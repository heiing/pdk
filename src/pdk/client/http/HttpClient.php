<?php

namespace heiing\pdk\client\http;

/**
 * Http Client
 * @author hzm
 */
class HttpClient {

    /**
     * Curl 实例
     */
    private $curl = null;

    /**
     * 请求选项
     */
    private $opts = [];
    private $default_opts = [
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => "HttpClient/1.0",
    ];

    /**
     * 请求头
     */
    private $request_headers = [];
    private $default_request_headers = [];

    private $response_headers = '';
    private $response_header = [];
    private $info = [];

    public function __construct() {
        $this->curl = curl_init();
        $this->opts = $this->default_opts;
        $this->request_headers = $this->default_request_headers;
    }

    public function __destruct() {
        curl_close($this->curl);
    }

    /**
     * 设置超时时间
     * @param int $seconds 超时时间，单位：秒
     */
    public function setTimeout($seconds) {
        $this->opts[CURLOPT_TIMEOUT] = $seconds;
        return $this;
    }

    /**
     * 设置连接超时时间
     * @param int $seconds 超时时间，单位：秒
     */
    public function setConnectTimeout($seconds) {
        $this->opts[CURLOPT_CONNECTTIMEOUT] = $seconds;
        return $this;
    }

    /**
     * 设置 UserAgent
     * @param string $agent 客户端代理名称
     */
    public function setUserAgent($agent) {
        $this->opts[CURLOPT_USERAGENT] = $agent;
        return $this;
    }

    /**
     * 设置认证的用户名与密码
     * @param string $username
     * @param string $password
     */
    public function setUser($username, $password = '') {
        $this->opts[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST | CURLAUTH_BASIC;
        $this->opts[CURLOPT_USERPWD] = "{$username}:{$password}";
    }

    /**
     * 设置头
     * @param int $name 名称
     * @param mixed $value 值
     * @param bool $replace 替换已存在的值
     */
    public function setHeader($name, $value, $replace = true) {
        if (true === $replace || !isset($this->request_headers[$name])) {
            $this->request_headers[$name] = [$value];
        } else if (!in_array($value, $this->request_headers[$name])) {
            array_push($this->request_headers[$name], $value);
        }
        return $this;
    }

    /**
     * 通过数组批量设置选项
     * @param array $options 键名为 CURLOPT_*常量，键值为对应的选项值
     */
    public function setOptions($options) {
        if (is_array($options) && !empty($options)) {
            $this->opts = array_merge($this->opts, $options);
        }
        return $this;
    }

    /**
     * 重置选项
     * @return \Http
     */
    public function resetOptions() {
        $this->opts = $this->default_opts;
        return $this;
    }

    /**
     * 重置头
     * @return \Http
     */
    public function resetHeaders() {
        $this->request_headers = $this->default_request_headers;
        return $this;
    }

    /**
     * 返回响应头
     * @return string 响应头（CrLf分隔的字符串）
     */
    public function getResponseHeaders() {
        return $this->response_headers;
    }

    /**
     * 获取响应头的值
     * @param string $name
     * @param bool   $return_all
     * @return string|array
     */
    public function getResponseHeader($name, $return_all = false) {
        if (empty($this->response_header) && !empty($this->response_headers)) {
            foreach (explode("\r\n", trim($this->response_headers)) as $line) {
                if (false === strpos($line, ':')) {
                    continue;
                }
                list($key, $value) = explode(':', $line, 2);
                if (empty($key)) {
                    continue;
                } else {
                    $key = trim($key);
                }
                if (isset($this->response_header[$key])) {
                    $this->response_header[$key][] = trim($value);
                } else {
                    $this->response_header[$key] = [trim($value)];
                }
            }
        }
        if (isset($this->response_header[$name])) {
            return $return_all ? $this->response_header[$name] : $this->response_header[$name][0];
        }
        return null;
    }

    /**
     * 返回响应状态码
     * @return int
     */
    public function getStatusCode() {
        return isset($this->info['http_code']) ? (int)$this->info['http_code'] : 0;
    }

    /**
     * 返回Http请求是否成功
     * @return boolean
     */
    public function isSuccess() {
        $code = $this->getStatusCode();
        return 200 <= $code  && $code <= 299;
    }

    /**
     * 获取响应信息
     * @param string $name
     * @return mixed
     */
    public function getResponseInfo($name = '') {
        if (empty($name)) {
            return $this->info;
        }
        return isset($this->info[$name]) ? $this->info[$name] : null;
    }

    /**
     * 获取 Curl Error
     * @return string
     */
    public function getError() {
        if (0 === curl_errno($this->curl)) {
            return "";
        }
        return curl_error($this->curl);
    }

    /**
     * 重置 Client
     * @return \HttpClient
     */
    public function reset() {
        $this->info = [];
        $this->response_headers = '';
        $this->response_header = [];
        if (is_resource($this->curl)) {
            curl_close($this->curl);
            $this->curl = curl_init();
        }
        $this->resetOptions();
        $this->resetHeaders();
        return $this;
    }

    /**
     * 发送HTTP请求，返回HTTP响应
     * @param string $method
     * @param string $url
     * @param string|array $body
     * @return string
     */
    public function request($method, $url, $body = '') {
        curl_setopt_array($this->curl, $this->opts);
        foreach ($this->request_headers as $name => $values) {
            foreach ($values as $value) {
                $headers[] = "{$name}: {$value}";
            }
        }
        if (version_compare(PHP_VERSION, "5.5.0") >= 0) {
            curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, false);
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        switch ($method) {
            case 'GET':
                curl_setopt($this->curl, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($this->curl, CURLOPT_POST, true);
            default:
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                break;
        }
        if (!empty($body)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        }
        $content = $this->getResponseTransfer();
        return $content;
    }

    /**
     * 以 HTTP GET 方法发送请求，返回响应内容
     * @param string $url 请求地址
     * @param array $params 附加参数，如QueryString
     * @return string 响应内容
     */
    public function doGet($url, $params = null) {
        return $this->request('GET', $this->urlQueryMerge($url, $params));
    }

    /**
     * 以 HTTP POST 方法发送请求，返回响应内容
     * @param string $url 请求地址
     * @param array $body Body数据
     * @param array $params URL参数
     * @return string 响应内容
     */
    public function doPost($url, $body = null, $params = null) {
        return $this->request('POST', $this->urlQueryMerge($url, $params), $body);
    }

    /**
     * 以 HTTP DELETE 方法发送请求，返回响应内容
     * @param string $url 请求地址
     * @param array $params 附加参数，如QueryString
     * @return string 响应内容
     */
    public function doDelete($url, $params = null) {
        return $this->request('DELETE', $this->urlQueryMerge($url, $params));
    }

    /**
     * 解析响应，返回响应的 body
     * @return string
     */
    private function getResponseTransfer() {
        $response = curl_exec($this->curl);
        $this->info = curl_getinfo($this->curl);
        $this->response_headers = substr($response, 0, $this->info['header_size']);
        if ($this->info['size_download'] > 0) {
            $content = substr($response, -1 * $this->info['size_download']);
        } else {
            $content = '';
        }
        return $content;
    }

    /**
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    private function urlQueryMerge($url, $params) {
        if (!empty($params) && is_array($params)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }
        return $url;
    }

}
