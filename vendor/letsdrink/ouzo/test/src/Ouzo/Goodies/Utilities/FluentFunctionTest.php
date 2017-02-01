<?php
use Application\Model\Test\Product;
use Ouzo\Utilities\FluentFunctions;
use Ouzo\Utilities\Functions;

class FluentFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldChainFunctionCalls()
    {
        //given
        $function = FluentFunctions::extractField('name')
            ->removePrefix('super')
            ->prepend(' extra')
            ->append('! ')
            ->surroundWith("***");

        //when
        $result = Functions::call($function, new Product(array('name' => 'super phone')));

        //then
        $this->assertEquals('*** extra phone! ***', $result);
    }

    /**
     * @test
     */
    public function shouldNegate()
    {
        //given
        $function = FluentFunctions::startsWith("start")->negate();

        //when
        $result = Functions::call($function, "starts with prefix");

        //then
        $this->assertFalse($result);
    }
}
