<?php

namespace Basset\Index;

use Basset\Statistics\EntryStatistics;


class IndexEntry
{

    private $value;

    public function __construct(EntryStatistics $value = null) 
    {
        $this->value = $value;
    }

    public function getValue() 
    {
        return $this->value;
    }

    public function setValue(EntryStatistics $value) 
    {
        $this->value = $value;
    }

}