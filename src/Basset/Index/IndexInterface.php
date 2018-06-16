<?php


namespace Basset\Index;

use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics
    };
use Basset\Utils\TransformationInterface;

/**
 * An Index Representation.
 */

interface IndexInterface extends \Iterator, \ArrayAccess, \Countable
{

	public function getCollectionStatistics(): CollectionStatistics;

    public function setCollectionStatistics(CollectionStatistics $cs);

    public function addEntry(string $key, EntryStatistics $value);

    public function getData(): array;

}
