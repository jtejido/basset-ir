<?php

namespace Basset\Index;


interface IndexInterface
{
    public function getCollectionStatistics();

    public function getEntryStatistics();
}
