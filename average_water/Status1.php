<?php
class Status
  {
      protected $_values;
      protected $_limit;
      protected $_children;
      protected $_ancestors;

     public function __construct(array $values, array $limit, array $ancestors)
     {
         $this->_values = $values;
         $this->_limit = $limit;
         $ancestors[] = $this->_values;
         $this->_ancestors = $ancestors;

         $this->generateChildren();
     }

     protected function generateChildren()
     {
                 $children = array();

         for($i=0;$i<count($this->_values);$i++){
                         for($o=$i+1;$o<count($this->_values);$o++){
                                 $child = $this->turnStatus($i, $o);
                 if($child === array(4,4,0)){
                                         $this->showResult($child);
                 }
                 if($child !== false && !$this->isAncestor($child) && $child !== array(4,4,0)){
                                         $children[] = new Status($child, $this->_limit, $this->_ancestors);
                 }

                 $child = $this->turnStatus($o, $i);
                 if($child === array(4,4,0)){
                                         $this->showResult($child);
                 }
                 if($child !== false && !$this->isAncestor($child) && $child !== array(4,4,0)){
                                         $children[] = new Status($child, $this->_limit, $this->_ancestors);
                 }
             }
         }
         $this->_children = $children;
     }

     protected function isAncestor($value)
     {
                 foreach ($this->_ancestors as $ancestor) {
                         if($value === $ancestor){
                                 return true;
             }
         }
         return false;
     }

     protected function turnStatus($i, $o)
     {
                 $value = $this->_values;
         if($this->_values[$o] == 0){
                         return false;
         }

         if($this->_values[$i] == $this->_limit[$i]){
                         return false;
         }
         if(($this->_limit[$i] - $this->_values[$i]) <= $this->_values[$o]){
                         $m = $this->_limit[$i] - $this->_values[$i];
         }else{
                         $m = $this->_values[$o];
         }

         $value[$o] -= $m;
         $value[$i] += $m;
         return $value;
     }

     protected function showResult(array $child)
     {
                $path = $this->_ancestors;
         $path[] = $child;
         //print_r($path);
         Register::append('final_status', $path);
         //echo "<br/>";
     }
 }