<?php

namespace Swc\Tools;

class Arr {
    public static function get(array $x, $name, $default=null) { return empty($x[$name]) ? $default : $x[$name]; }
    public static function sget(array $x, $name, $default=null) { return !isset($x[$name]) ? $default : $x[$name]; }
}
