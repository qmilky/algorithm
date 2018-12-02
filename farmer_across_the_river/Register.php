<?php

class Register
{
    static protected $_data = array();
    public function __construct()
    {

    }

    static public function register($key, $value)
    {
        self::$_data[$key] = $value;  //一次性添加，会覆盖原有的值
    }

    static public function append($key, $value)
    {
        self::$_data[$key][] = $value;   //将符合条件的某一种路径结果存入属性 $_data 中，不会覆盖原有值，直接在尾部继续添加。
    }
    //取出所有符合条件的结果集
    static public function registry($key)
    {
        if(isset(self::$_data[$key]))
        {
            return self::$_data[$key];
        }
        return null;
    }
}