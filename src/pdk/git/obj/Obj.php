<?php

namespace heiing\pdk\git\obj;

/**
 * Obj
 *
 * @author hzm
 */
abstract class Obj {
    
    private $id;
    
    protected function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     * 获取对象的 sha-1 值
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 获取对象的类型
     * @return string 对象类型
     */
    abstract public function getType();
    
    /**
     * 从输入的行解析对象
     * @param array $lines 输入的行
     */
    abstract public function parseFromLines($lines);
    
}
