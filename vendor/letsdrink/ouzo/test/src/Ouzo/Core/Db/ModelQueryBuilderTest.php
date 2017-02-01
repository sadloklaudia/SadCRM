<?php
use Application\Model\Test\Category;
use Application\Model\Test\Manufacturer;
use Application\Model\Test\Order;
use Application\Model\Test\OrderProduct;
use Application\Model\Test\Product;
use Ouzo\Db;
use Ouzo\Db\Any;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Relation;
use Ouzo\Db\Stats;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Restrictions;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class ModelQueryBuilderTest extends DbTransactionalTestCase
{
    public function setUp()
    {
        parent::setUp();
        $_SESSION = array();
    }

    /**
     * @test
     */
    public function shouldAcceptStringInWhere()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where('name = ?', 'tech')->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldFetchResultWhenNoParameters()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where('name is not null')->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldAcceptArrayOfColumnValuePairsInWhere()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => 'tech'))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldAcceptArrayOfValuesInWhere()
    {
        //given
        Product::create(array('name' => 'a'));
        Product::create(array('name' => 'b'));

        //when
        $loadedProducts = Product::where(array('name' => array('a', 'b')))->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
    }

    /**
     * @test
     */
    public function shouldIgnoreEmptyArrayForArrayOfValuesInWhere()
    {
        //given
        Product::create(array('name' => 'a'));
        Product::create(array('name' => 'b'));

        //when
        $loadedProducts = Product::where(array('name' => array()))->fetchAll();

        //then
        $this->assertCount(0, $loadedProducts);
    }

    /**
     * @test
     */
    public function shouldAcceptMultipleArraysOfValuesMixedWithSingleValuesInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $loadedProducts = Product::where(array('description' => 'bob', 'name' => array('a', 'b')))->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    /**
     * @test
     */
    public function shouldAcceptRestrictionsMixedWithValuesInWhere()
    {
        //given
        $product = Product::create(array('name' => 'tech', 'description' => 'desc'));

        //when
        $loadedProduct = Product::where(array(
            'name' => Restrictions::equalTo('tech'),
            'description' => 'desc'
        ))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldOrderResults()
    {
        //given
        $product2 = Product::create(array('name' => 'b', 'description' => 'bb'));
        $product1 = Product::create(array('name' => 'a', 'description' => 'aa'));

        //when
        $loadedProducts = Product::where()->order(array('name asc'))->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
        $this->assertEquals($product1, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
    }

    /**
     * @test
     */
    public function shouldOrderResultsByTwoColumns()
    {
        //given
        $product1 = Product::create(array('name' => 'b', 'description' => 'bb'));
        $product2 = Product::create(array('name' => 'a', 'description' => 'aa'));
        $product3 = Product::create(array('name' => 'a', 'description' => 'bb'));

        //when
        $loadedProducts = Product::where()->order(array('name asc', 'description desc'))->fetchAll();

        //then
        $this->assertCount(3, $loadedProducts);
        $this->assertEquals($product3, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
        $this->assertEquals($product1, $loadedProducts[2]);
    }

    /**
     * @test
     */
    public function shouldLimitAndOffsetResults()
    {
        //given
        Product::create(array('name' => 'a'));
        $product = Product::create(array('name' => 'b'));
        Product::create(array('name' => 'c'));

        //when
        $loadedProducts = Product::where()->offset(1)->limit(1)->order('name asc')->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    /**
     * @test
     */
    public function shouldJoinWithOtherTable()
    {
        //given
        Product::create(array('name' => 'other'));
        $product = Product::create(array('name' => 'phones'));

        Order::create(array('name' => 'other'));
        $order = Order::create(array('name' => 'a'));

        OrderProduct::create(array('id_order' => $order->getId(), 'id_product' => $product->getId()));

        //when
        $products = Product::join('orderProduct')->where('id_order IS NOT NULL')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product->getId(), $products[0]->getId());
    }

    /**
     * @test
     */
    public function shouldJoinThroughOtherRelation()
    {
        //given
        $cars = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'Reno', 'id_category' => $cars->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProducts = OrderProduct::join('product->category')
            ->fetchAll();

        //then
        $fetchedProduct = self::getNoLazy($orderProducts[0], 'product');
        $this->assertEquals($product->getId(), $fetchedProduct->getId());
        $this->assertEquals($cars, self::getNoLazy($fetchedProduct, 'category'));
    }

    public static function getNoLazy(Model $model, $attribute)
    {
        return Arrays::getValue($model->attributes(), $attribute);
    }

    /**
     * @test
     */
    public function shouldStoreJoinedModelsInAttributeForMultipleJoins()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'sony', 'description' => 'desc', 'id_category' => $category->getId()));
        $orderProduct = OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $products = Product::join('category')->join('orderProduct')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
        $this->assertEquals($orderProduct, self::getNoLazy($products[0], 'orderProduct'));
    }

    /**
     * @test
     */
    public function shouldStoreJoinedModelInAttribute()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'sony', 'description' => 'desc', 'id_category' => $category->getId()));

        //when
        $products = Product::join('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    /**
     * @test
     */
    public function shouldNotOverrideFetchedModelWhenDuplicatedJoinWithDifferentAlias()
    {
        //given
        $vans = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'Reno', 'id_category' => $vans->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProduct = OrderProduct::join('product->category', array('p1', 'c'))
            ->join('product', 'p2')
            ->where(array(
                'p1.name' => 'Reno',
                'p2.name' => 'Reno',
            ))
            ->fetch();

        //then
        $this->assertEquals($vans, self::getNoLazy(self::getNoLazy($orderProduct, 'product'), 'category'));
    }

    /**
     * @test
     */
    public function shouldNotOverrideFetchedModelWhenThereIsWithForJoinedField()
    {
        //given
        $vans = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'Reno', 'id_category' => $vans->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProduct = OrderProduct::join('product->category')
            ->with('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($vans, self::getNoLazy(self::getNoLazy($orderProduct, 'product'), 'category'));
    }

    /**
     * @test
     */
    public function shouldJoinHasOneRelation()
    {
        //given
        $product = Product::create(array('name' => 'sony'));
        $orderProduct = OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $fetched = Product::join('orderProduct')->fetch();

        //then
        $this->assertEquals($orderProduct, $fetched->orderProduct);
    }

    /**
     * @test
     */
    public function shouldJoinInlineRelation()
    {
        //given
        $product = Product::create(array('name' => 'sony'));
        $orderProduct = OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $fetched = Product::join(Relation::inline(array(
            'destinationField' => 'orderProduct',
            'class' => 'Test\OrderProduct',
            'foreignKey' => 'id_product',
            'localKey' => 'id'
        )))->fetch();

        //then
        $this->assertEquals($orderProduct, self::getNoLazy($fetched, 'orderProduct'));
    }

    /**
     * @test
     */
    public function shouldNotStoreJoinedModelInAttributeIfNotFound()
    {
        //given
        Product::create(array('name' => 'sony', 'description' => 'desc'));

        //when
        $products = Product::join('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertNull($products[0]->category);
    }

    /**
     * @test
     */
    public function shouldInnerJoinWithOtherTable()
    {
        //given
        $category = Category::create(array('name' => 'other'));
        Product::create(array('name' => 'other', 'id_category' => $category->id));
        Product::create(array('name' => 'other'));

        //when
        $products = Product::innerJoin('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    /**
     * @test
     * @group non-sqlite3
     */
    public function shouldRightJoinWithOtherTable()
    {
        //given
        $category = Category::create(array('name' => 'category 1'));
        Category::create(array('name' => 'category 2'));
        Category::create(array('name' => 'category 3'));
        Category::create(array('name' => 'category 4'));
        Product::create(array('name' => 'prod 1', 'id_category' => $category->id));
        Product::create(array('name' => 'prod 2', 'id_category' => $category->id));

        //when
        $products = Product::where()->rightJoin('category')->fetchAll();

        //then
        $this->assertCount(5, $products);
    }

    /**
     * @test
     * @group sqlite3
     */
    public function shouldThrowExceptionRightJoinWithOtherTable()
    {
        //given
        $category = Category::create(array('name' => 'category 1'));
        Category::create(array('name' => 'category 2'));
        Category::create(array('name' => 'category 3'));
        Category::create(array('name' => 'category 4'));
        Product::create(array('name' => 'prod 1', 'id_category' => $category->id));
        Product::create(array('name' => 'prod 2', 'id_category' => $category->id));
        $products = Product::where()->rightJoin('category');

        //when
        CatchException::when($products)->fetchAll();

        //then
        CatchException::assertThat()->isInstanceOf('\BadMethodCallException');
    }

    /**
     * @test
     */
    public function shouldLeftJoinWithOtherTable()
    {
        //given
        $category = Category::create(array('name' => 'category 1'));
        Product::create(array('name' => 'prod 1', 'id_category' => $category->id));
        Product::create(array('name' => 'prod 2', 'id_category' => $category->id));
        Product::create(array('name' => 'prod 3', 'id_category' => null));

        //when
        $products = Product::where()->leftJoin('category')->fetchAll();

        //then
        $this->assertCount(3, $products);
    }

    /**
     * @test
     */
    public function shouldCountRecords()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'aa'));
        Product::create(array('name' => 'b', 'description' => 'bb'));

        //when
        $count = Product::where()->count();

        //then
        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function shouldReturnZeroForCountingQueryThatAlwaysReturnsNothing()
    {
        //when
        $count = Product::where(array('name' => array()))->count();

        //then
        $this->assertEquals(0, $count);
    }

    /**
     * @test
     */
    public function shouldCountRecordsWithJoinWithOtherTable()
    {
        //given
        Product::create(array('name' => 'other'));
        $product = Product::create(array('name' => 'phones'));

        Order::create(array('name' => 'other'));
        $order = Order::create(array('name' => 'a'));

        OrderProduct::create(array('id_order' => $order->getId(), 'id_product' => $product->getId()));

        //when
        $products = Product::join('orderProduct')->where('id_order IS NOT NULL')->count();

        //then
        $this->assertEquals(1, $products);
    }

    /**
     * @test
     */
    public function shouldFetchWithRelationWhenTwoObjectReferenceTheSameForeignKey()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        Product::create(array('name' => 'htc', 'id_category' => $category->getId()));

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
        $this->assertEquals($category, self::getNoLazy($products[1], 'category'));
    }

    /**
     * @test
     */
    public function shouldFetchHasOneRelation()
    {
        //given
        $product = Product::create(array('name' => 'sony'));
        $orderProduct = OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $fetched = Product::where()->with('orderProduct')->fetchAll();

        //then
        $this->assertEquals($orderProduct, self::getNoLazy($fetched[0], 'orderProduct'));
    }

    /**
     * @test
     */
    public function shouldFetchBelongsToRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'sony', 'id_category' => $category->getId()));

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    /**
     * @test
     */
    public function shouldFetchHasManyRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product1 = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        $product2 = Product::create(array('name' => 'samsung', 'id_category' => $category->getId()));

        //when
        $category = Category::where(array('name' => 'phones'))
            ->with('products')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($category, 'products'))->containsOnly($product1, $product2);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfMultipleValuesForHasOne()
    {
        //given
        $product = Product::create(array('name' => 'phones'));

        OrderProduct::create(array('id_product' => $product->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        try {
            Product::where()->with('orderProduct')->fetchAll();
            $this->fail();
        } //then
        catch (DbException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldNotFetchJoinedRelationOnHasMany()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product1 = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        Product::create(array('name' => 'samsung', 'id_category' => $category->getId()));

        //when
        $joined = Category::join('products')->where('products.id = ?', $product1->getId())->fetch();

        //then
        $this->assertNull(Arrays::getValue($joined->attributes(), 'products'));
        $this->assertEquals($category, $joined);
    }

    /**
     * @test
     */
    public function shouldNotFetchRelationJoinedThroughHasMany()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $samsung = Manufacturer::create(array('name' => 'samsung'));
        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $samsung->getId()));
        Product::create(array('name' => 'samsung', 'id_category' => $category->getId()));

        //when
        $joined = Category::join('products->manufacturer')->where('products.id = ?', $product->getId())->fetch();

        //then
        $this->assertNull(Arrays::getValue($joined->attributes(), 'products'));
        $this->assertEquals($category, $joined);
    }

    /**
     * @test
     */
    public function shouldFetchWithRelationWhenObjectHasNoForeignKeyValue()
    {
        //given
        Product::create(array('name' => 'sony'));

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals(null, $products[0]->category);
    }

    /**
     * @test
     */
    public function shouldFetchRelationThroughOtherRelation()
    {
        //given
        $cars = Category::create(array('name' => 'phones'));
        $vans = Category::create(array('name' => 'phones', 'id_parent' => $cars->getId()));

        $product = Product::create(array('name' => 'Reno', 'id_category' => $vans->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProducts = OrderProduct::where()
            ->with('product->category->parent')
            ->fetchAll();

        //then
        $this->assertEquals($product->getId(), $orderProducts[0]->product->getId());
        $this->assertEquals($cars, $orderProducts[0]->product->category->parent);
    }

    /**
     * @test
     */
    public function shouldFetchRelationThroughOneToManyRelation()
    {
        //given
        $phones = Category::create(array('name' => 'phones'));
        $sony = Manufacturer::create(array('name' => 'sony'));
        Product::create(array('name' => 'best ever sony', 'id_category' => $phones->id, 'id_manufacturer' => $sony->id));

        $tablets = Category::create(array('name' => 'tablets'));
        $samsung = Manufacturer::create(array('name' => 'samsung'));
        Product::create(array('name' => 'best ever samsung', 'id_category' => $tablets->id, 'id_manufacturer' => $samsung->id));

        //when
        $categories = Category::where()
            ->with('products->manufacturer')
            ->fetchAll();
        Stats::reset();

        //then
        Assert::thatArray(FluentArray::from($categories)
            ->map(Functions::extract()->products)
            ->flatten()
            ->map(Functions::extract()->manufacturer->name)->toArray())->containsOnly('sony', 'samsung');

        $this->assertEquals(0, Stats::getNumberOfQueries()); //no lazily loaded relations
    }

    /**
     * @test
     */
    public function shouldFetchRelationThroughNullRelation()
    {
        $order = Order::create(array('name' => 'name'));
        OrderProduct::create(array('id_order' => $order->getId()));

        //when
        $orderProducts = OrderProduct::where()
            ->with('product->category')
            ->fetchAll();

        //then
        $this->assertNull($orderProducts[0]->product);
    }

    /**
     * @test
     */
    public function shouldFetchOtherObjectsByArbitraryAttributes()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'description' => 'phones'));

        //when
        $products = Product::where()->with('categoryWithNameByDescription')->fetchAll();

        //then
        $this->assertEquals($category, $products[0]->categoryWithNameByDescription);
    }

    /**
     * @test
     */
    public function shouldNotThrowExceptionIfNoRelationWithForeignKey()
    {
        //given
        Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'description' => 'desc'));

        //when
        $products = Product::where()->with('categoryWithNameByDescription')->fetchAll();

        //then
        $this->assertNull($products[0]->categoryWithNameByDescription);
    }

    /**
     * @test
     */
    public function shouldReturnDistinctResults()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::selectDistinct('description')->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('bob', $result[0][0]);
        $this->assertEquals('john', $result[1][0]);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnAsArray()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::select('name')->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('c', $result[1][0]);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnAsArrayByName()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::select(array('name'), PDO::FETCH_BOTH)->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0]['name']);
        $this->assertEquals('c', $result[1]['name']);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnsAsArrayOfArrays()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::select(array('name', 'description'))->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('bob', $result[0][1]);
        $this->assertEquals('c', $result[1][0]);
        $this->assertEquals('bob', $result[1][1]);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnByStringAsArrayOfArrays()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));

        //when
        $result = Product::select('name')->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(1, $result);
        $this->assertEquals(1, sizeof($result[0]));
        $this->assertEquals('a', $result[0][0]);
    }

    /**
     * @test
     */
    public function shouldNotTryToDeleteIfEmptyInClause()
    {
        //given
        $mockDb = $this->getMock('\Ouzo\Db', array('query'));
        $builder = new ModelQueryBuilder(new Product(), $mockDb);

        $mockDb->expects($this->never())->method('query');

        //when
        $affectedRows = $builder->where(array('name' => array()))->deleteAll();

        //then
        $this->assertEquals(0, $affectedRows);
        //no interaction with db
    }

    /**
     * @test
     */
    public function shouldDeleteRecord()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob'));

        //when
        $product->delete();

        //then
        $allProducts = Product::all();
        $this->assertCount(0, $allProducts);
    }

    /**
     * @test
     * @expectedException \Ouzo\DbException
     */
    public function shouldThrowExceptionOnInvalidQuery()
    {
        Product::select('non existing column')->where()->fetchAll();
    }

    /**
     * @test
     */
    public function shouldAllowChainedWheres()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob1'));
        Product::create(array('name' => 'a', 'description' => 'smith'));
        Product::create(array('name' => 'c', 'description' => 'bob3'));

        //when
        $products = Product::where(array('name' => 'a'))
            ->where('description like(?)', 'bob%')
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldHandleBooleanTrueInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a', 'sale' => true));
        Product::create(array('name' => 'b', 'sale' => false));

        //when
        $products = Product::where(array('sale' => true))->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldHandleBooleanFalseInWhere()
    {
        //given
        Product::create(array('name' => 'a', 'sale' => true));
        $product = Product::create(array('name' => 'b', 'sale' => false));

        //when
        $products = Product::where(array('sale' => false))->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyWheres()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where(array('name' => 'a'))
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyParametersInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where("name = 'a'")
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyStringAsParameterInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => ''));
        Product::create(array('name' => 'c', 'description' => 'a'));

        //when
        $products = Product::where()
            ->where("description = ''")
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    /**
     * @test
     */
    public function shouldAcceptNullParametersInWhere()
    {
        //it will return nothing but we don't want to force users to add null checks

        //given
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where("name = ?", null)
            ->fetchAll();

        //then
        $this->assertEmpty($products);
    }

    /**
     * @test
     */
    public function shouldCloneBuilder()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        $query = Product::where();

        //when
        $query->copy()->where(array('name' => 'other'))->count();

        //then
        $this->assertEquals($product, $query->fetch());
    }

    /**
     * @test
     */
    public function shouldAliasTables()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'id_category' => $category->getId()));

        //when
        $product = Product::alias('p')
            ->join('category', 'c')
            ->where("p.name = 'a' and c.name = 'phones'")
            ->fetch();

        //then
        $this->assertNotNull($product);
        $this->assertEquals($category, self::getNoLazy($product, 'category'));
        Assert::thatArray($product->attributes())->containsKeyAndValue(array(
            'name' => 'a',
            'id_category' => $category->getId()
        ));
    }

    /**
     * @test
     */
    public function shouldAliasTablesInNestedJoin()
    {
        //given
        $cars = Category::create(array('name' => 'cars'));
        $product = Product::create(array('name' => 'Reno', 'id_category' => $cars->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProduct = OrderProduct::alias('op')
            ->join('product->category', array('p', 'c'))
            ->where('op.id_order is null')
            ->where(array(
                'p.name' => 'Reno',
                'c.name' => 'cars'))
            ->fetch();

        //then
        $fetchedProduct = self::getNoLazy($orderProduct, 'product');
        $this->assertEquals($product->getId(), $fetchedProduct->getId());
        $this->assertEquals($cars, self::getNoLazy($fetchedProduct, 'category'));
    }

    /**
     * @test
     */
    public function shouldDoSelfJoinWithConditions()
    {
        //given
        $vehicles = Category::create(array('name' => 'vehicles'));
        $sportCars = Category::create(array('name' => 'sport cars', 'id_parent' => $vehicles->id));
        $bmw = Category::create(array('name' => 'bmw', 'id_parent' => $sportCars->id));

        //when
        $category = Category::alias('c')
            ->join('parent->parent', array('p1', 'p2'))
            ->where(array('c.name' => 'bmw', 'p1.name' => 'sport cars', 'p2.name' => 'vehicles'))
            ->fetch();

        //then
        $this->assertEquals($bmw->id, $category->id);
        $parent = self::getNoLazy($category, 'parent');
        $this->assertEquals($sportCars->id, $parent->id);
        $this->assertEquals($vehicles, self::getNoLazy($parent, 'parent'));
    }

    /**
     * @test
     */
    public function shouldOptimizeDuplicatedJoins()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $manufacturer = Manufacturer::create(array('name' => 'sony'));
        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $manufacturer->id));
        OrderProduct::create(array('id_product' => $product->getId()));

        Stats::reset();

        //when
        $orderProduct = OrderProduct::join('product->category')
            ->join('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($product->id, $orderProduct->product->id);
        $this->assertEquals($category, $orderProduct->product->category);
        $this->assertEquals($manufacturer, $orderProduct->product->manufacturer);
        $this->assertEquals(1, Stats::getNumberOfQueries());
    }

    /**
     * @test
     */
    public function shouldOptimizeDuplicatedRelationFetches()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $manufacturer = Manufacturer::create(array('name' => 'sony'));
        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $manufacturer->id));
        OrderProduct::create(array('id_product' => $product->getId()));

        Stats::reset();

        //when
        $orderProduct = OrderProduct::where()
            ->with('product->category')
            ->with('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($product->id, $orderProduct->product->id);
        $this->assertEquals($category, $orderProduct->product->category);
        $this->assertEquals($manufacturer, $orderProduct->product->manufacturer);
        $this->assertEquals(4, Stats::getNumberOfQueries());
    }

    /**
     * @test
     */
    public function shouldFetchEmptyHasManyRelationSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Category::create(array('name' => 'sony'));

        //when
        $category = Category::where(array('name' => 'sony'))
            ->with('products')
            ->fetch();

        //then
        $this->assertEquals(array(), self::getNoLazy($category, 'products'));
    }

    /**
     * @test
     */
    public function shouldFetchEmptyHasOneRelationSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Product::create(array('name' => 'sony'));
        $product = Product::where(array('products.name' => 'sony'))
            ->with('orderProduct')
            ->fetch();
        Stats::reset();

        //when
        $orderProduct = $product->orderProduct;

        //then
        $this->assertNull($orderProduct);
        $this->assertEquals(0, Stats::getNumberOfQueries());
    }

    /**
     * @test
     */
    public function shouldSetEmptyRelationInJoinSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Product::create(array('name' => 'sony'));

        //when
        $product = Product::where(array('products.name' => 'sony'))
            ->join('category')
            ->fetch();

        //then
        Assert::thatArray($product->attributes())->containsKeyAndValue(array('category' => null));
    }

    /**
     * @test
     */
    public function shouldUpdateModel()
    {
        //given
        $product = Product::create(array('name' => 'bob'));

        //when
        $affectedRows = Product::where(array('name' => 'bob'))->update(array('name' => 'eric'));

        //then
        $this->assertEquals('eric', $product->reload()->name);
        $this->assertEquals(1, $affectedRows);
    }

    /**
     * @test
     */
    public function shouldGroupByCategory()
    {
        //given
        $category1 = Category::create(array('name' => 'computer'));
        $category2 = Category::create(array('name' => 'phone'));
        Product::create(array('name' => 'notebook', 'id_category' => $category1->getId()));
        Product::create(array('name' => 'laptop', 'id_category' => $category1->getId()));
        Product::create(array('name' => 'smartphone', 'id_category' => $category2->getId()));

        //when
        $result = Product::select('id_category, count(*)')->groupBy('id_category')->fetchAll();

        //then
        Assert::thatArray($result)->containsOnly(array($category1->getId(), 2), array($category2->getId(), 1));
    }

    /**
     * @test
     */
    public function shouldGroupByCategoryUsingArray()
    {
        //given
        $category = Category::create(array('name' => 'computer'));
        Product::create(array('name' => 'notebook', 'id_category' => $category->getId()));

        //when
        $result = Product::select('id_category, count(*)')->groupBy(array('id_category'))->fetchAll();

        //then
        Assert::thatArray($result)->containsOnly(array($category->getId(), 1));
    }

    /**
     * @test
     * @expectedException \Ouzo\DbException
     */
    public function shouldThrowExceptionForGroupByWithoutSelect()
    {
        Product::where()->groupBy('id_category');
    }

    /**
     * @test
     */
    public function shouldSelectFunctionShouldSelectOnlySpecifiedColumns()
    {
        //given
        $category1 = Category::create(array('name' => 'category for billy'));
        Product::create(array('name' => 'billy', 'id_category' => $category1->getId()));

        //when
        $names = Category::select(array('categories.name'))
            ->join('product_named_billy')
            ->fetch();

        //then
        Assert::thatArray($names)->containsExactly('category for billy');
    }

    /**
     * @test
     * @group non-sqlite3
     */
    public function shouldAliasTableInUpdateQuery()
    {
        //given
        Category::create(array('name' => 'old'));

        //when
        Category::alias('c')
            ->where(array(
                'c.name' => 'old'
            ))
            ->update(array(
                'name' => "new"
            ));

        //then
        Assert::thatArray(Category::all())->onProperty('name')->containsExactly('new');
    }

    /**
     * @test
     * @group sqlite3
     */
    public function shouldThrowExceptionIfAliasInUpdateQuery()
    {
        //when
        CatchException::when(Category::alias('c')
            ->where(array(
                'c.name' => 'old'
            )))
            ->update(array(
                'name' => "new"
            ));

        //then
        CatchException::assertThat()->isInstanceOf('\InvalidArgumentException');
    }

    /**
     * @test
     */
    public function shouldSearchAnyOf()
    {
        //given
        $category1 = Category::create(array('name' => 'test1'));
        $category2 = Category::create(array('name' => 'test3'));
        $category3 = Category::create(array('name' => 'test with parent', 'id_parent' => $category2->getId()));

        //when
        $categories = Category::where(Any::of(array('name' => array('test1', 'test2'), 'id_parent' => $category2->getId())))
            ->fetchAll();

        //then
        Assert::thatArray($categories)->hasSize(2)
            ->onProperty('name')->containsExactly($category1->name, $category3->name);
    }

    /**
     * @test
     */
    public function shouldSearchAnyOfWithRestrictions()
    {
        //given
        $category1 = Category::create(array('name' => 'test1'));
        $category2 = Category::create(array('name' => 'test2'));
        $category3 = Category::create(array('name' => 'other name'));
        Category::create(array('name' => 'some other name'));

        //when
        $categories = Category::where(Any::of(array('name' => Restrictions::like('tes%'), 'id' => $category3->getId())))
            ->fetchAll();

        //then
        Assert::thatArray($categories)->hasSize(3)
            ->onProperty('name')->containsExactly($category1->name, $category2->name, $category3->name);
    }

    /**
     * @test
     */
    public function shouldSearchAnyOfAndWhereValues()
    {
        //given
        $category = Category::create(array('name' => 'shop'));
        $product1 = Product::create(array('name' => 'notebook', 'description' => 'notebook desc', 'id_category' => $category->getId()));
        $product2 = Product::create(array('name' => 'tablet', 'description' => 'tablet desc', 'id_category' => $category->getId()));
        Product::create(array('name' => 'pc', 'description' => 'pc desc'));

        //when
        $products = Product::where(Any::of(array('name' => 'tablet', 'description' => Restrictions::like('%desc'))))
            ->where(array('id_category' => $category->getId()))
            ->fetchAll();

        //then
        Assert::thatArray($products)->hasSize(2)
            ->onProperty('id')->containsExactly($product1->getId(), $product2->getId());
    }
}
