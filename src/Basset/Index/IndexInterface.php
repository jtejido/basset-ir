<?php

namespace Basset\Index;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Utils\TransformationInterface;


interface IndexInterface
{

	public function getCollectionStatistics(): CollectionStatistics;

    public function setCollectionStatistics(CollectionStatistics $cs);

    public function addEntry(string $key, EntryStatistics $value);

    public function getData(): array;

}
