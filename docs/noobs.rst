Introduction to continuous integration
======================================

If you are new to the concept of continuous integration you should start reading here. If you have some prior experience you might want to skip ahead to the :doc:`full documentation guide <indepth>` or the :doc:`quickstart guide <quickstart>`.

When working on a large project with many components, a change to one component might inadvertently change another component. This is a serious problem especially when several developer work together and might change each other's code in unexpected ways.

Usually each developer works on a separate branch and when the work is done integrates with the master branch. If the task takes a long time, during this time the developer's branch might diverge significantly from the master branch. When it's time to re-integrate the branch there might be many conflicts and unexpected changes which will be hard to fix.

CI methodology strive to solve these problems in two ways:

#. Reintegrating the code to master branch early and often and making sure all developers work on the most recent version of the code which include all the latest changes.

#. Developing a complete testing suite with high coverage - which tests all the components. This minimizes unexpected changes and regressions.

Read more in `Wikipedia - Continuous integration <http://en.wikipedia.org/wiki/Continuous_integration>`_.

Terminology
-----------

* Unit testing
    Automated tests which verify the validity of your code.

* Integration testing
    Automated tests which test the application as a whole, including external dependencies.

* Consistent environment
    All the developers should be able to work on the same configurations and environments.

* Build automation
    To provide a consistent environment - there should be a single command that will automatically build the required environment and configuration.

Dive into Kms-ci-framework
--------------------------

* :doc:`The full documentation guide <indepth>`
* :doc:`The quickstart guide <quickstart>`