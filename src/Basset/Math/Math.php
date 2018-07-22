<?php


namespace Basset\Math;

/**
 * Math Library
 * Only place here math operations not having PHP counterpart.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Math
{


    /**
     * Returns the logarithm in base 2 of e, used to change the base of logarithms
     *
     * @return float
     */
    public function log2ofE(): float
    {
        return 1 / log(2);
    }

    /**
     * Stirling formula for the power series.
     * 
     * @param float $a The parameter of the Stirling formula.
     * @param float $b The parameter of the Stirling formula.
     * @return float
     */
    public function stirlingPower(float $a,float $b): float
    {
        $diff = $a - $b;
        return ($b + 0.5) * log($a / $b) + $diff * log($a);
    }

    /**
     * Euclidean norm
     * ||x||2 = sqrt(x・x) // ・ is a dot product
     *
     * @param array $vector
     * @return float
     */
    public function euclideanNorm(array $vector): float
    {
        return sqrt($this->dotProduct($vector, $vector));
    }

    /**
     * Taxicab norm
     * ||x||1 = ∑ |x|
     *
     * @param array $vector
     * @return float
     */
    public function taxicabNorm(array $vector): float
    {
        return 
            array_sum(
                array_map(
                    function ($x) {
                        return abs($x);
                    },
                    $vector
                )
            );
    }

    /**
     * Dot product
     * a・b = summation{i=1,n}(a[i] * b[i])
     *
     * @param array $a
     * @param array $b
     * @return float
     */
    public function dotProduct(array $a, array $b): float
    {
        $dotProduct = 0;
        $keysA = array_keys(array_filter($a));
        $keysB = array_keys(array_filter($b));
        $uniqueKeys = array_unique(array_merge($keysA, $keysB));
        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key]))
                $dotProduct += ($a[$key] * $b[$key]);
        }
        return $dotProduct;
    }

    /**
     * Statistical mean/average of vector
     *
     * @param array $arr
     * @return mixed
     */
    public function mean(array $arr)
    {
        return ($arr) ? array_sum($arr)/count($arr) : 0;
    }

    /**
     * Random Gen between min and max
     *
     * @param int $min
     * @param int $max
     * @return mixed
     */
    public function random($min = 0, $max = 1)
    {
        return $min + lcg_value() * (abs($max - $min));
    }

    /**
     * Cumulative distribution function of the Cauchy distribution
     *
     * @param float $x
     * @param float $gamma
     * @return float
     */
    public function cauchyGenerator(float $x, float $gamma): float
    {
        return (1 / pi()) * atan(($this->random(0, 1) - $x) / $gamma) + .5;
    }

    /**
     * Digamma (psi) function
     * J Bernardo,
     * Psi ( Digamma ) Function,
     * Algorithm AS 103,
     * Applied Statistics,
     * Volume 25, Number 3, pages 315-317, 1976.
     * From http://www.psc.edu/~burkardt/src/dirichlet/dirichlet.f
     * Extended based on Radfort Neal
     * http://www.cs.toronto.edu/~radford/fbm.software.html
     *
     * @param float $x
     * @return float
     */
    public function digamma(float $x): float
    {
        $large = 9.5;
        $d1 = -0.5772156649015328606065121;
        $d2 = pow(pi(),2)/6;
        $small = 1e-6;
        $s3 = 1.0/12.0;
        $s4 = 1.0/120.0;
        $s5 = 1.0/252.0;
        $s6 = 1.0/240.0;
        $s7 = 1.0/132.0;
        $s8 = 691.0/32760.0;
        $s9 = 1.0/12.0;
        $s10 = 3617.0/8160.0;
        $y = 0.0;
        $r = 0.0;
        

        if ($x == 0.0) {
            return -1.0/0.0;
        }
        
        if ($x < 0.0) {
            $y = $this->digamma(-$x+1) + pi()*(1.0/tan(-pi()*$x));
            return $y;
        }
        
        if ($x <= $small) {
            $y = $y + $d1 - 1.0/$x + $d2*$x;
            return $y;
        }
        
        while(true) {
            if ($x > $small && $x < $large) {
                $y = $y - 1.0/$x;
                $x = $x + 1.0;
            } else {
                break;
            }
        }
        
        if ($x >= $large) {
          $r = 1.0/$x;
          $y = $y + log($x) - 0.5*$r;
          $r = $r * $r;
          $y = $y - $r * ( $s3 - $r * ( $s4 - $r * ($s5 - $r * ($s6 - $r * ($s7 - $r * ($s8 - $r * ($s9 - $r * $s10)))))));
        }
        
        return $y;
    }

}