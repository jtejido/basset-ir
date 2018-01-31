<?php

namespace Basset\Similarity;

/**
 * This class implements the Levenshtein distance of two sets.
 * This accepts 2 arrays of arbitrary lengths.
 * https://en.wikibooks.org/wiki/Algorithm_Implementation/Strings/Levenshtein_distance#PHP
 * 
 */
class LevenshteinDistance implements DistanceInterface
{
    /**
     * Count the number of positions that A and B differ.
     *
     * @param  string $A
     * @param  string $B
     * @return int    The Levenshtein distance of the two strings A and B
     */
    public function dist(array $A, array $B)
    {
        $A = array_values($A);
        $B = array_values($B);
        $m = count($A);
        $n = count($B);
        
            for($i=0;$i<=$m;$i++) $d[$i][0] = $i;
            for($j=0;$j<=$n;$j++) $d[0][$j] = $j;
            
            for($i=1;$i<=$m;$i++) {
                for($j=1;$j<=$n;$j++) {
                    $c = ($A[$i-1] == $B[$j-1])?0:1;
                    $d[$i][$j] = min($d[$i-1][$j]+1,$d[$i][$j-1]+1,$d[$i-1][$j-1]+$c);
                }
            }

            return $d[$m][$n];
    }
}
