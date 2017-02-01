Arrays
======

Helper functions that can operate on arrays.

----

all
~~~
Returns true if every element in array satisfies the predicate.

**Parameters:** ``array $elements``, ``$predicate``

**Example:**
::

    $array = [1, 2];
    $all = Arrays::all($array, function ($element) {
        return $element < 3;
    });

**Result:** ``true``

----

.. _Arrays-toMap:

toMap
~~~~~
This method creates associative array using key and value functions on array elements.

**Parameters:** ``array $elements``, ``$keyFunction``, ``$valueFunction = null``

**Example:**
::

    $array = range(1, 2);
    $map = Arrays::toMap($array, function ($elem) {
        return $elem * 10;
    }, function ($elem) {
        return $elem + 1;
    }); 

**Result:**
::

    Array
    (
        [10] => 2
        [20] => 3
    )

.. note::

    If ``$valueFunction`` is not given Functions::identity() is used.

::

    $users = array(new User('bob'), new User('john'));
    $usersByName = Arrays::toMap($users, function ($user) {
        return $user->name;
    }); 

``$usersByName`` will contain associative array with users indexed by their names.

.. note::

    You can Functions::extractField provided by ouzo:

    ``$usersByName = Arrays::toMap($users, Functions::extractField('name'));``

----

flatten
~~~~~~~
Returns a new array that is a one-dimensional flattening of the given array.

**Parameters:** ``array $elements``

**Example:**
::

    $array = [
        'names' => [
            'john',
            'peter',
            'bill'
        ],
        'products' => [
            'cheese',
            ['milk', 'brie']
        ]
    ];
    $flatten = Arrays::flatten($array);

**Result:**
::

    Array
    (
        [0] => john
        [1] => peter
        [2] => bill
        [3] => cheese
        [4] => milk
        [5] => brie
    )

----

findKeyByValue
~~~~~~~~~~~~~~
This method returns a key for the given value.

**Parameters:** ``array $elements``, ``$value``

**Example:**
::

    $array = [
        'k1' => 4,
        'k2' => 'd',
        'k3' => 0,
        9 => 'p'
    ];
    $key = Arrays::findKeyByValue($array, 0);

**Result:** ``k3``

----

any
~~~
Returns true if at least one element in the array satisfies the predicate.

**Parameters:** ``array $elements``, ``$predicate``

**Example:**
::

    $array = ['a', true, 'c'];
    $any = Arrays::any($array, function ($element) {
        return is_bool($element);
    });

**Result:** ``true``

----

first
~~~~~
This method returns the first value in the given array .

**Parameters:** ``array $elements``

**Example:**
::

    $array = ['one', 'two' 'three'];
    $first = Arrays::first($array);

**Result:** ``one``

----

last
~~~~
This method returns the last value in the given array.

**Parameters:** ``array $elements``

**Example:**
::

    $array = ['a', 'b', 'c'];
    $last = Arrays::last($array);

**Result:** ``c``

----

firstOrNull
~~~~~~~~~~~
This method returns the first value or ``null`` if array is empty.

**Parameters:** ``array $elements``

**Example:**
::

    $array = [];
    $return = Arrays::firstOrNull($array);

**Result:** ``null``

----

getValue
~~~~~~~~
Returns the element for the given key or a default value otherwise.

**Parameters:** ``array $elements``, ``$key``, ``$default = null``

**Example:**
::

    $array = ['id' => 1, 'name' => 'john'];
    $value = Arrays::getValue($array, 'name');

**Result:** ``john``

**Example:**
::

    $array = ['id' => 1, 'name' => 'john'];
    $value = Arrays::getValue($array, 'surname', '--not found--');

**Result:** ``--not found--``

----

filterByAllowedKeys
~~~~~~~~~~~~~~~~~~~
Returns an array containing only the given keys.

**Example:**
::

    $array = ['a' => 1, 'b' => 2, 'c' => 3];
    $filtered = Arrays::filterByAllowedKeys($array, ['a', 'b']);

**Result:** 
::

    Array
    (
        [a] => 1
        [b] => 2
    )

----

filterByKeys
~~~~~~~~~~~~
Filters array by keys using the predicate.

**Example:**
::

    $array = ['a1' => 1, 'a2' => 2, 'c' => 3];
    $filtered = Arrays::filterByKeys($array, function ($elem) {
        return $elem[0] == 'a';
    });

**Result:** 
::

    Array
    (
        [a1] => 1
        [b2] => 2
    )

----

.. _Arrays-groupBy:

groupBy
~~~~~~~
Group elements in array using function to grouping elements. If set ``$orderField`` grouped elements will be also sorted.

