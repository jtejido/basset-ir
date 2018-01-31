
![Droopy](https://i.imgflip.com/1c38he.jpg)


Basset
=============

Basset is a PHP Information Retrieval library. It provides different ways of searching through documents in a collection by applying advanced and experimental IR techniques gathered from different Research studies and Conferences, most notably:

1. [TREC](http://trec.nist.gov/) 

2. [SIGIR](http://sigir.org/)

3. [ECIR](http://irsg.bcs.org/ecir.php)

4. [ACM](https://www.acm.org/)


Basics
=============


Installation
-------------

In your composer.json, you'll have to write it like this:

```
{
    "require": {
        "jtejido/basset": "1.0.*@dev"
    }
}
```


Adding Documents
-------------

Basset manages adding document thru the CollectionSet Class. It expects TokensDocument instance which is a plain wrapper for Tokenized documents.

```
use Basset\Tokenizers\WhitespaceTokenizer;
use Basset\Collections\CollectionSet;

// Get files
$doc1 = file_get_contents('../location/for/file1.txt');
$doc2 = file_get_contents('../location/for/file2.txt');
$doc3 = file_get_contents('../location/for/file3.txt');

// Instantiate tokenizer
	$tokenizer = new WhitespaceTokenizer();

/* 
 * Add docs to Corpus and transform (set to true, add the file $class or $id for supervised ranking)
 * Set this to false if unsupervised -- default: false
 */
	$documents = new CollectionSet(true); 
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc1)), 'Deadpool');
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc2)), 'BigFish');
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc3)), 'American Sniper');
```

You can either use WhitespaceTokenizer, which breaks it down for every space and newline, or WhitespaceAndPunctuationTokenizer, which also tokenizes punctuations.

By default, CollectionSet is set to false, which means label is not needed, even if it's filled in, as it uses its array key as identifier for indexing later instead of the label. So this format is valid as long as it's left at default.

```
	$documents = new CollectionSet(); 
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc1)));
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc2)), 'BigFish');
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc3)), '');
```

When set to true, an exception will be thrown when a label isn't filled.


Transforming Documents
-------------

When indexing documents, different transformations can be done to minimize processing time, and also alleviate the issue of not giving proper weight to documents *'that actually matters'* to an intended query.

Normalizing documents ensures that each tokens are 'canonical' and equivalent to provide better expectation of the result.
The default in Basset, and mostly common, is by transforming all tokens to lowercase.

Transformations had to be done BEFORE indexing.

```
use Basset\Normalizers\English;
use Basset\Utils\TransformationSet;
use Basset\Utils\StopWords;

$stopwords = file_get_contents('../location/for/stopwords.txt');

// Specify Transformations
	$transformations = array(
	                  new English(),
	                  new StopWords($tokenizer->tokenize($stopwords))
	                  );


// Initiate TransformationSet
	$transform = new TransformationSet();
	$transform->register($transformations);

```

Once instantiated, it can be applied to the CollectionSet.

```
 	$documents->applyTransformation($transform);
```

applyTransformation method also acceps single transformations instead of going through the TransformationSet.

```
 	$documents->applyTransformation(new English());
```

Stopwords can also take its own normalization. English is set as default.

```
	new StopWords($tokenizer->tokenize($stopwords), new English());
```


Indexing Documents
-------------

Once the documents are added and transformed, it had to be indexed. Basset has Statistics class that reads an instance of a CollectionSet.

```
use Basset\Statistics\Statistics;

// Index files added above
	$index = new Statistics($documents); 
```

Statistics has methods that counts the ff:

```
// Returns number of occurences of the word in the entire collection.
	$index->termFrequency('fish'); 
```

**termFrequency($term = null)** - the default value for termFrequency, which also gives out an array of all counted terms in the collection.

```
// Returns number of documents containing the word in the entire collection.
	$index->documentFrequency('fish'); 
```

**documentFrequency($term = null)** - the default value for documentFrequency, which also gives out an array of all counted documents containing all word in the collection.

```
	$index->numberofCollectionTokens(); 
```

**numberofCollectionTokens()** - Returns total number of all tokens in the entire collection.

```
	$index->numberofDocuments(); 
```

**numberofDocuments()** - Returns number of documents in the collection.


Feature Extraction
-------------

From Wikipedia, A feature is an attribute or property shared by all of the independent units on which analysis or prediction is to be done. Any attribute could be a feature, as long as it is useful to the model.

A good example for a simple feature is a [feature vector](https://en.wikipedia.org/wiki/Feature_vector) called [tf-idf](https://en.wikipedia.org/wiki/Tf%E2%80%93idf) from each collection.

The tf-idf (tf x idf) weight is a weight often used in information retrieval and text mining. This weight is a statistical measure used to evaluate how important a word is to a document in a collection or corpus.

Term Frequency, which measures how frequently a term occurs in a document. While Inverse Document Frequency is a measure of how important a term is in the whole corpus.

Most common use of a tf-idf vector is with Vector Space Model approaches.

IDF is commonly computed as log(Total number of documents / Number of documents with term t in it).

```
use Basset\FeatureExtraction\TfIdfFeatureExtraction;

// Returns number of occurences of all words in the document.
	$tfidf = new TfIdfFeatureExtraction();
	$tfidf->getFeature($documents[0]);
```


What comes next is mixing all of these together with some Ranking options (plain wrapper for things above) and we'll come up with....


General Use
-------------

```
	$doc1 = Storage::disk('local')->get('test.txt');
	$doc2 = Storage::disk('local')->get('test2.txt');
	$doc3 = Storage::disk('local')->get('test3.txt');

	$stopwords = Storage::disk('local')->get('stopwords.txt');

	$tokenizer = new WhitespaceTokenizer();

/*
 * Specify transformers
 * 
 * You can also create your own language-specific normalizers
 * Extend Normalizer and implement NormalizerInterface
 * 
 * The only stemmer in this lib is regex stemmer as I know there are many implementations elsewhere (Porter1 and 2)
 * Extend Stemmer() and implement StemmerInterface 
 *
 */

	$transformations = array(
	                  new English(),
	                  new StopWords($tokenizer->tokenize($stopwords)),
	                  new WhateverStemmer()
	                  );


	$transform = new TransformationSet();
	$transform->register($transformations);


	$documents = new CollectionSet(true); 
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc1)), 'Deadpool');
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc2)), 'BigFish');
	$documents->addDocument(new TokensDocument($tokenizer->tokenize($doc3)), 'American Sniper');
	$documents->applyTransformation($transform);


/*
 * Query should be wrapped in a DocumentInterface, as we also get its FeatureExtraction (which only receives 
 * DocumentInterface)
 */
	$query = 'bIg FiSh';
	$query_tokenized = new TokensDocument($tokenizer->tokenize($query));
	$query_tokenized->applyTransformation($transform);


/* 
 * Set Ranking Probability relevance models
 * Ranking is just the wrapper for Probabilistic models (apart from DFR which takes in different parameters).
 * Receives ScoringInterface type, CollectionSet, and search() takes in DocumentInterface type.
 */
	$search = new Ranking(new BM25(), $documents);
	$search->search($query_tokenized); // ([BigFish] => 1.11569 [Deadpool] => 0.13361 [American Sniper] => 0.13368)


// alternatively, you can select DFR Models
	$search = new DFRRanking(new In(), new B(), new NormalizationH1(), $documents);
	$search->search($query_tokenized);

// or Algebraic Models
	$search = new VectorSpaceModel(new CosineSimilarity(), $documents);
    $search->setFeature(new PivotTfIdfFeatureExtraction);
    $search->search($query_tokenized);

```



[Ranking Options](https://github.com/jtejido/basset-ir/tree/master/src/Basset/Ranking)
=============

Each Ranking options accepts specific parameters, please read each Classes to read which papers they're derived from and which parameters can be set and values set by default.


Probabilistic Models
-------------

**Usage:**

*Ranking(ScoringInterface model, CollectionSet documentset)*


***Probabilistic Relevance Models***

1. BM25() - Okapi's Best Matching algorithm.


***Ready-made Divergence-From-Randomness Models***

1. BB2() - Bernoulli-Einstein model with Bernoulli after-effect and normalization 2.
2. IFB2() - Inverse Term Frequency model with Bernoulli after-effect and normalization 2.
3. InB2() - Inverse Document Frequency model with Bernoulli after-effect and normalization 2.
4. InL2() - Inverse Document Frequency model with Laplace after-effect and normalization 2.
5. PL2() - Poisson model with Laplace after-effect and normalization 2.
6. XSqrA_M() - Inner product of Pearson's X^2 with the information growth computed with the multinomial M.


***Language Models***

1. HiemstraLM() - Based on Hiemstra's [work](https://pdfs.semanticscholar.org/67ba/b01706d3aada95e383f1296e5f019b869ae6.pdf).
2. DirichletLM() - Bayesian smoothing with Dirichlet Prior.
3. JelinekMercerLM() - Based on the Jelinek-Mercer smoothing method.
4. AbsoluteDiscountingLM() - Absolute Discounting smoothing method.
5. TwoStageLM() - Leave-one-out method. This is also a generalization of both DirichletLM and JelinekMercerLM methods.

***Information-based Models***

1. LLDistribution(1 or 2) - Log-logistic distribution.
2. SPLDistribution(1 or 2) -  Smoothed Power-Law (SPL) distribution.


***Divergence-From-Independence (DFI)***

1. DFI(1 or 2 or 3) - Saturated measure of distance from independence.
2. IRRA12() -  Term weighting model developed on the basis of Shannon’s [Information Theory](https://en.wikipedia.org/wiki/Information_theory). 


[DFR Framework](http://terrier.org/docs/v4.2/dfr_description.html)
-------------

DFR models are obtained by instantiating the three components of the framework: 

1. Selecting a basic randomness model.
2. Applying the first normalisation.
3. Normalising the term frequencies.

**Usage:**

*DFRRanking(BasicModelInterface model, AfterEffectInterface aftereffect, NormalizationInterface normalization, CollectionSet documentset)*


***Models:***

1. P() - Approximation of the binomial.
2. BE() - Bose-Einstein distribution.
3. G() - Geometric approximation of the Bose-Einstein.
4. In() - Inverse Document Frequency model.
5. InFreq() - Inverse Term Frequency model.
6. InExp() - Inverse Expected Document Frequency model.


***After Effect:***

1. L() - Laplace’s law of succession.
2. B() - Ratio of two Bernoulli processes.


***Normalization:***

1. NormalizationH1() - Uniform distribution of the term frequency.
2. NormalizationH2() - The term frequency density is inversely related to the length.


Algebraic Models
-------------

**Usage:**

*VectorSpaceModel(SimilarityInterface type, CollectionSet documentset)*


***Similarity Types***

Basset have the following Similarity(implements SimilarityInterface) for scoring documents in a tf-idf vector.

1. CosineSimilarity() - Classic (D,Q) tf-idf vectors computed thru Cosine similarity.
2. DiceSimilarity() - It was independently developed by the botanists Thorvald Sørensen and Lee Raymond Dice.
3. JaccardIndex() - Also known as Intersection over Union or Jaccard-Tanimoto Coefficient.
3. TverskyIndex() - A Generalization of JaccardIndex and DiceSimilarity. TverskyIndex accepts parameters, so please take a look at the class.
5. Euclidean() - Simple computation using Euclidean Norm.
6. SoftCosineSimilarity() - My experimental attempt to do CosineSimilarity with Levenshtein distance, by [Sidorov Et al.](http://www.cic.ipn.mx/~sidorov/similarity.pdf).


VectorSpaceModel also requires Feature Extraction for building the tf-idf vector.

*setFeature(FeatureExtractionInterface)*

just like our code above:

```
$search = new VectorSpaceModel(new CosineSimilarity(), $documents);
    $search->setFeature(new PivotTfIdfFeatureExtraction);
    $search->search($query_tokenized);
```

The following Features are built-in with Basset.

***Feature Types***

1. TfIdfFeatureExtraction() - Classic (D,Q) tf-idf vectors.
2. PivotTfIdfFeatureExtraction() - Uses cosine normalization that removes a bias inherent in standard length normalization see Singhal Et al. [work](http://singhal.info/pivoted-dln.pdf).
3. LemurTfIdfFeatureExtraction() - Implementation of Robertson's Tf in tf-idf Vector.


Work-In-Progress
-------------

1. Persistent support for indexing.
2. Pipeline.
3. Evaluation tools.
4. Support for PerField Weighting (BM25F, PL2F, etc).
5. Generalized Vector Space Model (TO-DO).
6. Latent Semantic Indexing (TO-DO, not very much focused on, due to PHP's current speed at SVD's complexity).
