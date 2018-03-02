<?php

namespace Basset\Ranking\ProbabilisticDistribution;



class LLDistribution implements ProbabilisticDistributionInterface
{


    public function score($tf, $lambda){

        return -log($lambda / ($tf + $lambda));

    }

}