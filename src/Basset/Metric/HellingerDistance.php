<?php


namespace Basset\Metric;


/**
 * @see https://en.wikipedia.org/wiki/Hellinger_distance
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class HellingerDistance extends Metric implements VSMInterface, DistanceInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function dist(array $a, array $b): float
    {

        if(empty($a) || empty($b)){
            throw new \InvalidArgumentException('Vector $' . (empty($a) ? 'a' : 'b') . ' is not an array');
        }
        
        $meanV1 = $this->math->mean(array_count_values(array_keys($a)));
        $meanV2 = $this->math->mean(array_count_values(array_keys($b)));

        $n = count($a);
        $sum = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $sum += pow(sqrt($a[$key]/$meanV1)-sqrt($b[$key]/$meanV2),2);
            }
        }


        return sqrt($sum) * (1/sqrt(2));

    }


}
