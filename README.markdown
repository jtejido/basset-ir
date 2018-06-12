
![Droopy](https://i.imgflip.com/1c38he.jpg)


Basset
=============

Basset is a PHP Information Retrieval library. This is a culmination of developments in the field and ported over for research purposes.

Basset provides different ways of searching through documents in a collection, by applying advanced and experimental IR techniques gathered from different Research studies and Conferences, most notably:

1. [TREC](http://trec.nist.gov/) 

2. [SIGIR](http://sigir.org/)

3. [ECIR](http://irsg.bcs.org/ecir.php)

4. [ACM](https://www.acm.org/)


[![Build Status](https://travis-ci.com/jtejido/basset-ir.svg?branch=master)](https://travis-ci.com/jtejido/basset-ir)


Documentation
=============

You can read about it [here](https://basset-ir.blogspot.com/2018/02/basset-information-retrieval-library-in.html)



Using the Cranfield Collection and the sample.php file
=============

The [Cranfield Collection](http://ir.dcs.gla.ac.uk/resources/test_collections/) has been the pioneer collection in information retrieval to validate a system's effectiveness.

I've included the 1400 abstract Cranfield Collection as an XML file that you can parse into separate files.

The test file at tests/sample.php can be executed right away to do the parsing and do a search for a single test query.
Customize it to your needs if needed.

You can read Cranfield/cranfield-collection/cranqrel for Glassgow's qrels result.

I've also included SMART system's stopword list for standardization (see stopwords/stopwords.txt).