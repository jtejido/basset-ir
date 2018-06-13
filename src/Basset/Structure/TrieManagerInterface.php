<?php


namespace Basset\Structure;


interface TrieManagerInterface
{

    public function addEntry(string $key, $value = null);

    public function search($prefix);

    public function getData();

    public function getTrie();


}
