<?php
class Status
{
    protected $_values;  //存储值依次代表"农夫，狼，羊，菜"，0为在左岸或者从右岸带回来，1为在右岸或者从左岸运送到右岸到过程。
    protected $_ancestors;
    static public $_children;


    public  function __construct(array $values, array $ancestors)
    {
        $this->_values = $values;
        $ancestors[] = $this->_values;
        $this->_ancestors = $ancestors;
        $this->generateChildren();
    }

    protected function generateChildren()
    {

//        for($i = 0; $i <= 1; $i++)
//        {
//            for($j = 0; $j <= 1; $j++)
//            {
//                for($m = 0; $m <= 1; $m++)
//                {
//                    for($n = 0; $n <= 1; $n++)
//                    {
//                        $state = array($i, $j, $m, $n);
//                        //判断是否是结果
//                        if($state === [1,1,1,1])
//                        {
//                            $this->showResult($state);
//                        }
//                        //判断状态是否允许
//                        $not_safe = $this->notAllow($state);
//                        if(!$not_safe && $state != [1,1,1,1])
//                        {
//                            new Status($state, $this->_ancestors);
//                        }
//                    }
//                }
//            }
//        }

        for($i = 0; $i <= 3; $i++)
        {
            $values = $this->_values;
            switch($i)
            {
                case 0:
                    if($values[0] === 0 && $values[2] === 0)
                    {
                        echo 'one<br>';
                        $values[0] = 1;  //农夫带羊过河；
                        $values[2] = 1;
                    }else
                    {
                        echo 'one break<br>';
                        break;
                    }

                    $this->continueInstantiation($values);
                    break;
                case 1:
                    if($values[0] === 0 && $values[1] === 0)
                    {
                        echo 'two<br>';
                        $values[0] = 1;  //农夫带狼过河；
                        $values[1] = 1;
                    }else
                    {
                        echo 'two break<br>';
                        break;
                    }

                    $this->continueInstantiation($values);
                    break;
                case 2:
                    if($values[0] === 0 && $values[3] === 0)
                    {
                        echo 'three<br>';
                        $values[0] = 1;  //农夫带菜过河
                        $values[3] = 1;
                    }else
                    {
                        echo 'three break<br>';
                        break;
                    }


                    $this->continueInstantiation($values);
                    break;
                case 3:
                    if($values[0] === 0 )
                    {
                        echo 'four<br>';
                        $values[0] = 1;  //农夫自己过河；
                    }else
                    {
                        echo 'four break<br>';
                        break;
                    }

                    $this->continueInstantiation($values);
                    break;
            }

        };




    }

    protected function continueInstantiation($state)
    {
        if($state === [1,1,1,1])
        {
            $this->showResult($state);
        }
        //判断状态是否允许
        $not_safe = $this->notAllow($state);

        if(!$not_safe && $state != [1,1,1,1])
        {

            //过河后返回
            for($j = 0; $j <= 3; $j++)
            {
                $values = $state;
                switch($j)
                {
                    case 1:
                        echo '1' . '<br>';
                        if($values[0] === 1 && $values[1] === 1)
                        {
                            $values[0] = 0;  //农夫带狼返回；
                            $values[1] = 0;
                        }else
                        {
                            break;
                        }
                        $this->back_safe($values, $state);
                        break;

                    case 2:
                        echo '2' . '<br>';
                        if($values[0] === 1 && $values[2] === 1)
                        {
                            $values[0] = 0; //农夫带羊返回；
                            $values[2] = 0;
                        }else
                        {
                            break;
                        }
                        $this->back_safe($values, $state);

                        break;
                    case 3:
                        echo '3' . '<br>';
                        if($values[0] === 1 && $values[3] === 1)
                        {
                            $values[0] = 0;  //农夫带菜返回；
                            $values[3] = 0;
                        }else
                        {
                            break;
                        }

                        $this->back_safe($values, $state);
                        break;
                    case 0:
                        echo '0' . '<br>';
                        if($values[0] === 1)
                        {
                            $values[0] = 0;  //农夫自己返回；
                        }else
                        {
                            break;
                        }

                        $this->back_safe($values, $state);
                        break;

                }

            }


        }
    }
    protected function back_safe($values, $state)
    {
        if($values === [1,1,1,1])
        {
            $this->showResult($values);
        }
        $not_safe_back = $this->notAllow($values);
        if(!$not_safe_back)
        {
            //用如下方法在返回失败时还会记录下过河失败的路径
//            $ancestors = $this->_ancestors;
//            $ancestors[] = $state;
//            $this->_ancestors = $ancestors;   //关键，记录过河的路径，new该类时在构造方法中记录的是返回的路径。
//            $value = array_merge($this->_ancestors, $state);  //此合并结构与所需要的不一致
            //用此方法在返回失败时不会记录过河失败的路径，重要！！！
            $ancestor = $this->_ancestors;
            $ancestor[] = $state;
            $children[] = new Status($values, $ancestor); //$ancestor 中包含过河路径和返回路径，成功后一起添加，不成功都不添加。
            self::$_children = $children;
        }

    }
    protected function notAllow($values)
    {
        //判断，若狼和羊在一起农夫不在，或者羊和菜在一起农夫不在,或者狼羊菜在一起农夫不在，三者都不允许
//        echo '<pre>'; var_dump($values);
        if(in_array($values, $this->_ancestors))
        {
//            $a = $this->_ancestors;
//            array_pop($a);
//            $this->_ancestors = $a;
            return true;
        }
        if(in_array($values, [[1,1,0,0],[0,0,1,1],[1,0,0,1],[0,1,1,0]]))
        {
            return true;
        }
        //判断，之前出现的状态就不继续循环，避免死循环。

        return false;
    }

    protected function showResult(array $state)
    {
        $path = $this->_ancestors;
        $path[] = $state;  //此路线的所有路径。
        Register::append('final_status', $path);
    }
}

