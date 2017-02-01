<?php
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Arrays;

class MockTestClass
{
    public function method()
    {
        return null;
    }

    public function method2()
    {
        return null;
    }

    public function method3(array &$a)
    {
        return null;
    }
}

class MockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnMockObjectOfTheGivenType()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        //when
        $result = $mock instanceof MockTestClass;

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldStubMethod()
    {
        //given
        $mock = Mock::mock('MockTestClass');
        Mock::when($mock)->method()->thenReturn('result');

        //when
        $result = $mock->method();

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldVerifyMethodCall()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        //when
        $mock->method("arg");


        //then
        Mock::verify($mock)->method("arg");
    }

    /**
     * @test
     */
    public function shouldFailIfMethodWasNotCalled()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        //when
        CatchException::when(Mock::verify($mock))->method();

        //then
        CatchException::assertThat()->isInstanceOf("PHPUnit_Framework_ExpectationFailedException");
    }

    /**
     * @test
     */
    public function shouldVerifyMethodIsNotCalled()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        //when
        $mock->method("arg");
        $mock->method2("arg");

        //then
        Mock::verify($mock)->neverReceived()->method("other");
    }

    /**
     * @test
     */
    public function shouldFailIfUnwantedMethodWasCalled()
    {
        //given
        $mock = Mock::mock('MockTestClass');
        $mock->method(1);

        //when
        try {
            Mock::verify($mock)->neverReceived()->method(1);
            $this->fail('expected failure');
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1) is never called', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldFailIfUnwantedMethodWasCalledWithAnyArguments()
    {
        //given
        $mock = Mock::mock();
        $mock->method(1);

        //when
        try {
            Mock::verify($mock)->neverReceived()->method(Mock::anyArgList());
            $this->fail();
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(any arguments) is never called', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldShowActualInteractions()
    {
        //given
        $mock = Mock::mock('MockTestClass');
        $mock->method(1);
        $mock->method2(1);

        //when
        try {
            Mock::verify($mock)->method(1, 2);
            $this->fail();
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1), method2(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1, 2)', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldHandleArrayAndObjectParametersInActualInteractionsMessage()
    {
        //given
        $mock = Mock::mock();
        $mock->method(1, null, array(1, 2), new MockTestClass());

        //when
        try {
            Mock::verify($mock)->method(1, 2);
            $this->fail();
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1, null, [1, 2], MockTestClass {})', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1, 2)', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldReturnSimpleMockIfNoClassGiven()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result');

        //when
        $result = $mock->method();

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldThrowException()
    {
        //given
        $mock = Mock::mock();
        $exception = new Exception("msg");
        Mock::when($mock)->method()->thenThrow($exception);

        //when
        CatchException::when($mock)->method();

        //then
        CatchException::assertThat()->isEqualTo($exception);
    }

    /**
     * @test
     */
    public function shouldStubMethodWithCallback()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method(Mock::any())->thenAnswer(function (MethodCall $methodCall) {
            return $methodCall->name . ' ' . Arrays::first($methodCall->arguments);
        });

        //when
        $result = $mock->method('arg');

        //then
        $this->assertEquals("method arg", $result);
    }

    /**
     * @test
     */
    public function shouldVerifyThatMethodWasCalledWithAnyArgument()
    {
        //given
        $mock = Mock::mock();

        //when
        $mock->method("arg1", "arg2");


        //then
        Mock::verify($mock)->method(Mock::any(), "arg2");
    }

    /**
     * @test
     */
    public function shouldVerifyThatMethodWasCalledWithAnyArgumentList()
    {
        //given
        $mock = Mock::mock();

        //when
        $mock->method("arg1", "arg2");


        //then
        Mock::verify($mock)->method(Mock::anyArgList());
    }

    /**
     * @test
     */
    public function shouldStubMethodForAnyArgument()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        Mock::when($mock)->method("first", Mock::any())->thenReturn('result');

        //when
        $result = $mock->method("first", "any");

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldStubMethodForAnyArgumentList()
    {
        //given
        $mock = Mock::mock();

        Mock::when($mock)->method(Mock::anyArgList())->thenReturn('result');

        //when
        $result = $mock->method(1, 2);

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNoStubbedMethodMatchesTheCall()
    {
        //given
        $mock = Mock::mock();

        Mock::when($mock)->method(Mock::any(), 1)->thenReturn('result');

        //when
        $result = $mock->method(1, 2);

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldStubMultipleCallsWithDifferentResults()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result1');
        Mock::when($mock)->method()->thenReturn('result2');

        //when
        $result1 = $mock->method();
        $result2 = $mock->method();

        //then
        $this->assertEquals("result1", $result1);
        $this->assertEquals("result2", $result2);
    }

    /**
     * @test
     */
    public function shouldReturnLastResultForMultipleCalls()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result');

        //when
        $result1 = $mock->method();
        $result2 = $mock->method();

        //then
        $this->assertEquals("result", $result1);
        $this->assertEquals("result", $result2);
    }

    /**
     * @test
     */
    public function shouldStubMultipleCallsInOneCallToWhen()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result1', 'result2');

        //when
        $result1 = $mock->method();
        $result2 = $mock->method();

        //then
        $this->assertEquals("result1", $result1);
        $this->assertEquals("result2", $result2);
    }


    /**
     * @test
     */
    public function shouldStubMultipleExceptions()
    {
        //given
        $exception1 = new Exception("msg1");
        $exception2 = new Exception("msg2");
        $mock = Mock::mock();

        Mock::when($mock)->method()->thenThrow($exception1);
        Mock::when($mock)->method()->thenThrow($exception2);

        //when then
        CatchException::when($mock)->method();
        CatchException::assertThat()->isEqualTo($exception1);

        CatchException::when($mock)->method();
        CatchException::assertThat()->isEqualTo($exception2);
    }

    /**
     * @test
     */
    public function shouldStubMultipleExceptionsInOneCallToWhen()
    {
        //given
        $exception1 = new Exception("msg1");
        $exception2 = new Exception("msg2");
        $mock = Mock::mock();

        Mock::when($mock)->method()->thenThrow($exception1, $exception2);

        //when then
        CatchException::when($mock)->method();
        CatchException::assertThat()->isEqualTo($exception1);

        CatchException::when($mock)->method();
        CatchException::assertThat()->isEqualTo($exception2);
    }

    /**
     * @test
     */
    public function shouldStubMultipleCallsWithResultsAndExceptions()
    {
        //given
        $exception = new Exception("msg");
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result');
        Mock::when($mock)->method()->thenThrow($exception);

        //when
        $result = $mock->method();
        CatchException::when($mock)->method();

        //then
        $this->assertEquals("result", $result);
        CatchException::assertThat()->isEqualTo($exception);
    }

    /**
     * @test
     */
    public function shouldStubMethodThatTakesParamByRef()
    {
        //given
        $mock = Mock::mock('MockTestClass');
        $a = array();
        Mock::when($mock)->method3(Mock::anyArgList())->thenReturn('result');

        //when
        $result = $mock->method3($a);

        //then
        $this->assertEquals("result", $result);
        Mock::verify($mock)->method3($a);
    }

    /**
     * @test
     */
    public function shouldStubWithArgumentMatcher()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        Mock::when($mock)->method(Mock::argThat()->startsWith('matching'))->thenReturn('result');

        //when then
        $this->assertEquals("result",  $mock->method("matching arg"));
        $this->assertNull($mock->method("something else"));
    }

    /**
     * @test
     */
    public function shouldVerifyWithArgumentMatcher()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        //when
        $mock->method("matching arg");

        //then
        Mock::verify($mock)->method(Mock::argThat()->startsWith('matching'));
    }

    /**
     * @test
     */
    public function shouldFailVerificationWithArgumentMatcher()
    {
        //given
        $mock = Mock::mock('MockTestClass');

        $mock->method("something else");

        //when
        try {
            Mock::verify($mock)->method(Mock::argThat()->extractField('name')->startsWith('matching'));
            $this->fail('Expected failure');
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method("something else")', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(argThat()->extractField("name")->startsWith("matching"))', $e->getComparisonFailure()->getExpected());
        }
    }
}
