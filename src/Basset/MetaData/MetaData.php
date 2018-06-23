<?php


namespace Basset\MetaData;



/**
 * An object that wraps an array of metadata information given for a certain document, it doesn't matter what it is,
 * it could be a title, url, path for the file, etc. as long as it's provided a proper key which works as its tag.
 *
 * @var $metadata
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class MetaData
{


    private $metadata;

    public function __construct(array $metadata = array())
    {
        $this->metadata = $metadata;
    }

    /**
     * @param string $tag
     * @param mixed $value
     */
    public function addData(string $tag, string $value)
    {
        $this->metadata[$tag] = $value;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->metadata;   
    }

    /**
     * @param string $tag The metadata tag.
     * @return string
     */
    public function getTag(string $tag): string
    {
        if(!isset($this->metadata[$tag])){
            throw new \Exception("No " . $tag . " tag found."); 
        }

        return $this->metadata[$tag];
    }

}
