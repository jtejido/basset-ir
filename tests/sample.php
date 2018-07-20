<?php
include_once(__DIR__.'/../autoload.php');
include_once(__DIR__.'/../Cranfield/cranfield_parser.php');

use Basset\Documents\Document;
use Basset\Documents\TokensDocument;

use Basset\Search\Search;
use Basset\Models\ModBM25;
use Basset\Models\PivotedTfIdf;
use Basset\Metric\SqrtCosineSimilarity;

use Basset\Normalizers\English;
use Basset\Tokenizers\WhitespaceAndPunctuationTokenizer;
use Basset\Utils\StopWords;
use Basset\Utils\TransformationSet;

use Basset\Index\IndexReader;
use Basset\Index\IndexWriter;

use Basset\Models\DFIModel;
use Basset\Models\DFIModels\ChiSquared;
use Basset\Models\Idf;

use Basset\MetaData\MetaData;
use Basset\Expansion\IdeDecHi;

// These two are still experimental, use it for VSM models at this moment
use Basset\Expansion\DifferentialEvolution;
use Basset\Expansion\GeneticAlgorithm;


class Similarity {

    public function test() {
        $start = microtime(true);
        // Initialized required stuff.

        // THE DOCUMENTS
        $cranfield = new CranfieldParser(__DIR__.'/../Cranfield/cranfield-collection/cran.all.1400.xml-format.xml');
        $documents = $cranfield->parse(); 

        // This is a set of NLP stuff used to analyze each tokens(terms) in a given Document.
        $stopwords = file_get_contents(__DIR__.'/../stopwords/stopwords.txt');
        $tokenizer = new WhitespaceAndPunctuationTokenizer;

        $pipeline = array(
                    new StopWords($tokenizer->tokenize($stopwords)),
                    new English,
                    // also stemmer if you have any, as I don't have any. Make sure it implements TransformationInterface
                    );
        $transform = new TransformationSet;
        $transform->register($pipeline);

        /** 
         *
         * Start indexing files.
         * IndexWriter() takes an optional directory path, and it will create an index folder with an .idx file inside.
         * 
         * Everything is commited to disk once close() is called, otherwise you can keep adding document/s.
         * The file created is Basset's inverted index file.
         *
         * Once created, IndexReader() takes care of reading it.
         *
         * If a custom directory path is created (and an optional custom file name thru setFileName()), you need 
         * to specify the path in IndexReader(), otherwise it'll just look for a default file (index/basset_index.idx).
         *
         * The created index file acts as the persistent data for all documents added in the collection. Once created,
         * you can comment out the code below to avoid re-indexing the same collection during run-time (if you're
         * simply trying out different models for the same collection, e.g., NPL, Medline, TREC, etc.).
         *
         */

        $index = new IndexWriter(__DIR__.'/../custom_index');
        $index->setFileName('mycustomindex');
        foreach($documents as $title => $body){
            $index->addDocument(new TokensDocument($tokenizer->tokenize($body)), new MetaData(array('title' => $title)));
        }
        $index->applyTransformation($transform);
        $index->close();

        // MetaData class is a wrapper for assigning any array of info for a given doc, be it a title, path or a url, etc.

        /** 
         * Dumping $index->getLocation() gives '../custom_index/mycustomindex.idx' which should be fed as parameter
         * for IndexReader.
         */

        // prepare one query as Document instance from Cranfield/cranfield-collection/cran.qry.xml-format
        $query = new Document(new TokensDocument($tokenizer->tokenize(' what theoretical and experimental guides do we have as to turbulent couette flow behaviour . ')));
        $query->applyTransformation($transform);


        /**
         *
         * Start search.
         *
         * There has been changes in class name and operations since the v1 release (to accomodate for
         * structural changes).
         *
         * DocumentRanking became Search(mostly working as a manager for everything) and requires an IndexReader 
         * instance.
         *
         * Weighting Models are set thru model(), where the weighting model used for the query and the metric for 
         * comparing the query against the documents are explicitly set.
         * You can still change them thru queryModel() and similarity(), and the info regarding the defaults are 
         * given from the docs at
         * https://myth-of-sissyphus.blogspot.com/2018/02/basset-information-retrieval-library-in.html
         * 
         */

        $indexReader = new IndexReader(__DIR__.'/../custom_index/mycustomindex.idx'); // read the custom index specified above

        $search = new Search($indexReader);
        $search->query($query);
        $search->model(new ModBM25);
        $search->setQueryExpansion(new IdeDecHi); //all feedback types default to top 10 relevant and non-relevant docs and querylength + 100 top terms to be used for expansion.
        $results = $search->search(15); // defaults to 10

        $display = array();

        foreach($results->getResults() as $result) {
            $title = $result->getMetaData()->getTag('title'); //getting the title tag from metadata added for the doc.
            $display[$title] = $result->getScore();
        }
        
        print_r($display); // top K docs
        print_r(microtime(true) - $start . "\xA");

        /**
         * search() returns an instance of ResultSet in descending order, and can take a $limit number and boolean $descending as 
         * parameter to display stuff, as 1400 items is a lot of stuff (default is search(10, true)).
         * ResultSet displays result as array thru getResults(), it has docID, score and the given MetaData for the document.
         */

    }

}

// parse Cranfield xml first before getting relevance

$sim = new Similarity;
$sim->test();


