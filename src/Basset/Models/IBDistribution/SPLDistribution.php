<?php

namespace Basset\Models\IBDistribution;


class SPLDistribution implements IBDistributionInterface
{


    public function score($tf, $lambda){

        $exp = $tf/($tf + 1);

        return -log((pow($lambda, $exp) - $lambda) / (1 - $lambda));

    }

}