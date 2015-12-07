<?php

require_once __DIR__.'/vendor/autoload.php';

/*
 Write a program that takes a random string of numbers 
 return the numbers that could be a possible lotto number

 E.g. 
 [ “1”, “42". “100848", “4938532894754”, “1234567”, “472844278465445”]

 Your function should return:
 4938532894754 -> 49 38 53 28 9 47 54 
 1234567 -> 1 2 3 4 5 6 7
 *
*/

$nums = [];
for ($i = 0; $i < 2000; $i++) { 
    $nums[] = (string)mt_rand(1,999999999999999);
}

$set = [ 1, 42,100848,4938532894754,1234567,472844278465445];

$gen = new Lotto\NumberGenerator();
$start = microtime(true);
$results = $gen->parseNumbers(array_merge($set, $nums))
            ->getNumbers();

$actual = ceil((count($results)*100)/count(array_merge($set, $nums)));
echo sprintf("Done in %s seconds - returning with %s percent success rate\n", microtime(true) - $start, $actual);

foreach ($results as $r) {
    print($r."\n");
}


