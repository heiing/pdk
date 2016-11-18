<?php
namespace heiing\pdk\logs\writer;

/**
 * Writer: 写日志的接口
 *
 * @author hzm
 */
interface Writer {
    
    /**
     * 写入日志内容
     * @param string $buffer
     * @return int
     */
    public function write($buffer);
    
}
