<?php
$sum = 0;
if(5&1)
{
    $sum += 1;
}
if(5&2)
{
    $sum += 2;
}
if(5&4)
{
    $sum += 4;
}
if(5 & 8)
{
    $sum += 8;
}
if(5 & 16)
{
    $sum += 16;
}
if(5 & 32)
{
    $sum += 32;
}

echo $sum ;