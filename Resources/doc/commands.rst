Commands
========

The features built with this bundle are managed using some commands.

The provided commands are:

.. code-block:: bash

    $ console features:create [--enabled] [--role ROLE] <parent> <name>
    $ console features:disable <parent> <name>
    $ console features:enable [--role ROLE] <parent> <name>
    $ console features:load [--dry-run] <bundle>

By executing the ``features:load`` command, used features are loaded and stored
directly from the bundle *.twig files inside the ``Resources/views`` directory.
