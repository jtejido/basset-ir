<?php


namespace Basset\Results;


/**
 * An object that wraps the results, an array of ResultEntry.
 *
 * @var $resultset
 * @var $order
 * @var $limit
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class ResultSet
{


    CONST DEFAULT_ORDER = 0;

    CONST DEFAULT_LIMIT = 10;

    private $resultset;

    private $order;

    private $limit;

    public function __construct()
    {
        $this->resultset = array();
        $this->order = self::DEFAULT_ORDER;
        $this->limit = self::DEFAULT_LIMIT;
    }

    /**
     * Doc ID with its score.
     *
     * @param int $docId, float $score
     */
    public function addEntry(ResultEntry $entry)
    {
        $this->resultset[] = $entry;   
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order)
    {
        $this->order = $order;   
    }

    /**
     * @param int $order
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getResultSize(): int
    {
        return count($this->resultset);   
    }

    /**
     * @return array
     */
    public function getDocIds(): array
    {
        $results = array();

        foreach ($this->resultset as $key => $value)
        {
            $results[] = $value->getId();
        }

        return $results;
    }

    /**
     * Orders the array in ascending order, then remove items after the first N of array.
     *
     * @return array
     */
    public function getResults(): array
    {
        
        $results = array();

        foreach ($this->resultset as $key => $value)
        {
            $results[$key] = $value->getScore();
        }

        if ($this->order === 0) {
            array_multisort($results, SORT_DESC, $this->resultset);
        } else {
            array_multisort($results, SORT_ASC, $this->resultset);
        }

        array_splice($this->resultset, $this->limit);

        return $this->resultset;
    }

}
