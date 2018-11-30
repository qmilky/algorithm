<?php
class Status
{

    protected $_values;
    protected $_limit;
    protected $_ancestors;   //重要
    protected $_children;
    public  function  __construct(array $values, array $limit, array $ancestors)
    {

        $this->_values = $values; //[8, 0, 0],[5, 3, 0]
        $this->_limit = $limit;
//        $ancestors[] = $ancestors;//会导致死循环
        $ancestors[] = $this->_values; // 记录下父级状态,如果没有此步直接$this->_ancestors[] = $this->_values;报错Allowed memory size of 268435456 bytes exhausted (tried to allocate 20480 bytes) in /Users/qiyamin/htdocs/average_water/Status.php on line 16
        $this->_ancestors = $ancestors;   //每次都会覆盖。

        $this->generateChildren();  //主逻辑
    }

    public  function  generateChildren()
    {
        $children = array();
        $n = count($this->_values);  // 3
        //双层循环的意义，因为每次操作都是从某一只水桶中将水倒入另一只水桶，所以只需要2层循环，$i、$j即可
        //哪里体现了二叉树深度优先遍历， 查看结果集，找到第一条路径后，类似于递归从分叉处进行下一次遍历
        for($i=0; $i<$n; $i++)
        {
            for($j=$i+1; $j<$n; $j++)
            {
                $child = $this->turnStatus($i, $j); //倒水时改变状态，但状态记录到$this->_ancestors中是通过 new Status（）实现的。
                if($child === array(4, 4, 0))
                {
                    $this->showResult($child);
                }
                if($child != false && !$this->isAncestor($child) && $child !==array(4, 4, 0))
                {
                    $children[] = new Status($child, $this->_limit, $this->_ancestors) ;
                }
                //注意turnStatus参数的位置。如果 $this->_values[$j]为0，则交换倒水和接水的两只桶的次序重新进行倒水的动作
                $child = $this->turnStatus($j, $i);  //返回的是此次倒水完成后三只水桶中各自的水量。
                if($child === array(4, 4, 0))
                {
                    $this->showResult($child);
                }
                if($child != false && !$this->isAncestor($child) && $child !== array(4, 4, 0))
                {
                    //类的实例，作用？？？,记录不同倒水动作后，该类各个属性的状态值。
                    //先循环，循环中符合条件进入自调用，自调用无法继续时，继续上一次循环，循环完成之后继续当前循环的自调用。
                    //自调用会重复之前的状态 $this->_ancestors 时才进行下一次循环继续两两倒水，循环完毕之后继续自调用。
                    //每 new 一次都会实例化一个不同都对象，对象中的属性不一样，基本上每一个节点上就是一个对象
                    $children[] = new Status($child, $this->_limit, $this->_ancestors) ;

                }
            }
        }
        $this->_children = $children;
//        echo '<pre>';
//        var_dump($this->_children);
    }


    /*
     * 改变状态，模拟倒水的动作
     * 将$value[$j]中都水倒入$value[$i]中
     * */
    protected  function turnStatus($i, $j)
    {
        $value = $this->_values;  //第一次调用为8， 0， 0
        //如果要倒水的桶是空的直接返回
        if($value[$j] == 0)
        {
            return false;
        }
        //如果接收水的水桶是满的直接返回
        if($value[$i] === $this->_limit[$i])
        {
            return false;
        }
        // 如果接收水的水桶中剩余可接水的量小于或者等于倒水的桶中的水量，那么此次到水量为$this->_limit[$i] - $value[$i]
        if( $this->_limit[$i] - $value[$i] <= $value[$j])
        {
            $m = $this->_limit[$i] - $value[$i];
        }
        else{
            //如果接收水的水桶中剩余可接水的量大于倒水的桶中的水量，那么此次倒水的量就为要倒水的桶中的水量
            $m = $value[$j];
        }

        $value[$j] -= $m;  //$m为此次倒水的水量，倒水的水桶减去相应倒水的水量
        $value[$i] += $m;  //接收水的水桶加上此次接水的水量 $m
        return $value;   //将此次操作的两只水桶在操作完后各自的水量返回,第三只水桶的水量保持不变一并返回。

    }

    protected function showResult(array $child)
    {
        $path = $this->_ancestors;  //此处记录各个水桶在达到[4， 4， 0]状态之前的各种状态。
        $path[] = $child;   //$child = array(4, 4, 0)，将最后一个状态[4, 4, 0]也加入到路径中。
        Register::append('final_status', $path);  ////将符合条件的某一种路径结果存入类 Register 的属性 $_data 中。
    }

    /*
     * 判断的作用，避免陷入死循环
     * */
    protected function  isAncestor($value)
    {
        //判断当前三只水桶的水量状态（如：3， 5， 0）在之前有没有出现过，只要出现过一次就返回true，避免陷入死循环
        foreach($this->_ancestors as $ancestor)
        {
            if($value === $ancestor)
            {
                return true;
            }
        }
        return false;
    }



}