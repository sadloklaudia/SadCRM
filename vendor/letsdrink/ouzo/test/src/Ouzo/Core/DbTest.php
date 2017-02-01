<?php
use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;

class Sample
{
    public function callMethod()
    {
        return 'OK';
    }

    public function exceptionMethod()
    {
        throw new InvalidArgumentException();
    }
}

class DbTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldRunFunctionInTransaction()
    {
        //when
        $result = Db::getInstance()->runInTransaction(array(new Sample(), 'callMethod'));

        //then
        $this->assertEquals('OK', $result);
    }

    /**
     * @test
     */
    public function runInTransactionShouldInvokeBeginAndCommitOnSuccess()
    {
        // given
        $dbHandle = Mock::mock();

        $db = new Db(false);
        $db->_dbHandle = $dbHandle;

        //when
        $result = $db->runInTransaction(array(new Sample(), 'callMethod'));

        //then
        $this->assertEquals('OK', $result);
        Mock::verify($dbHandle)->beginTransaction();
        Mock::verify($dbHandle)->commit();
        Mock::verify($dbHandle)->neverReceived()->rollbackTransaction();
    }

    /**
     * @test
     */
    public function runInTransactionShouldInvokeRollbackOnFailure()
    {
        // given
        $dbHandle = Mock::mock();

        $db = new Db(false);
        $db->_dbHandle = $dbHandle;

        //when
        CatchException::when($db)->runInTransaction(array(new Sample(), 'exceptionMethod'));

        //then
        CatchException::assertThat()->isInstanceOf('InvalidArgumentException');
        Mock::verify($dbHandle)->beginTransaction();
        Mock::verify($dbHandle)->neverReceived()->commitTransaction();
        Mock::verify($dbHandle)->rollBack();
    }
}
