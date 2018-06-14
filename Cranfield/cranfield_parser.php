<?php

/**
 *
 * Parses XML Cranfield collection(trec_format).
 *
 */

Class CranfieldParser {


	public function __construct($file)
    {
        $this->file = $file;
    }

	public function parse()
	{
		$doc = file_get_contents($this->file);
		$string = str_replace(array("\r", "\n"), '', $doc);
		$xml = new \SimpleXMLElement($string);
		$documents = array();
		foreach($xml as $key => $value){
			$file = null;
			$filename = null;
			foreach($value as $node => $content){
				if ($node === 'DOCNO'){
					$filename = (string)$content[0];
				}

				if ($node === 'TEXT'){
					$file = (string)$content[0];
				}

				if($file && $filename) {
					if(!isset($documents[$filename])) {
						$documents[$filename] = $file;
					}
				}
				
			}
		}

		return $documents;
	}
}

