<?php
/*
 Write a program that takes a random string of numbers 
 return the numbers that could be a possible lotto number

 E.g. 
 [ “1”, “42". “100848", “4938532894754”, “1234567”, “472844278465445”]

 Your function should return:
 49 38 53 28 94 75 4 -> 49 38 53 28 9 47 54 
 1234567 -> 1 2 3 4 5 6 7
 *
*/


/*
 * Generate a decent sized list of variable sized random numbers
 */
$nums = [];
for ($i = 0; $i < 20000; $i++) { 
    $nums[] = (string)mt_rand(1,999999999999999);
}

$nums =  [1, 42,100848, 4938532894754, 1234567, 472844278465445];

$start = microtime(true);
$lotto_numbers = [];


// For all of the possible numbers
foreach ($nums as $k => $num)  {
    $num = (string)$num;
    $len = strlen($num);

    // If the number is too small or too big - skip it
    if ($len <  7 || $len > 14) {  
        continue; 
    }

    $current_nums = [];
    $place = 0;
    $step = $len > 7 ? 2 : 1;
    // While we do not have enough numbers 
    // OR we ran out before we got enough ( which shouldnt happen - but its better than being stuck here forever ) 
    while (count($current_nums) != 7 || strlen($num) != 0) { 
        $proposed_num = getProposedNumber($num, $place, $step);

        // If the number is NOT a repeat
        if ( !isset($current_nums[$proposed_num])) { 
            $current_nums[$proposed_num] = $proposed_num;
            
            // remove the numbers we just added 
            // from the string under concideration
            $num = substr($num, strlen($proposed_num));
        }
    }

    array_push($lotto_numbers, $current_nums);
}

var_dump($lotto_numbers);

function getProposedNumber($num, $index, $step) { 
    // Determine what step we should take 
    // Get the next number to conscider
    $proposed_num = substr($num, $index,  $step);

    if (!$proposed_num) { 
        // this never happens at the moment
        var_dump($num);die;
    }

    // if the number is not a valid number
    // Check if it is 2 digits and return the first one
    if (!isValid($proposed_num)) { 
        if ( strlen($proposed_num) === 2) { 
            $proposed_num = $proposed_num[0];
        } else {
            // this never happens atm
            var_dump($num);die;
        }
    } 

    return  $proposed_num;
}



function isValid($num) { 
    return $num >= 1 && $num <= 59;
}

$time = microtime(true) - $start;
echo "Done in {$time}";
