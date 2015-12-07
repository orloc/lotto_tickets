<?php

namespace Lotto;

class NumberGenerator { 
    private $start;
    private $lotto_numbers;

    public function __construct() { 
        $this->start = microtime(true);
        $this->lotto_numbers = [];
    }

    public function __destruct() { 
        $time = microtime(true) - $this->start;
        echo "Done in {$time}";
    }

    public function parseNumbers(array $nums) { 
        foreach ($nums as $k => $num)  {
            $num = (string)$num;
            $len = strlen($num);

            if ($len <  7 || $len > 14) {  
                continue; 
            }

            $current_nums = $this->doParse($num, $len);

            if ($current_nums !== false) { 
                $this->addNumber($current_nums);
            }
            echo "==============";
            var_dump($this->getNumbers());
        }

        return $this;
    }

    public function getNumbers() {
        return $this->lotto_numbers;
    }

    protected function doParse($num, $len) { 
        $current_nums = [];
        $step = $len > 7 ? 2 : 1;

        while (count($current_nums) !== 7 && strlen($num) !== 0) { 
            $data = $this->getProposedNumber($num,  $step, $current_nums);
            if ($data === false ) { 
                return false;
            }

            list($num, $proposed_num) = $data;

            if (is_array($proposed_num)) { 
                foreach ($proposed_num as $n) { 
                    list($num, $current_nums) = $this->pushNumber($num, $n, $current_nums);
                }
            } else {
                list($num, $current_nums) = $this->pushNumber($num, $proposed_num, $current_nums);
            }

            var_dump($current_nums);
        }
        
        if (count($current_nums) !== 7 || $num != false) { 
            return false;
        }

        foreach ($current_nums as $i => $n ) { 
            if (substr($n, 0, 1) == '0') { 
                $current_nums[$i] = $n[1];
            }
        }

        return implode(" ", $current_nums);
    }

    protected function pushNumber($num, $proposed_num, array $current_nums) { 
        $num = substr($num, strlen($proposed_num));


        $current_nums[$proposed_num] = $proposed_num;

        return [ $num, $current_nums ];
    }
    
    protected function getProposedNumber($num,  $step, $current) { 
        $error = false;
        if ( (strlen($num) + count($current)  === 7) ) { 
            $proposed_num = str_split(substr($num, 0, $step))[0];
        } else {
            $proposed_num = substr($num, 0,  $step);
        }


        while (!$this->isValid($proposed_num, $current) ) { 
            while (substr($num, 0, 1) === '0') { 
                $num = substr($num, 1);
            }

            if (
                (count($current) + ceil(strlen($num) / 2) >= 7)
                || (strlen($num) <= 5 && count($current) == 2)
                || (strlen($num) + count($current)  === 7)
            ){
                $proposed_num = str_split(substr($num, 0, $step))[0];
            } else {
                $proposed_num = substr($num, 0,  $step);
            }


            if (isset($current[$proposed_num])) { 
                $lastTry = $proposed_num.substr($num, 1,1);
                if ($this->isValid($lastTry, $current)) {
                    $proposed_num = $lastTry;
                } else {
                    $error = true;
                    break;
                }
            }

        }  


        return  $error ? false : [ $num, $proposed_num] ;
    }

    protected function isValid($num, $current) { 
        return !isset($current[$num]) && ($num >= 1 && $num <= 59);
    }

    protected function addNumber($current_nums) { 
        array_push($this->lotto_numbers, $current_nums);
        return $this;
    }
}
