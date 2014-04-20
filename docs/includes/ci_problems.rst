Continuous integration is a process which has the potential to solve many problems in the typical software development process of large projects:

* Stepping on each other's toes - 
    Each developer in a team usually works on a separate component which might affect other component in unexpected ways. Continuous integration process can test the code on every commit and this makes sure the changes a developer made to a certain component do no affect other components in the code base.

* A good testing suite takes a long time to run - 
    Using a continuous integration system the testing suite is run on a dedicated server in the background while the developer can continue developement. The continuous integration server can be configured to send alerts if a build has failed.

* "It works on my machine" - 
    This is a common response when someone complains about a bug which you can't reproduce. Usually the cause for this type of bugs is differente configurations / versions. Using a continuous integration system you can make sure all developers work on exactly the same configuration and environment. Also, you can test with different environments and configurations to detect these type of bugs early.

Read more about continuous integration in `wikipedia <http://en.wikipedia.org/wiki/Continuous_integration>`_.
