<?php

require_once __DIR__.'/vendor/autoload.php';
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
$nums = [29980053193868, 7800000094256, 4900006894754];
        $set = [ 1, 42,100848,4938532894754,1234567,472844278465445];
// 29 9 8 53 19 38 68
// 7 8 9 4 2 5 6 

$gen = new Lotto\NumberGenerator();

$results = $gen->parseNumbers(array_merge($set, $nums))
            ->getNumbers();

        var_dump($results);

