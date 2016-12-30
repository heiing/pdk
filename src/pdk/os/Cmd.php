<?php

namespace heiing\pdk\os;

/**
 * Cmd 执行命令
 *
 * @author hzm
 */
class Cmd {
    
    private $stdout = null;
    private $stderr = null;
    private $code = -1;
    private $pipe = [];
    
    private $pid = 0;
    private $statusExitCode = -1;
    
    private $wd   = null;
    private $env  = null;
    private $proc = null;
    private $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    
    /**
     * 返回进程的 PID，如果进程未启动则返回 0
     * @return int 成功则返回进程 ID，进程未启动则返回 0
     */
    public function getPid() {
        return $this->pid;
    }
    
    /**
     * 获取命令退出码
     * @return int 返回命令退出码
     */
    public function getExitCode() {
        if ((-1 === $this->code) && (-1 !== $this->statusExitCode)) {
            return $this->statusExitCode;
        }
        return $this->code;
    }
    
    /**
     * 获取命令的联合输出：标准输出 + (CR or LF or CRLF) + 标准错误
     * @return string
     */
    public function getCombinedOutput() {
        return $this->getStdout() . PHP_EOL . $this->getStderr();
    }
    
    /**
     * 获取命令的标准输出，如果进程未启动或未结束，则返回 null
     * @return string
     */
    public function getStdout() {
        return $this->stdout;
    }
    
    /**
     * 设置标准输出
     * @param string $filepath 标准输出的文件路径，如果为 null，则使用系统标准输出
     */
    public function setStdout($filepath = null) {
        if (null === $filepath) {
            $this->desc[1] = ['pipe', 'w'];
        } else {
            $this->desc[1] = ['file', $filepath, 'w'];
        }
    }
    
    /**
     * 获取命令的错误输出，如果进程未启动或未结束，则返回 null
     * @return string
     */
    public function getStderr() {
        return $this->stderr;
    }
    
    /**
     * 设置标准错误
     * @param string $filepath 标准错误的文件路径，如果为 null，则使用系统标准错误
     */
    public function setStderr($filepath = null) {
        if (null === $filepath) {
            $this->desc[2] = ['pipe', 'w'];
        } else {
            $this->desc[2] = ['file', $filepath, 'w'];
        }
    }
    
    /**
     * 设置命令的工作目录，必须是绝对路径
     * @param string $pwd
     */
    public function setWorkingDirectory($pwd) {
        $this->wd = $pwd;
    }
    
    /**
     * 获取当前的工作目录
     * @return string
     */
    public function getWorkingDirectory() {
        return $this->wd;
    }
    
    /**
     * 批量设置环境变量
     * @param array|null $environments 如果为 null，则使用与 PHP 进程相同的环境变量
     */
    public function setEnvironmentVariables($environments = null) {
        $this->env = $environments;
    }
    
    /**
     * 设置环境变量
     * @param string $name  变量名
     * @param string $value 变量值
     */
    public function setEnvironmentVariable($name, $value) {
        if (!is_array($this->env)) {
            $this->env = [];
        }
        $this->env[$name] = $value;
    }
    
    /**
     * 删除环境变量
     * @param string $name
     */
    public function deleteEnvironmentVariable($name) {
        if ((null !== $this->env) && isset($this->env[$name])) {
            unset($this->env[$name]);
        }
    }
    
    /**
     * 将 $bytes 写入到命令的标准输入。必须在 start 之后，wait 之前调用。
     * @param string $bytes
     * @return int 返回已写入的字节数
     */
    public function write($bytes) {
        if (!isset($this->pipe[0]) || !is_resource($this->pipe[0])) {
            return 0;
        }
        return fwrite($this->pipe[0], $bytes);
    }
    
    /**
     * 启动 $command 命令，并等待命令结束
     * @param string $command
     * @return boolean 成功返回 true，失败返回 false
     */
    public function run($command) {
        if (true === $this->start($command)) {
            return $this->wait();
        }
        return false;
    }
    
    /**
     * 启动进程，执行 $command 命令。如果上一个命令未退出，则不会启动并且返回 false
     * @param string $command
     * @return boolean 成功返回 true，失败返回 false
     */
    public function start($command) {
        if (is_resource($this->proc)) {
            return false;
        }
        
        $this->reset();
        
        $this->proc = proc_open($command, $this->desc, $this->pipe, $this->wd, $this->env);
        if (false === $this->proc) {
            return false;
        }
        
        if (false === $this->status()) {
            return false;
        }
        
        return is_resource($this->proc);
    }
    
    /**
     * 等待 command 进程执行完毕。
     * @return boolean 成功返回 true，失败返回 false
     */
    public function wait() {
        if (!is_resource($this->proc)) {
            return false;
        }
        
        if (isset($this->pipe[1]) && is_resource($this->pipe[1])) {
            $this->stdout = stream_get_contents($this->pipe[1]);
        }
        
        if (isset($this->pipe[2]) && is_resource($this->pipe[2])) {
            $this->stderr = stream_get_contents($this->pipe[2]);
        }
        
        for ($i = 0; $i < count($this->pipe); $i++) {
            if (isset($this->pipe[$i]) && is_resource($this->pipe[$i])) {
                fclose($this->pipe[$i]);
            }
        }
        
        $this->code = (int)proc_close($this->proc);
        $this->proc = null;
        
        return 0 === $this->code;
    }
    
    /**
     * 向进程发送信号
     * @param ing $signal 信号，默认为 TERM(15)
     * @return boolean 发送信号成功即返回 true，失败 返回 false
     */
    public function kill($signal = 15) {
        if (!is_resource($this->proc)) {
            return true;
        }
        return proc_terminate($this->proc, $signal);
    }
    
    /**
     * 向进程发送信号，一直等待进程成功退出为止
     * @param ing $signal 信号，默认为 TERM(15)
     * @param int $timeoutSeconds 超时秒数
     * @return boolean 成功返回 true, 失败或超时返回 false
     */
    public function killWait($signal = 15, $timeoutSeconds = 10) {
        if (false === $this->kill($signal)) {
            return false;
        }
        $usleep = 100000;
        $timeout = $timeoutSeconds * 1000000;
        while ($usleep <= $timeout) {
            usleep($usleep);
            $usleep *= 2;
            $status = $this->status();
            if (false === $status) {
                continue;
            }
            if (false === $status['running']) {
                $this->wait();
                return true;
            }
        }
        return false;
    }
    
    private function reset() {
        $this->pid = 0;
        $this->pipe = [];
        $this->code = -1;
        $this->stdout = null;
        $this->stderr = null;
        $this->statusExitCode = -1;
    }
    
    private function status() {
        if (!is_resource($this->proc)) {
            return [
                'command' => null,
                'pid' => 0,
                'running' => false,
                'signaled' => false,
                'stopped' => false,
                'exitcode' => -1,
                'termsig' => 0,
                'stopsig' => 0,
            ];
        }
        $status = proc_get_status($this->proc);
        if (false === $status) {
            return false;
        }
        $code = (int)$status['exitcode'];
        if ($code !== -1) {
            $this->statusExitCode = $code;
        }
        if ($this->pid === 0) {
            $this->pid = (int)$status['pid'];
        }
        return $status;
    }
    
}
