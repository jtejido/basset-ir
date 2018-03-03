<?php

namespace Basset\Ranking\IDF;

/**
 * Assuming we can represent the term frequency within a document
 * as a density function, we can take this to be a uniform distribution; that is,
 * the density function of the term frequency is constant. The H1 hypothesis is a
 * variant of the verbosity principle of Robertson [Robertson and Walker 1994]
 */

class OkapiIdf extends BaseIdf implements IdfInterface
{


    public function getIdf() {
    	return $this->math->DFRlog(($this->getNumberOfDocuments()-$this->getDocumentFrequency()+0.5)/($this->getDocumentFrequency() + 0.5));
    }

}