<?php


namespace Basset\Structure;

use Basset\Statistics\CollectionStatistics;

class TrieManager implements TrieManagerInterface
{

    protected $trie;

    /**
     * The TrieManager manages all operations relating to a Trie object.
     * At the moment we wouldn't allow deleting and/or appending anything from the tree as it is used mainly for reading
     * what's on the index.
     */
    public function __construct(TrieInterface $trie)
    {
        $this->trie = $trie;
    }

    public function getData()
    {
        return $this->trie;
    }

    public function getTrie(): TrieNode
    {
        return $this->trie->getTrie();
    }

    public function addEntry(string $key, $value = null): bool
    {
        if ($key > '') {
            $trieNodeEntry = $this->getNodeByKey($key, true);
            if ($trieNodeEntry->value === null) {
                $trieNodeEntry->value = [$value];
            } else {
                $trieNodeEntry->value[] = $value;
            }

            return true;
        } else {
            return false;
        }
    }

    public function search($prefix)
    {
        $trieNode = $this->getNodeByKey($prefix);
        if (!$trieNode) {
            return new TrieCollection();
        }
        return $this->getChildren($trieNode, $prefix);
    }

    private function getNodeByKey($key, $create = false)
    {
        $trieNode = $this->trie->getTrie();
        $keyLen = strlen($key);

        $i = 0;
        while ($i < $keyLen) {

            $character = $key[$i++];
            if (!isset($trieNode->children[$character])) {
                if ($create) {
                    $trieNode->children[$character] = new TrieNode();
                } else {
                    return false;
                }
            }
            $trieNode = $trieNode->children[$character];

        };

        return $trieNode;
    }

    private function getChildren(TrieNode $trieNode, $prefix)
    {
        $collection = new TrieCollection();
        if ($trieNode->value !== null) {
            foreach($trieNode->value as $value) {
                if ($value instanceOf TrieEntry) {
                    $collection->add(clone $value);
                } else {
                    $collection->add(
                        new TrieEntry($value, $prefix)
                    );
                }
            }
        }

        if (isset($trieNode->children)) {
            foreach ($trieNode->children as $character => $trie) {
                if($collection->getKeys() == $prefix){
                    $collection->merge(
                        $this->getChildren($trie, $prefix . $character)
                    );
                }
            }
        }
        
        return $collection;
    }
}
