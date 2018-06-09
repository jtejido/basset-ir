<?php

namespace Basset\Structure;
use Basset\Statistics\CollectionStatistics;

class Trie implements TrieInterface
{

    private $trie;

    public function __construct()
    {
        $this->trie = new TrieNode();
    }

    public function getTrie()
    {
        return $this->trie;
    }

}
