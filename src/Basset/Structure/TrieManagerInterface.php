<?php

namespace Basset\Structure;


interface TrieManagerInterface
{

    public function addEntry($key, $value = null);

    public function search($prefix);

    public function getData();

    public function getTrie();


}
