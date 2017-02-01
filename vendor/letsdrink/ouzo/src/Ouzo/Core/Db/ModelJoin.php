<?php
namespace Ouzo\Db;

use Ouzo\Model;

class ModelJoin
{
    /**
     * @var Relation
     */
    private $relation;
    private $alias;
    private $destinationField;
    private $fromTable;
    private $type;
    private $on;
    private $fetch;

    public function __construct($destinationField, $fromTable, $relation, $alias, $type, $on, $fetch)
    {
        $this->relation = $relation;
        $this->alias = $alias;
        $this->destinationField = $destinationField;
        $this->fromTable = $fromTable;
        $this->type = $type;
        $this->on = $on;
        $this->fetch = $fetch;
    }

    public function storeField()
    {
        return $this->fetch &&  $this->destinationField();
    }

    public function destinationField()
    {
        return $this->destinationField;
    }

    public function alias()
    {
        return $this->alias ? : $this->relation->getRelationModelObject()->getTableName();
    }

    /**
     * @return Model
     */
    public function getModelObject()
    {
        return $this->relation->getRelationModelObject();
    }

    public function asJoinClause()
    {
        $joinedModel = $this->relation->getRelationModelObject();
        $joinTable = $joinedModel->getTableName();
        $joinKey = $this->relation->getForeignKey();
        $idName = $this->relation->getLocalKey();
        $onClauses = array(new WhereClause($this->on, array()), $this->relation->getCondition());
        return new JoinClause($joinTable, $joinKey, $idName, $this->fromTable, $this->alias, $this->type, $onClauses);
    }

    public function equals(ModelJoin $other)
    {
        return
            $this->relation === $other->relation &&
            $this->alias === $other->alias &&
            $this->destinationField === $other->destinationField &&
            $this->fromTable === $other->fromTable &&
            $this->type === $other->type &&
            $this->on === $other->on;
    }

    public static function equalsPredicate($other)
    {
        return function ($modelJoin) use ($other) {
            return $modelJoin->equals($other);
        };
    }
}
