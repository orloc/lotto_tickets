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
     * For every number in our list
     * convert that number to a string and find its length
     * check to see if our string is too small // too big and skip it if so
     * send the number to be parsed
     * if we got a valid lotto number back add it to our list of numbers
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
     * Initialize our step and current lotto number we are working on
     * While we dont have a full lotto number AND we havn't run out of numbers to make one with
     * Get a suggested number
     * If its valid push it onto our stack of numbers 
     * If we complete WITHOUT 7 number OR with numbers left over we failed 
     * Remove leading 0's 
     * Final Sanity Check we have exactly 7 numbers and return our imploeded string
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
     * Shift the proposed number off the active number string
     * Add the propsoed number to our list of numbers
     * return the mutated data
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
     *
     * Update the length of the working number, and how many numbers we have so far
     * IF the lenght of our string + the current numbers have is exactly 7 - override what ever step is and force a step of 1
     * 
     * While the number we generated is not valid TRY AGAIN WITH VIGOR 
     * if our number is false - we failed
     * remove all LEADING 0's before actually trying
     * Check all our edge cases which preclude us wanting to take a single step
     * IF after all that our number is a duplicate 
     * TRY ONE LAST TIME else throw an error and break
     *
     * update the last generate number to make sure we know when we get stuck
     *
     * return the number or false on failure
     *
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

    /**
     * Validates a number against the current lotto numbers and general lotto rules
     * @param $num
     * @param $current
     */
    protected function isValid($num, $current) { 
        return !isset($current[$num]) && ($num >= 1 && $num <= 59);
    }

    /**
     * Adds a number to our list
     * @param $current_nums
     */
    protected function addNumber($current_nums) { 
        array_push($this->lotto_numbers, $current_nums);
        return $this;
    }
}
