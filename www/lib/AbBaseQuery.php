<?php

use Phalcon\Mvc\Model\Criteria;

class AbBaseQuery
{
    private $clz = null;

    /**
     * @var Phalcon\Mvc\Model\Criteria
     */
    private $query = null;

    private $conditionCount = 0;

    public function __construct($clz)
    {
        $this->clz = $clz;

        $this->query = $clz::query();
    }

    public function addCondition($condition)
    {
        if ($this->conditionCount == 0)
        {
            $this->query->where($condition);
        }
        else
        {
            $this->query->addWhere($condition);
        }
        $this->conditionCount += 1;
    }

    public function count()
    {
        $clz = $this->clz;
        return  $clz::count();
    }

    public function addJoin($modelName, $fieldPair) {
        $aliasMd5 = md5($modelName);
        $this->query->join($modelName, "{$aliasMd5}.{$fieldPair[0]} = {$this->clz}.{$fieldPair[1]}", $aliasMd5);
    }

    public function execute($params=array())
    {
        // I have to add this line to fetch all data including joined table.
        $this->query->columns('*');

        if (array_key_exists('binds', $params)) {
            $binds = $params['binds'];
            $this->query->bind($binds);
        }

        if (array_key_exists('order', $params)) {
            $order = $params['order'];
            $this->query->orderBy($order);
        }

        if (array_key_exists('limit', $params)) {
            $limit = $params['limit'];
            $this->query->limit($limit[0], $limit[1]);
        }

        try {

            return $this->query->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}