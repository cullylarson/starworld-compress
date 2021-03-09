<?php

namespace Swc\Tools;

class Rando {
    public static function str($length=20) {
        $characters = "abcdefghijklmonpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $str = "";
        for($i=0; $i < $length; $i++) $str .= substr($characters, rand(0, strlen($characters) - 1), 1);
        return $str;
    }
}
