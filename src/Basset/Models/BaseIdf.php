<?php

declare(strict_types=1);

namespace Basset\Models;


/**
 * idf implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BaseIdf extends WeightedModel
{

    CONST E = M_E;

    private $base;

    public function __construct($base = self::E)
    {
        $this->base = $base;
    }

    public function getBase() 
    {
        return $this->base;
    }

    public function setBase($base) 
    {
        $this->base = $base;
    }



}