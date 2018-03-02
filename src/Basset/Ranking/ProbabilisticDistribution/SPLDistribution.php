<?php

namespace Basset\Ranking\ProbabilisticDistribution;



class SPLDistribution implements ProbabilisticDistributionInterface
{


    public function score($tf, $lambda){

        $exp = $tf/($tf + 1);

        return -log((pow($lambda, $exp) - $lambda) / (1 - $lambda));

    }

}