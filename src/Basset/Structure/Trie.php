<?php

declare(strict_types=1);

namespace Basset\Structure;


class Trie implements TrieInterface
{

    private $trie;

    public function __construct()
    {
        $this->trie = new TrieNode();
    }

    public function getTrie(): TrieNode
    {
        return $this->trie;
    }

}