**Parameters:** ``array $elements``, ``$keyFunction``, ``$orderField = null``

**Example:**
::

    $obj1 = new stdClass();
    $obj1->name = 'a';
    $obj1->description = '1';
    
    $obj2 = new stdClass();
    $obj2->name = 'b';
    $obj2->description = '2';
    
    $obj3 = new stdClass();
    $obj3->name = 'b';
    $obj3->description = '3';
    
    $array = [$obj1, $obj2, $obj3];
    $grouped = Arrays::groupBy($array, Functions::extractField('name'));

**Result:**
::

    Array
    (
        [a] => Array
            (
                [0] => stdClass Object
                    (
                        [name] => a
                        [description] => 1
                    )

            )

        [b] => Array
            (
                [0] => stdClass Object
                    (
                        [name] => b
                        [description] => 2
                    )

                [1] => stdClass Object
                    (
                        [name] => b
                        [description] => 3
                    )

            )

    )

----

orderBy
~~~~~~~
This method sorts elements in array using order field.

**Parameters:** ``array $elements``, ``$orderField``

**Example:**
::

    $obj1 = new stdClass();
    $obj1->name = 'a';
    $obj1->description = '1';

    $obj2 = new stdClass();
    $obj2->name = 'c';
    $obj2->description = '2';

    $obj3 = new stdClass();
    $obj3->name = 'b';
    $obj3->description = '3';

    $array = [$obj1, $obj2, $obj3];
    $sorted = Arrays::orderBy($array, 'name');

**Result:**
::

    Array
    (
        [0] => stdClass Object
            (
                [name] => a
                [description] => 1
            )

        [1] => stdClass Object
            (
                [name] => b
                [description] => 3
            )

        [2] => stdClass Object
            (
                [name] => c
                [description] => 2
            )

    )

----

mapKeys
-------
This method maps array keys using the function. Invokes the function for each key in the array. Creates a new array containing the keys returned by the function.

**Parameters:** ``array $elements``, ``$function``

**Example:**
::

    $array = [
         'k1' => 'v1',
         'k2' => 'v2',
         'k3' => 'v3'
    ];
    $arrayWithNewKeys = Arrays::mapKeys($array, function ($key) {
         return 'new_' . $key;
    });

**Result:**
::

    Array
    (
         [new_k1] => v1
         [new_k2] => v2
         [new_k3] => v3
    )

----

map
~~~
This method maps array values using the function.
Invokes the function for each value in the array.
Creates a new array containing the values returned by the function.

**Parameters:** ``array $elements``, ``$function``

**Example:**
::

    $array = ['k1', 'k2', 'k3'];
    $result = Arrays::map($array, function ($value) {
        return 'new_' . $value;
    });

**Result:**
::

    Array
    (
        [0] => new_k1
        [1] => new_k2
        [2] => new_k3
    )

----

filter
~~~~~~
This method filters array using function. Result contains all elements for which function  returns ``true``
**Parameters:** ``$elements``, ``$function``

**Example:**
::

    $array = [1, 2, 3, 4];
    $result = Arrays::filter($array, function ($value) {
        return $value > 2;
    });

**Result:**
::

    Array
    (
        [2] => 3
        [3] => 4
    )

----

filterNotBlank
~~~~~~~~~~~~~~
This method filter array will remove all values that are blank.

**Parameters:** ``array $elements``

----

toArray
~~~~~~~
Make array from element. Returns the given argument if it's already an array.

**Parameters:** ``$element``

**Example:**
::

    $result = Arrays::toArray('test');

**Result:**
::

    Array
    (
        [0] => test
    )

----

randElement
~~~~~~~~~~~
Returns a random element from the given array.

**Parameters:** ``array $elements``

