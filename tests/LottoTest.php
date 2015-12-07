<?php

class LottoTest extends PHPUnit_Framework_TestCase { 

    private $gen;

    public function setUp() { 
        $this->gen = new Lotto\NumberGenerator();
    }

    public function testGoodSet() { 
        $set = [ 1, 42,100848,4938532894754,1234567,472844278465445];

        $results = $this->gen->parseNumbers($set)
            ->getNumbers();

        $this->assertCount(2, $results);
        
    }

    public function testValidZeros() { 
        // 49 6 8 9 4 7 54
        $set = [ 4900006894754 ];
        $results = $this->gen->parseNumbers($set)
            ->getNumbers();

        $this->assertCount(1, $results);
    }

    public function testInvalidZeros() { 
        $set = [ 4900000094754 ];
        // 49 9 4 7 5 4
        $results = $this->gen->parseNumbers($set)
            ->getNumbers();
    
        $this->assertCount(0, $results);
    }

    public function testLargeRandomSet() { 
        $nums = [];
        for ($i = 0; $i < 20000; $i++) { 
            $nums[] = (string)mt_rand(1,999999999999999);
        }

        // aparently we expect on average about 150 rows - so lets be conservative
        $results = $this->gen->parseNumbers($nums)
            ->getNumbers();
        $expected = ceil((100*100)/count($nums));
        $actual = ceil((count($results)*100)/count($nums));

        $this->assertGreaterThanOrEqual($expected, $actual);

        foreach ($results as $r) { 
            $digits = [];
            $str = explode(" ", $r);
            foreach ($str as $s) { 
                $d = str_split($s);
                foreach ($d as $n){
                    $digits[$n] = false;
                }
            }

            $this->assertCount(7, $str);
            foreach ($str as $s) { 
                $d = str_split($s);
                foreach ($d as $n) {
                    if (!$digits[$n]) { 
                        $digits[$n] = true;
                    }
                }

                $this->assertGreaterThanOrEqual(1, $s);
                $this->assertLessThanOrEqual(59, $s);
            }

            $missing = array_filter($digits, function($d) { 
                return $d === false;
            });
            $this->assertCount(0, $missing);
        }

    }
}
