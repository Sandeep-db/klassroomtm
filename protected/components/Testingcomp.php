<?php

class Testingcomp extends CComponent
{
    public function add($x, $y)
    {
        return $x + $y;
    }

    public function divide($x, $y)
    {
        return $x / $y;
    }

    public function exceptiontestinghelper($x, $y, $z)
    {
        if ($x != 0) {
            if ($y != 0) {
                return (($x * $y) - ($y * $z)) / ($x * $y);
            } else {
                throw new Exception('x or y should not be zero');
            }
        } else {
            throw new Exception('x or y should not be zero');
        }
    }

    public function warning()
    {
        $a = 10;
        unset($a);
        echo $a;
    }

    public function error()
    {
        $x = $y + 0;
    }

    public function notice()
    {
        $array = [];
        $value = $array['1'];
    }


    public function sayhello()
    {
        // return "Hello";
    }
}
