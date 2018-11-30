<?php
require_once('Status.php');
require_once('Register.php');


$status = new Status(array(8, 0, 0), array(8, 5, 3), array());

$paths = Register::registry('final_status');
printf("共找出 %d 种不同的方式", count($paths));
$stepNum = 0;   //记录步数最少的路径。
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
printf(" 其中，第 %d 种方式所用步数最少，为 %d 步", $_key, $stepNum-1);
echo '<pre>';
print_r($paths);