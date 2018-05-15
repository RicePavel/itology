<?php

namespace App\Component;

class MathExpression {
    
    private $text = '';
    private $result = '';
    
    public function __construct() {
        $text = '';
        $result = '';
        $num_1 = rand(1, 30);
        $num_2 = rand(1, 30);
        $signs = [1 => "+", 2 => "-"];
        $sign = $signs[rand(1, 2)];
        $text = $num_1 . " " . $sign . " " . $num_2;
        if ($sign === "+") {
            $result = $num_1 + $num_2;
        } else if ($sign === "-") {
            $result = $num_1 - $num_2;
        }
        $this->text = $text;
        $this->result = $result;
    }
    
    function getText() {
        return $this->text;
    }

    function getResult() {
        return $this->result;
    }


    
}

