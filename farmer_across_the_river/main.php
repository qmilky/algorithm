<?php
require_once('Status.php');
require_once('Register.php');


$status = new Status(array(0, 0,  0, 0), array());

$paths = Register::registry('final_status');
//$obj = Status::$_children;
//print_r($obj);
if(!empty($paths) )
{
    if(is_array($paths))
    {
        foreach($paths as $k => $path)
        {
            if($k === 0)
            {
                $stepNum = count($path);
            }
            $_stepNum = count($path);
            //依次比较哪个步数最少并记录
            if($_stepNum < $stepNum)
            {
                $stepNum = $_stepNum;
                $_key = $k;
            }

        }
        printf("共找出 %d 种不同的方式", count($paths));
//        $stepNum = 0;   //记录步数最少的路径。
        if(!isset($_key)) $_key = 1;
        printf(" 其中，第 %d 种方式所用步数最少，为 %d 步", $_key, $stepNum-1);
        echo '<pre>';
        print_r($paths);
    }

}else{
    echo '非常抱歉，没有找到合适的方法！';
}

