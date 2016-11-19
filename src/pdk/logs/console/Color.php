<?php

namespace heiing\pdk\logs\console;

/**
 * Color
 *
 * @author hzm
 */
class Color {
    
    // models
    const STANDARD  = 1;
    const INTENSITY = 2;
    const COLOR_256 = 3;
    
    // types
    const BACKGROUND = 1;
    const FOREGROUND = 2;
    
    const BLACK   = 0;
    const RED     = 1;
    const GREEN   = 2;
    const YELLOW  = 3;
    const BLUE    = 4;
    const MAGENTA = 5;
    const CYAN    = 6;
    const WHITE   = 7;
    
    private $color;
    private $type;
    private $model;
    
    private $prefix = '';
    
    public function __construct($color, $type, $model = self::STANDARD) {
        $this->type  = $type;
        $this->model = $model;
        if ($color >= 10) {
            $this->color = $color;
        } else {
            if (self::BACKGROUND === $type) {
                $this->color = (self::INTENSITY === $this->model ? 100 : 40) + $color;
            }
            $this->color = (self::INTENSITY === $this->model ? 90 : 30) + $color;
        }
        if (self::COLOR_256 === $model) {
            $this->prefix = (self::BACKGROUND === $type ? '48;5;' : '38;5;');
        }
    }
    
    public function getColor() {
        return $this->color;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getModel() {
        return $this->model;
    }

    public function toString() {
        return "{$this->prefix}{$this->getColor()}";
    }
    
    public function __toString() {
        return $this->toString();
    }

    public static function getBackground($color) {
        return self::getStaticColorString($color, self::BACKGROUND, self::STANDARD);
    }
    
    public static function getForeground($color) {
        return self::getStaticColorString($color, self::FOREGROUND, self::STANDARD);
    }
    
    public static function getIntensityBackground($color) {
        return self::getStaticColorString($color, self::BACKGROUND, self::INTENSITY);
    }
    
    public static function getIntensityForeground($color) {
        return self::getStaticColorString($color, self::FOREGROUND, self::INTENSITY);
    }
    
    public static function get256Background($color) {
        return self::getStaticColorString($color, self::BACKGROUND, self::COLOR_256);
    }
    
    public static function get256Foreground($color) {
        return self::getStaticColorString($color, self::FOREGROUND, self::COLOR_256);
    }
    
    private static function getStaticColorString($color, $type, $model) {
        $c = new Color($color, $type, $model);
        return $c->toString();
    }
    
}
