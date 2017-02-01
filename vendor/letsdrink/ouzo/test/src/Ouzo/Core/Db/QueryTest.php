<?php
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Utilities\Arrays;

class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSelectQuery()
    {
        // when
        $query = Query::select();

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertFalse($query->distinct);
    }

    /**
     * @test
     */
    public function shouldCreateSelectDistinctQuery()
    {
        // when
        $query = Query::selectDistinct();

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertTrue($query->distinct);
    }

    /**
     * @test
     */
    public function shouldCreateSelectCountQuery()
    {
        // when
        $query = Query::count();

        // then
        $this->assertEquals(QueryType::$COUNT, $query->type);
    }

    /**
     * @test
     */
    public function shouldCreateDeleteQuery()
    {
        // when
        $query = Query::delete();

        // then
        $this->assertEquals(QueryType::$DELETE, $query->type);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithWhereOrderLimitOffset()
    {
        // when
        $query = Query::select()->from('table')->where(array('name' => 'bob'))->limit(5)->offset(10)->groupBy('group')->order(array('name asc'));

        // then
        $this->assertEquals('table', $query->table);
        $this->assertEquals(array('name' => 'bob'), $query->whereClauses[0]->where);
        $this->assertEquals(5, $query->limit);
        $this->assertEquals(10, $query->offset);
        $this->assertEquals('group', $query->groupBy);
        $this->assertEquals(array('name asc'), $query->order);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithJoin()
    {
        // when
        $query = Query::select()->join('table', 'id', 'other_id', 'tab');

        // then
        $this->assertCount(1, $query->joinClauses);
        $join = Arrays::first($query->joinClauses);
        $this->assertEquals('id', $join->joinColumn);
        $this->assertEquals('table', $join->joinTable);
        $this->assertEquals('other_id', $join->joinedColumn);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithColumns()
    {
        // when
        $query = Query::select(array('name', 'id'));

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertEquals(array('name', 'id'), $query->selectColumns);
    }
}
