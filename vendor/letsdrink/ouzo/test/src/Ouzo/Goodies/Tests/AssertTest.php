<?php
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;

class Photo
{
    private $_photoName;
    private $_data;

    public function __construct($photoName, $data = '')
    {
        $this->_photoName = $photoName;
        $this->_data = $data;
    }

    public function getPhotoName()
    {
        return $this->_photoName;
    }
}

class PhotoFrame
{
    private $photo;

    public function __construct($photo)
    {
        $this->photo = $photo;
    }
}

class AssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(array('1'))->contains('1');
        Assert::thatArray(array('1', '2'))->contains('1');
        Assert::thatArray(array('1', '2', '3'))->contains('1');
        Assert::thatArray(array('1', '2', '3'))->contains('1', '2');
        Assert::thatArray(array('1', '2', '3'))->contains('1', '2', '3');
        Assert::thatArray(array('1', '2', '3'))->contains('3', '2', '1');
    }

    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElementWithProperty()
    {
        $object = new stdClass();
        $object->prop = 1;

        Assert::thatArray(array($object))->onProperty('prop')->contains(1);
    }

    /**
     * @test
     */
    public function shouldNotContainElementOpProperty()
    {
        $object = new stdClass();
        $object->prop = 2;

        CatchException::when(Assert::thatArray(array($object))->onProperty('prop'))->contains(1);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowException()
    {
        $this->_assertNotContains(array(null), '1');
        $this->_assertNotContains(array('string'), '1');
        $this->_assertNotContains(array(array('1', '2')), '3');
        $this->_assertNotContains(array(array('1', '2')), '1', '3');
        $this->_assertNotContains(array(array('1', '2')), '1', '2', '3');
    }

    /**
     * @test
     */
    public function hasSizeShouldAssertThatArrayHasSpecifiedSize()
    {
        Assert::thatArray(array())->hasSize(0);
        Assert::thatArray(array('1'))->hasSize(1);
        Assert::thatArray(array('1', '2'))->hasSize(2);
    }

    /**
     * @test
     */
    public function hasSizeShouldThrowException()
    {
        $this->_assertNotHasSize(array(), 1);
        $this->_assertNotHasSize(array('1'), 2);
        $this->_assertNotHasSize(array('1', '2'), 0);
    }

    /**
     * @test
     */
    public function isEmptyShouldAssertThatArrayHasNoElements()
    {
        Assert::thatArray(array())->isEmpty();
    }

    /**
     * @test
     */
    public function isEmptyShouldThrowException()
    {
        $this->_assertNotIsEmpty(array('1'));
        $this->_assertNotIsEmpty(array('1', '2'));
    }

    /**
     * @test
     */
    public function isNotEmptyShouldAssertThatArrayHasElements()
    {
        Assert::thatArray(array('1'))->isNotEmpty();
        Assert::thatArray(array('1', '2'))->isNotEmpty();
    }

    /**
     * @test
     */
    public function isNotEmptyShouldThrowException()
    {
        $this->_assertNotIsNotEmpty(array());
    }

    /**
     * @test
     */
    public function containsOnlyShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(array('1'))->containsOnly('1');
        Assert::thatArray(array('1', '2', '3'))->containsOnly('1', '2', '3');
        Assert::thatArray(array('1', '2', '3'))->containsOnly('3', '1', '2');
    }

    /**
     * @test
     */
    public function containsOnlyShouldThrowException()
    {
        $this->_assertNotContainsOnly(array(null), '1');
        $this->_assertNotContainsOnly(array('string'), '1');
        $this->_assertNotContainsOnly(array(array('1', '2')), '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1', '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1', '2', '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1');
        $this->_assertNotContainsOnly(array(array('1', '2', '3')), '1');
        $this->_assertNotContainsOnly(array(array('1', '2', '3')), '1', '2');
    }

    /**
     * @test
     */
    public function containsExactlyShouldAssertThatArrayContainsElementInGivenOrder()
    {
        Assert::thatArray(array('1'))->containsExactly('1');
        Assert::thatArray(array('1', '2', '3'))->containsExactly('1', '2', '3');
    }

    /**
     * @test
     */
    public function containsExactlyShouldThrowException()
    {
        $this->_assertNotContainsExactly(array(null), '1');
        $this->_assertNotContainsExactly(array('string'), '1');
        $this->_assertNotContainsExactly(array(array('1', '2')), '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1', '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1', '2', '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '1');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '1', '2');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '3', '1', '2');
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldAssertThatArrayContainsKeyValues()
    {
        $array = array('id' => 123, 'name' => 'john', 'surname' => 'smith');
        Assert::thatArray($array)->containsKeyAndValue(array('id' => 123, 'name' => 'john'));
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldThrowException()
    {
        $array = array('id' => 123, 'name' => 'john', 'surname' => 'smith');
        $this->_assertNotContainsKeyAndValue($array,
            array('id' => 12)
        );
        $this->_assertNotContainsKeyAndValue($array,
            array('id' => 123, 'name' => 'john', 'surname' => 'smith', 'new_key' => 'new_value')
        );
    }

    /**
     * @test
     */
    public function containsShouldAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        Assert::thatArray($photos)->onMethod('getPhotoName')->containsOnly('photo1', 'photo2');
    }

    /**
     * @test
     */
    public function containsShouldNotAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        CatchException::when(Assert::thatArray($photos)->onMethod('getPhotoName'))->contains('photo3');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldCheckSequences()
    {
        $array = array('ccc', 'aaa', 'bbb', 'ccc', 'ddd');
        Assert::thatArray($array)->containsSequence('ccc', 'ddd');
        Assert::thatArray($array)->containsSequence();
        Assert::thatArray($array)->containsSequence('aaa');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenOrderIsIncorrect()
    {
        $array = array('ccc', 'aaa', 'bbb', 'ccc', 'ddd');
        CatchException::when(Assert::thatArray($array))->containsSequence('ddd', 'ccc');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenIsNotSequence()
    {
        $array = array('ccc', 'aaa', 'bbb', 'ccc', 'ddd');
        CatchException::when(Assert::thatArray($array))->containsSequence('aaa', 'ddd');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenPassTooManyParameters()
    {
        $array = array('ccc', 'aaa', 'bbb', 'ccc', 'ddd');
        CatchException::when(Assert::thatArray($array))->containsSequence('ccc', 'aaa', 'bbb', 'ccc', 'ddd', 'zzz');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function excludeShouldThrowExceptionWhenFoundInArray()
    {
        CatchException::when(Assert::thatArray(array('1', '2', '3', '4')))->exclude(7, 8, 4);
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function excludeShouldCheckExclude()
    {
        Assert::thatArray(array('1', '2', '3', '4'))->exclude(7, 8, 9);
        Assert::thatArray(array('one', 'two', 'three', 'four'))->exclude('eleven');
    }

    /**
     * @test
     */
    public function shouldExtractPropertyRecursively()
    {
        $obj[0] = new stdClass();
        $obj[0]->property1 = new stdClass();
        $obj[0]->property1->name = 'name1';
        $obj[1] = new stdClass();
        $obj[1]->property1 = new stdClass();
        $obj[1]->property1->name = 'name2';

        Assert::thatArray($obj)->onProperty('property1->name')->containsExactly('name1', 'name2');
    }

    /**
     * @test
     */
    public function shouldExtractPrivateProperty()
    {
        $photos = array(new Photo('vacation', 'vvv'), new Photo('portrait', 'ppp'));

        Assert::thatArray($photos)->onProperty('_data')->containsExactly('vvv', 'ppp');
    }

    /**
     * @test
     */
    public function shouldExtractPrivatePropertyRecursively()
    {
        $photos = array(new PhotoFrame(new Photo('vacation', 'vvv')));

        Assert::thatArray($photos)->onProperty('photo->_data')->containsExactly('vvv');
    }

    /**
     * @test
     */
    public function onPropertyFailureShouldShowNiceMessage()
    {
        //given
        $obj[0] = new stdClass();
        $obj[0]->property1 = 'prop1';
        $obj[1] = new stdClass();
        $obj[1]->property1 = 'prop2';

        //when
        CatchException::when(Assert::thatArray($obj)->onProperty('property1'))->contains('prop3');

        //then
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException')
            ->hasMessage('Cannot find expected ["prop3"] in actual ["prop1", "prop2"]' . "\n" . 'Failed asserting that true is false.');
    }

    /**
     * @test
     */
    public function shouldCheckKeysRecursivelyAreEqual()
    {
        //given
        $array = array(
            'customer' => array(
                'name' => 'Name',
                'phone' => '123456789',
            ),
            'other' => array(
                'ids_map' => array(
                    '1qaz' => 'qaz',
                    '2wsx' => 'wsx'
                )
            )
        );

        //then
        $expected = array(
            'customer' => array(
                'name' => 'New name',
                'phone' => '45456456',
            ),
            'other' => array(
                'ids_map' => array(
                    '1qaz' => 'QQQ',
                    '2wsx' => 'EVV'
                )
            )
        );
        Assert::thatArray($array)->hasEqualKeysRecursively($expected);
    }

    private function _assertNot()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $array = array_shift($args);

        call_user_func_array(array(CatchException::when(Assert::thatArray($array)), $method), $args);
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    private function _assertNotContains()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('contains'), func_get_args()));
    }

    private function _assertNotContainsOnly()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('containsOnly'), func_get_args()));
    }

    private function _assertNotContainsExactly()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('containsExactly'), func_get_args()));
    }

    private function _assertNotIsEmpty()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('isEmpty'), func_get_args()));
    }

    private function _assertNotIsNotEmpty()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('isNotEmpty'), func_get_args()));
    }

    private function _assertNotHasSize()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('hasSize'), func_get_args()));
    }

    private function _assertNotContainsKeyAndValue()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('containsKeyAndValue'), func_get_args()));
    }
}
