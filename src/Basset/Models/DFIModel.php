<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface,
        IDFInterface
    };
use Basset\Models\Statistics\{
        EntryStatistics,
        CollectionStatistics
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermCount,
        Models\DFIModels\DFIInterface
    };

class DFIModel extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

/**
 * Provides a framework for nonparametric index term weighting model using the notion of independence, as described
 * in Kocabas et al's paper.
 * @see http://trec.nist.gov/pubs/trec18/papers/muglau.WEB.MQ.pdf
 *
 * DFI models are obtained by instantiating the three components of the framework: 
 * selecting a divergence measure and selecting an idf method, take note that it uses log2 similar to DFR.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

    protected $model;

    protected $idf;

    protected $index;

    public function __construct(DFIInterface $model, IDFInterface $idf)
    {
        parent::__construct();
        $this->model = $model;
        $this->idf = $idf;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
        if ($this->model == null || $this->idf == null) {
            throw new \Exception("Null Parameters not allowed.");
        }
    }

    public function setStats(EntryStatistics $stats)
    {
        $this->stats = $stats;
        $this->model->setStats($this->stats);
        $this->idf->setStats($this->stats);
    }

    public function setCollectionStatistics(CollectionStatistics $cs)
    {
        $this->cs = $cs;
        $this->model->setCollectionStatistics($this->cs);
        $this->idf->setCollectionStatistics($this->cs);
    }


    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        $this->idf->setBase(2); // because the paper uses base 2 for all idf
        $dfi = $this->model->score($tf, $docLength, $docUniqueLength); 
        $idf = $this->idf->score($tf, $docLength, $docUniqueLength);

        return $dfi * $idf;
    }


}