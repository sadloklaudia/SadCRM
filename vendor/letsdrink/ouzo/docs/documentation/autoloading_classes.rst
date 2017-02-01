Autoloading classes
===================

Ouzo is compliant with `PSR-4`_ specification. By default newly created project derived from ouzo-app will have `PSR-4`_ structure as well.
However, you can use any class loading method: `PSR-4`_, `PSR-0`_, classmap or whatever.

.. _`PSR-4`: http://www.php-fig.org/psr/psr-4/
.. _`PSR-0`: http://www.php-fig.org/psr/psr-0/

.. note::

    There are three types of classes which Ouzo is expecting to be in specific locations:

    * Controllers - under ``\Application\Controller``
    * Models - under ``\Application\Model``
    * Widgets - under ``\Application\Widget``

Changing the defaults
~~~~~~~~~~~~~~~~~~~~~

If you wish to change the defaults, it can be done easily with configuration settings:

::

    $config['namespace']['controller'] = '\\My\\New\\Controller';
    $config['namespace']['model'] = '\\My\\New\\Model';
    $config['namespace']['widget'] = '\\My\\New\\Widget';
