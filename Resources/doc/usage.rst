Usage
=====

You can use this bundle from your Twig templates:

.. code-block:: twig

    {% feature "feature" from "group" %}
        feature enabled
    {% else %}
        feature disabled {# this will be the output #}
    {% endfeature %}

or from the service directly:

.. code-block:: php

    $featureService = $this->container->get('ae_feature.feature');

    if (!$featureService->isGranted('feature', 'group')) {
        throw new Exception();
    }