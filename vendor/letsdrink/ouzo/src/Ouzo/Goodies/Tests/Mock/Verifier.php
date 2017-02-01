<?php
namespace Ouzo\Tests\Mock;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Joiner;
use PHPUnit_Framework_ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

class Verifier
{
    private $mock;

    public function __construct(SimpleMock $mock)
    {
        $this->mock = $mock;
    }

    public function neverReceived()
    {
        return new NotCalledVerifier($this->mock);
    }

    public function __call($name, $arguments)
    {
        if ($this->_wasCalled($name, $arguments)) {
            return $this;
        }
        $calls = $this->_actualCalls();
        $expected = MethodCall::newInstance($name, $arguments)->__toString();
        $this->_fail("Expected method was not called", $expected, $calls);
    }

    protected function _fail($description, $expected, $actual)
    {
        throw new PHPUnit_Framework_ExpectationFailedException(
            $description,
            new ComparisonFailure($expected, $actual, $expected, $actual)
        );
    }

    protected function _actualCalls()
    {
        if (empty($this->mock->_called_methods)) {
            return "no interactions";
        }
        return Joiner::on(', ')->join(Arrays::map($this->mock->_called_methods, Functions::toString()));
    }

    protected function _wasCalled($name, $arguments)
    {
        return Arrays::find($this->mock->_called_methods, new MethodCallMatcher($name, $arguments));
    }
}