**Example:**
::

    $array = ['john', 'city', 'small'[;
    $rand = Arrays::randElement($array);

**Result:** *rand element from array*

----

combine
~~~~~~~
Returns a new array with ``$keys`` as array keys and ``$values`` as array values.

**Parameters:** ``array $keys``, ``array $values``

**Example:**
::

    $keys = ['id', 'name', 'surname'];
    $values = [1, 'john', 'smith'];
    $combined = Arrays::combine($keys, $values);

**Result:**
::

    Array
    (
        [id] => 1
        [name] => john
        [surname] => smith
    )

----

keyExists
~~~~~~~~~
Checks is key exists in an array.

**Parameters:** ``array $elements``, ``$key``

**Example:**
::

    $array = ['id' => 1, 'name' => 'john'];
    $return = Arrays::keyExists($array, 'name');

**Result:** ``true``

----

reduce
~~~~~~
Method to reduce an array elements to a string value.

**Parameters:** ``array $elements``, ``callable $function``

----

find
~~~~
Finds first element in array that is matched by function. Returns null if element was not found.

**Parameters:** ``array $elements``, ``callable $function``

----

intersect
~~~~~~~~~
Computes the intersection of arrays.

**Parameters:** ``array $array1``, ``array $array2``

----

setNestedValue
~~~~~~~~~~~~~~
Setting nested value.

**Parameters:** ``array $array``, ``array $keys``, ``$value``

**Example:**
::

    $array = array();
    Arrays::setNestedValue($array, array('1', '2', '3'), 'value');

Result:
::

    Array
    (
         [1] => Array
             (
                 [2] => Array
                     (
                         [3] => value
                     )
             )
    )

----

sort
~~~~
Returns a new array with is sorted using given comparator.
The comparator function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.
To obtain comparator one may use ``Comparator`` class (for instance ``Comparator::natural()`` which yields ordering using comparison operators).

**Parameters:** ``array $array``, ``$comparator``

**Example:**
::

    class Foo
    {
        private $value;

        public function __construct($value)
        {
          $this->value = $value;
        }

        public function getValue()
        {
          return $this->value;
        }
    }
    $values = [new Foo(1), new Foo(3), new Foo(2)];
    $sorted = Arrays::sort($values, Comparator::compareBy('getValue()'));

**Result:**
::

    Array
    (
         [0] =>  class Foo (1) {
                     private $value => int(1)
                 }
         [1] =>  class Foo (1) {
                     private $value => int(2)
                 }
         [2] =>  class Foo (1) {
                     private $value => int(3)
                 }
    )

----

getNestedValue
~~~~~~~~~~~~~~
Return nested value when found, otherwise return null value.

**Parameters:** ``array $array``, ``array $keys``

**Example:**
::

    $array = ['1' => ['2' => ['3' => 'value']]];
    $value = Arrays::getNestedValue($array, ['1', '2', '3']);

**Result:** ``value``

----

removeNestedValue
~~~~~~~~~~~~~~~~~
.. deprecated:: 1.0
Use :ref:`Arrays::removeNestedKey <Arrays-removeNestedKey>` instead.

----

.. _Arrays-removeNestedKey:

removeNestedKey
~~~~~~~~~~~~~~~
Returns array with removed keys even are nested.

**Parameters:** ``array $array``, ``array $keys``

**Example:**
::

    $array = ['1' => ['2' => ['3' => 'value']]];
    Arrays::removeNestedKey($array, array('1', '2'));

Result:
::

    Array
    (
         [1] => Array
             (
             )
    )

----

hasNestedKey
~~~~~~~~~~~~
Check is array has nested keys.

.. note::

    Possibly check array with null values using flag ``Arrays::TREAT_NULL_AS_VALUE``.

**Parameters:** ``array $array``, ``array $keys``, ``$flags = null``

**Example:**
::

    $array = ['1' => ['2' => ['3' => 'value']]];
    $value = Arrays::hasNestedKey($array, ['1', '2', '3']);

**Result:** ``true``

**Example with null values:**
::

    $array = ['1' => ['2' => ['3' => null]]];
    $value = Arrays::hasNestedKey($array, ['1', '2', '3'], Arrays::TREAT_NULL_AS_VALUE);

**Result:** ``true``

----

flattenKeysRecursively
~~~~~~~~~~~~~~~~~~~~~~
Returns maps of the flatten keys with corresponding values.

**Parameters:** ``array $array``

**Example:**
::

    $array = [
         'customer' => [
             'name' => 'Name',
             'phone' => '123456789'
         ],
         'other' => [
             'ids_map' => [
                 '1qaz' => 'qaz',
                 '2wsx' => 'wsx'
             ],
             'first' => [
                 'second' => [
                     'third' => 'some value'
                 ]
             ]
         ]
    ];
    $flatten = Arrays::flattenKeysRecursively($array)

**Result:**
::

    Array
    (
         [customer.name] => Name
         [customer.phone] => 123456789
         [other.ids_map.1qaz] => qaz
         [other.ids_map.2wsx] => wsx
         [other.first.second.third] => some value
    )

----

count
~~~~~
Returns the number of elements for which the predicate returns true.

**Parameters:** ``array $elements``, ``$predicate``

**Example:**
::

    $array = [1, 2, 3];
    $count = Arrays::count($array, function ($element) {
       return $element < 3;
    });

**Result:** ``2``
