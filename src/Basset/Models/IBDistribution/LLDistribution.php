<?php

namespace Basset\Models\IBDistribution;


class LLDistribution implements IBDistributionInterface
{


    public function score($tf, $lambda){

        return -log($lambda / ($tf + $lambda));

    }

}