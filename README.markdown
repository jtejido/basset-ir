
![Droopy](https://i.imgflip.com/1c38he.jpg)


[![Latest Stable Version](https://poser.pugx.org/jtejido/basset/v/stable)](https://packagist.org/packages/jtejido/basset)
[![Build Status](https://travis-ci.com/jtejido/basset-ir.svg?branch=master)](https://travis-ci.com/jtejido/basset-ir)
[![License](https://poser.pugx.org/jtejido/basset/license)](https://packagist.org/packages/jtejido/basset)


Basset
=============

Basset is a full-text PHP Information Retrieval library. This is a collection of developments in the field of IR and ported over to PHP for research purposes.

Basset provides different ways of searching through documents in a collection (ad-hoc retrieval), by applying advanced and experimental IR algorithms and/or techniques gathered from different Research studies and Conferences, most notably:

1. [TREC](http://trec.nist.gov/) 

2. [SIGIR](http://sigir.org/)

3. [ECIR](http://irsg.bcs.org/ecir.php)

4. [ACM](https://www.acm.org/)



Documentation
=============

You can read about it [here](https://myth-of-sissyphus.blogspot.com/2018/02/basset-information-retrieval-library-in.html)



Using the Cranfield Collection and the sample.php file
=============

The [Cranfield Collection](http://ir.dcs.gla.ac.uk/resources/test_collections/) has been the pioneer collection in information retrieval to validate a system's effectiveness.

I've included the 1400 abstract Cranfield Collection as an XML file that you can parse into separate files.

The test file at tests/sample.php can be executed right away to do the parsing and do a search for a single test query.
Customize it to your needs if needed.

You can read Cranfield/cranfield-collection/cranqrel for Glassgow's qrels result.

I've also included SMART system's stopword list for standardization (see stopwords/stopwords.txt).