Path
====

It is a utility designed to simplify path related operations, such as joining or normalizing paths.

----

join
~~~~
Allows you to join all given path parts together using system specific directory separator.
It ignores empty arguments and excessive separators.

**Example:**
::

    echo Path::join('/disk', 'my/dir', 'file.txt');

Result: ``/disk/my/dir/file.txt``

----

joinWithTemp
~~~~~~~~~~~~
Similar to ``Path::join``, but additionaly it adds system specific temporary directory path at the beginning.

**Example:**
::

    echo Path::joinWithTemp('/disk', 'my/dir', 'file.txt');

Result: ``/tmp/disk/my/dir/file.txt``

----

normalize
~~~~~~~~~
It normalizes given path by removing unncessary references to parent directories (i.e. "..") and removing double slashes.

**Example:**
::

    echo Path::normalize('/disk/..//photo.jpg');

Result:
``/photo.jpg``
