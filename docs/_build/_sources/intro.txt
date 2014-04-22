Introduction / Executive summary
================================

Continuous integration (CI) is a set of processes and methodologies that has the potential to solve many problems in the typical software development process of large projects incorporating a team of developers.

Introduction to kms-ci-framework
--------------------------------

Kms-ci-framework is made up of two main parts:

#. Documentation
    Description of the methodologies and processes which you can use to implement a continuous integration process for your projects.

#. Tools and code
    A set of tools to help you implement the relevant methodologies and processes defined in the documentation. At the core it provides a single command which will automagically setup your environment to be the same on different machines and will launch the testing suite. You can setup your continuous integration server to run this command on each commit and will run the testing suite.

Features
--------

* Unit testing for PHP using `PHPUnit <http://phpunit.de/>`_.
* Integration testing - support for repeatable and consistent environment configurations accross different machines.
* Perform tasks which require a browser using `PhantomJS <http://phantomjs.org/>`_ - a headless browser with a JS api.
* Integration testing using `CasperJS <http://casperjs.org/>`_ - testing utility built on top of PhantomJS.
* Tested with `Jenkins <http://jenkins-ci.org/>`_ - continuous integration server
* Operating system agnostic - tested on Linux and Windows 7 but should work on other OS as well.

Interested?
-----------

* Want to learn more about the concept of continuous integration?
    :doc:`introduction to continuous integration concepts <noobs>`

* Want to dive into the detailed documentation?
    :doc:`main project documentation <indepth>`.

* tl;dr
    Read the :doc:`quickstart guide <quickstart>`.
