Commands
========

The features built with this bundle are managed using some commands.

The provided commands are:

.. code-block:: bash

    $ console features:create [--enabled] [--role ROLE] <parent> <name>
    $ console features:disable <parent> <name>
    $ console features:enable [--role ROLE] <parent> <name>
    $ console features:load [--dry-run] <path> (<path>)...

The ``features:load`` command accepts a path like ``app/Resources/views/`` or a
bundle like ``AppBundle`` and the command will look inside the
``Resources/views`` directory for *.twig files.
