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

            // If the number is too small or too big - skip it
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

    public function getNumbers() {
        return $this->lotto_numbers;
    }

    // While we do not have enough numbers 
    // OR we ran out before we got enough ( which shouldnt happen - but its better than being stuck here forever ) 
    // If the number is NOT a repeat
    // remove the numbers we just added 
    // from the string under concideration plus the additonal place skipped if any
    // Check if we skipped a bunch of invalid numbers to get here 
    // IF so should re-evalutate our step or break if we cant make this work
    // add the item we havnt added yet to account for its potential
    // continue if all hope is lost else 
    // finish the rest of the number with this step 
    protected function doParse($num, $len) { 
        $current_nums = [];
        $initial = 0;
        $step = $len > 7 ? 2 : 1;

        while (count($current_nums) !== 7 && strlen($num) !== 0) { 
            list($proposed_num, $place, $initial) = $this->getProposedNumber($num, $initial, $step, $current_nums);

            $num = substr($num, strlen($proposed_num) + $place);

            if ( !isset($current_nums[$proposed_num]) ) { 
                if ($place > $initial) { 
                    $can_continue = count($current_nums) + 1 + strlen($num) >= 7;
                    if (!$can_continue) { 
                        continue;
                    } else { 
                        $step = 1;
                    }
                }
                $current_nums[$proposed_num] = $proposed_num;
            } else {
                continue;
            }
        }

        if (count($current_nums) !== 7) { 
            return false;
        }

        return implode(" ", $current_nums);
    }
    
    // Determine what step we should take 
    // Get the next number to conscider
    // if the number is not a valid number
    // Check if it is 2 digits and return the first one
    // skip ahead until we find something or run out
    protected function getProposedNumber($num, $index, $step, $current) { 
        if (count($current) === 6 && strlen($num) === 2 && $this->isValid($num, $current)) { 
            $proposed_num = $num;
        } else {
            $proposed_num = substr($num, $index,  $step);
            $val = function($num) { 
                return count(array_filter(str_split($num), function($n) { 
                    return intval($n) === 0;
                })) <= 1;
            };

            $init_index = $index;
            while (!$this->isValid($proposed_num, $current) && isset($num[$index])) { 
                $index++;
                if ( strlen($proposed_num) === 2 && $val === 0) { 
                    $proposed_num = $proposed_num[0];
                } else {
                    $proposed_num = substr($num, $index,  $step);
                }
            }  
            var_dump($proposed_num);
        }

        $clean_num = trim($proposed_num, "0");

        return  [ $clean_num, $index, isset($init_index) ? $init_index : 0];
    }

    protected function isValid($num, $current) { 
        return !isset($current[$num]) && (intval($num) >= 1 && intval($num) <= 59);
    }

    protected function addNumber($current_nums) { 
        array_push($this->lotto_numbers, $current_nums);
        return $this;
    }
}
