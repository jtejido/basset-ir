<?php


namespace Basset\Index;

use Basset\Statistics\EntryStatistics;

/**
 * IndexEntry is primarily used inside the Index class. Adding value to an Index instance should be done thru 
 * addIndex() method from Index.
 * 
 * @see EntryStatistics
 * @see Index
 *
 * @var $value
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class IndexEntry
{

    private $value;

    public function __construct(EntryStatistics $value = null) 
    {
        $this->value = $value;
    }

    /**
     * @return EntryStatistics
     */
    public function getValue(): EntryStatistics
    {
        return $this->value;
    }

    /**
     * @param EntryStatistics $value The entry statistics for the given term in the Index instance.
     */
    public function setValue(EntryStatistics $value) 
    {
        $this->value = $value;
    }

}