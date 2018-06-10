<?php

declare(strict_types=1);

namespace Basset\Index;

use Basset\Statistics\EntryStatistics;


class IndexEntry
{

    private $value;

    public function __construct(EntryStatistics $value = null) 
    {
        $this->value = $value;
    }

    public function getValue(): EntryStatistics
    {
        return $this->value;
    }

    public function setValue(EntryStatistics $value) 
    {
        $this->value = $value;
    }

}