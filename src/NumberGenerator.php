<?php

namespace Lotto;

/**
 * Class NumberGenerator
 * @package Lotto
 */
class NumberGenerator { 
    private $lotto_numbers;

    public function __construct() { 
        $this->lotto_numbers = [];
    }

    /**
     * Takes an array of numbers to filter
     * @param array $nums
     * @return $this
     */
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
        }

        return $this;
    }

    /**
     * Returns the filtered numbers
     * @return array
     */
    public function getNumbers() {
        return $this->lotto_numbers;
    }

    /**
     * Parses an individual number
     * @param $num
     * @param $len
     * @return bool|string
     */
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
        }
        
        if (count($current_nums) !== 7 || $num != false) { 
            return false;
        }

        foreach ($current_nums as $i => $n ) { 
            if (substr($n, 0, 1) == '0') { 
                $current_nums[$i] = $n[1];
            }
        }

        if (count($current_nums) === 7 ) { 
            return implode(" ", $current_nums);
        }

        return false;

    }

    /**
     * Adds a number to our list and removes used items
     * @param $num
     * @param $proposed_num
     * @param array $current_nums
     * @return array
     */
    protected function pushNumber($num, $proposed_num, array $current_nums) { 
        $num = substr($num, strlen($proposed_num));


        $current_nums[$proposed_num] = $proposed_num;

        return [ $num, $current_nums ];
    }

    /**
     * Tries its best to find a valid number
     * Returns false on failure
     * @param $num
     * @param $step
     * @param $current
     * @return array|bool
     */
    protected function getProposedNumber($num,  $step, $current) { 
        $error = false;
        $len = strlen($num);
        $current_count = count($current);
        if ( ($len + $current_count  === 7) ) { 
            $proposed_num = str_split(substr($num, 0, $step))[0];
        } else {
            $proposed_num = substr($num, 0,  $step);
        }

        $last_num = $proposed_num;
        while (!$this->isValid($proposed_num, $current) ) { 
            if ($num === false) { 
                break;
            }
            while (substr($num, 0, 1) === '0') { 
                $num = substr($num, 1);
            }

            if (
                ($current_count + ceil($len / 2) >= 7)
                || ($len <= 5 && $current_count == 2)
                || ($len + $current_count  === 7)
                || $last_num === $proposed_num
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
            $last_num = $proposed_num;

        }  

        if (strlen($proposed_num) === 0 ) {
            $error = true;
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
