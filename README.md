Asset release management
===========

maintains a live for domains
routes requests

release folders should be named with concatenated zero-padded compenent date parts, in descending significant order — ISO 8601 like. For example in the 12 digit format YYYYMMDDHHMM. For example, a release folder containing assets scheduled for going live at 9am on Christmas day you'd use the directory name 201412250900.

anything in a release folder will be mirrored to the live directory, overwriting any previous versions of the file and then the release folder will be moved to the applied releases directory. This action is can be undoable.

This can be automated using the Scheduler class to run the process as required.

Developed a custom Twig Loader to serve template files via the Release Manager
