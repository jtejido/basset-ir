<?php


namespace Basset\Results;

use Basset\MetaData\MetaData;


/**
 * An object that is added as an entry to result set, it's a storage for docID, score and the doc's metadata.
 *
 * @var $id
 * @var $score
 * @var $meta
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class ResultEntry
{

    private $id;

    private $score;

    private $meta;

    public function __construct(int $id, float $score, MetaData $meta)
    {
        $this->id = $id;
        $this->score = $score;
        $this->meta = $meta;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;   
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;   
    }

    /**
     * @return MetaData
     */
    public function getMetaData(): MetaData
    {
        return $this->meta;   
    }

}
