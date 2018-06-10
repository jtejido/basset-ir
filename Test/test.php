<?php
include_once('../autoload.php');
include_once('../Cranfield/cranfield_parser.php');

use Basset\Documents\Document;
use Basset\Documents\TokensDocument;

use Basset\Search\Search;
use Basset\Models\TfIdf;
use Basset\Models\BM25;
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



class Similarity {

    public function test() {

        // Initialized required stuff.

        // Change directory in /Cranfield/cranfield_parser.php if needed. This is hard-coded there.
        $path = './cranfield_parsed/'; 

        $stopwords = file_get_contents('../stopwords/stopwords.txt');
        $tokenizer = new WhitespaceAndPunctuationTokenizer;

        $pipeline = array(
                    new StopWords($tokenizer->tokenize($stopwords)),
                    new English,
                    // also stemmer if you have any, as I don't have any.
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
         * Once created, IndexReader() takes care of reading it, this creates an in-memory trie structure
         * for fast traversal during the Search() phase.
         *
         * If a custom directory path is created (and an optional custom file name thru setFileName()), you need 
         * to specify the path in IndexReader(), otherwise it'll just look for a default file (index/basset_index.idx).
         *
         * The created index file acts as the persistent data for all documents added in the collection. Once created,
         * you can comment out the code below to avoid re-indexing the same collection during run-time (if you're
         * simply trying out different models for the same collection, e.g., NPL, Medline, TREC, etc.).
         *
         */

        $index = new IndexWriter('../custom_index');
        $index->setFileName('mycustomindex');
        $files = glob($path . '*');
        foreach($files as $file){
            $index->addDocument(new TokensDocument($tokenizer->tokenize(file_get_contents($file))), basename($file));
        }
        $index->applyTransformation($transform);
        $index->close();

        /** 
         * Dumping $index->getLocation() gives '../custom_index/mycustomindex.idx' which should be fed as parameter
         * for IndexReader.
         */

        // prepare one query as Document instance from Cranfield/cranfield-collection/cran.qry.xml-format
        $query = new Document(new TokensDocument($tokenizer->tokenize('what similarity laws must be obeyed when constructing aeroelastic models of heated high speed aircraft .')));
        $query->applyTransformation($transform);


        /**
         *
         * Start search.
         * There has been changes in class name and operations since the v1 release (to accomodate for
         * structural changes and for those familiar with Lucene instantiations).
         * DocumentRanking became Search() which requires an IndexReader instance.
         * documentModel() became model where query model and a metric is already specified inside.
         * You can still specify them thru queryModel() and similarity(), but a default is given from the docs at
         * https://myth-of-sissyphus.blogspot.com/2018/02/basset-information-retrieval-library-in.html
         * 
         */

        $indexReader = new IndexReader('../custom_index/mycustomindex.idx'); // read the custom index specified above

        $search = new Search($indexReader);
        $search->query($query);
        $search->model(new TfIdf);
        print_r($search->search(15)); 

        /* 
         * search() returns an array in descending order, and can take a limit number as parameter to display some 
         * limited amount of stuff, as 1400 items will surely be dumped on your terminal (default is 10).
         */

    }

}

// parse Cranfield xml first before getting relevance
$collection = new CranfieldParser('../Cranfield/cranfield-collection/cran.all.1400.xml-format.xml');
$collection->parse();
$sim = new Similarity;
$sim->test();


