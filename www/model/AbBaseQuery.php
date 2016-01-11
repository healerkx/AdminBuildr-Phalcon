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

    public function execute($params=array())
    {
        if (array_key_exists('binds', $params)) {
            $binds = $params['binds'];
            $this->query = $this->query->bind($binds);
        }

        if (array_key_exists('order', $params)) {
            $order = $params['order'];
            $this->query = $this->query->orderBy($order);
        }

        return $this->query->execute();
    }
}