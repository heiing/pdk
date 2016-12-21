<?php

namespace heiing\pdk\errors;

/**
 * BasicExceptionPrinter
 *
 * @author hzm
 */
class BasicExceptionFormatter {
    
    /**
     *
     * @var \Exception
     */
    private $e = null;
    private $maxTraces = 20;
    
    private $isError = false;
    
    private $ln = PHP_EOL;
    private $pn = "    ";
    
    public function __construct(\Exception $e, $maxTraces = 20) {
        $this->e = $e;
        $this->maxTraces = $maxTraces;
        $this->isError = ($e instanceof \ErrorException);
    }
    
    public function isError() {
        return $this->isError;
    }
    
    public function toString() {
        return $this->getMessageLine() . $this->getTraceLines() . $this->getPreviousLines();
    }
    
    public function __toString() {
        return $this->toString();
    }
    
    public function getName() {
        if ($this->isError) {
            return Errors::getName($this->e->getSeverity());
        }
        return get_class($this->e);
    }
    
    public function getCode() {
        if ($this->isError) {
            return $this->e->getSeverity();
        }
        return $this->e->getCode();
    }
    
    public function getTraceFunctionName($t) {
        $cls = '';
        if (!empty($t['class'])) {
            $cls .= $t['class'];
        }
        if (!empty($t['type'])) {
            $cls .= $t['type'];
        }
        $args = count($t['args']);
        $s = $args > 1 ? 's' : '';
        return "{$cls}{$t['function']}({$args} parameter{$s})";
    }
    
    public function getMessageLine() {
        return "[{$this->getName()}]:[{$this->getCode()}] {$this->e->getMessage()} @[{$this->e->getFile()}]:[{$this->e->getLine()}]{$this->ln}";
    }
    
    public function getTraceLine($t) {
        if (isset($t['file'])) {
            $fl = " @[{$t['file']}]:[{$t['line']}]";
        } else {
            $fl = '';
        }
        return "{$this->pn}{$this->getTraceFunctionName($t)}{$fl}{$this->ln}";
    }
    
    public function getTraceLines() {
        $traces = $this->e->getTrace();
        $lens = count($traces);
        $max = $lens > $this->maxTraces ? $this->maxTraces : $lens;
        $ret = '';
        for ($i = 0; $i < $max; $i++) {
            $ret .= $this->getTraceLine($traces[$i]);
        }
        if ($lens > $this->maxTraces) {
            $more = $lens - $this->maxTraces;
            $ret .= "{$this->pn}{$more} traces more...{$this->ln}";
        }
        return $ret;
    }
    
    public function getPreviousLines() {
        $pre = $this->e->getPrevious();
        if (null === $pre) {
            return '';
        }
        $fmt = new BasicExceptionFormatter($pre, $this->maxTraces);
        return "  Caused by " . $fmt->toString();
    }
    
}
