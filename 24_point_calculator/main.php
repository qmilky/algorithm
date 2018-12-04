<?php
//24 点计算器
//设置脚本最大执行时间,set_time_limit,如果设置为0（零），没有时间方面的限制。
//设置允许脚本运行的时间，单位为秒。如果超过了此设置，脚本返回一个致命的错误。默认值为30秒，或者是在php.ini的max_execution_time被定义的值，如果此值存
set_time_limit(0);
$values = array(5, 5, 5, 1);
$result = 24;

$list = array();
$all = array();
$res = makeValue($values);
//var_dump($res); NULL
echo '<pre>';
print_r($list);
//print_r(array_unique($all));
/*
 * 函数makeValue() 得到 256 种表达式，也是解空间。深度优先遍历得出。
 * */
function makeValue($values, $set = array())
{
    $words = array('+', '-', '*', '/');
    //判断给定数组中只有一个值时：（关键）
    if(sizeof($values) == 1)
    {

        //从数组头部取出一个值,返回取出的那个值

        $set[] = array_shift($values);
//        echo '<pre>';
//        var_dump($set);die;
        return makeSpecial($set);
    }
    foreach($values as $key => $value)
    {
        $tmpValues = $values;
        unset($tmpValues[$key]);  //去除本次取出的那个key对应的值，剩余值作为下次递归调用的$values参数。
        foreach($words as $word)
        {
//            $a = array_merge($set, array($value, $word));
//            echo '<pre>';
//            var_dump($a);
            //$tmpValues 为去除 $value 后剩余的值，每次取出$values 中的一个值和运算符中的一个放入$set中
            //该步首先保证运算符不变数值遍历进行递归计算，当$set中记录好一个完整的表达式之后调用makeSpecial（）进行计算看看结果是不是等于24，等于24 则记录在全局变量$list中，不等就废弃该表达式。
            //然后，遍历运算符再次计算，从最后一个1开始改变它前面的运算符计算。
            makeValue($tmpValues, array_merge($set, array($value, $word)));
        }
    }

}


/*
 * 该函数判断是否需要加括号，每次输出一个完整的表达式的时候就会调用此函数。
 *
 * */
function  makeSpecial($set)
{
    $size = sizeof($set);
    //若 $size <= 3 或者运算符中没有乘法或者除法运算，说明最多就是两个数之间的运算或者只有加减运算，无须加括号
    if($size <= 3 || !in_array('/', $set) && !in_array('*', $set) )
    {
        return makeResult($set);
    }
    // 判断是否需要加括号，$len = 3（数值位置），$len += 2（定位到数值的位置，中间间隔运算符，所以要加2）。
    //两层循环遍历的意思的逻辑？？？该遍历无法产生 (5+5)*5+1这样的表达式,会有（5+5）*(5+1)这样的表达式。
    for($len = 3; $len < $size - 1; $len += 2 )   //$size最大为7，可能取值为5，7，$len取值只能为3，5($len取值为5的情况 ：（5+5+5）*1)
    {
        for($start = 0; $start < $size - 1; $start += 2 )//$start 取值为0，2，4，6，递归调用时$size会变化
        {
            if(isset($set[$start - 1]) && isset($set[$start + $len]))
            {
//                $start = 2时才会第一次进入此处，$set 中存储的是：值、符号、值、符号的形式，$start - 1以及$start + 3刚好是符号位；下面判断的是该值的前后都没有 '*'，'/' 运算
                if(!($set[$start - 1] == '*' || $set[$start - 1] == '/' || $set[$start + $len] == '*' || $set[$start + $len] == '/'))
                {
                    continue;//跳出本次循环，内循环
                }
            }
            //下面的也不影响结果
//            if(isset($set[$start - 1]))
//            {
//                //判断前一位是否是'*'，'/' 运算符，不是则判断后三位，例如5*（5+5-1）
//                if(!($set[$start - 1] == '*' || $set[$start - 1] == '/' ))
//                {
//                    if(isset($set[$start + $len]))
//                    {
//                        //判断往后推3位是否是'*'，'/'运算符，不是，则无须加括号，例如（5+5）*5+1
//                        if( !($set[$start + $len] == '*' || $set[$start + $len] == '/'))
//                        {
//                            continue;
//                        }
//                    }
//                }
//            }

            //array_slice 在数组中根据条件取出一段值，3或者5个数值，并返回，返回值为数组，不影响$set；
            $subSet = array_slice($set, $start, $len);

            if(!(in_array('+', $subSet)) && !(in_array('-', $subSet)))
            {
                //重要，理解！！！
                //截取的3位中不包含加也不包含减也无须添加括号，只能是'*'和'/'，优先运算，无须添加括号。
                continue;
            }
            //若为混合运算，执行如下：
            $tmpSet = $set;
//            echo '<pre>';
//            echo '111111<br>';
//            var_dump($tmpSet);
            //array_splice（移除之后剩余都键值索引也会更新）从数组中移除 $len - 1 （因为下面赋值时会覆盖留下来都那个没有用都数值）位元素, $len 从3开始，每次递增 2
            array_splice($tmpSet, $start,$len - 1);  //影响 $tmpSet值。
//            var_dump($tmpSet);  //测试，主要关注索引
            //此处会覆盖不用的那个数值。
            $tmpSet[$start] = '('.implode('', $subSet).')';
//            var_dump($tmpSet);
            makeSpecial($tmpSet);//前面都数值两两运算后的表达式（字符串）作为一个述职与后面剩余的数继续进行递归运算。
        }
    }
}


/*
 * 计算表达式结果是否为24，是的话记录下来
 *
 * */

function   makeResult($set)
{
//    Global定义的是一个全局变量，不同于static和普通变量。他们3个(static, const和global)之间的区别是：普通变量只在自己的小范围内使用，static的变量作用域包含整个函数范围，global的作用域是包含整个程序运行的范围。
//    static关键字在类中描述一个成员是静态的，static能够限制外部的访问，因为static后的成员是属于类的，是不属于任何对象实例，其他类是无法访问的，只对类的实例共享，能一定程序对该成员尽心保护。其实这是类里面的一个概念，当然单独拿到方法中使用也是可以的。
    global $result, $list, $all;

    $str = implode('', $set);
    //eval是把字符串当作代码执行,单引号内的变量不会被执行，双引号内的变量是会被执行的，而如果双引号内变量加上反斜杠就可以让这个变量不被执行，成为特例
    @eval("\$num=$str;");  //就是给 $num 这个变量赋值,主要作用是想让$str表达式进行四则运算得出结果；
    if($num == $result && !in_array($str, $list))
    {
        $list[] = $str; //记录所有的表达式，字符串形式。
    }

    $all[] = $str;
}
