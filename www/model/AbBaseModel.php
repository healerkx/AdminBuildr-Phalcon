<?php
use \Phalcon\Mvc\Model;

class AbBaseModel extends Model
{
    /**
     * @param $search
     * @param bool|false $order
     * @return mixed
     */
    public function search($search, $order=false)
    {
        $clz = get_class();
        $query = new AbBaseQuery($clz);

        $binds = array();
        foreach ($search as $key => $value)
        {
            $condition = $this->getCondition($key, $value);

            $query->addCondition($condition);

            $binds[$key] = $value;
        }

        if (method_exists($this, 'beforeSearch'))
        {
            $this->onSearch($query);
        }

        $params = array('binds' => $binds);
        if ($order) {
            $params['order'] = $order;
        }
        return $query->execute($params);
    }

    public function getCondition($key, $value)
    {
        $searchOnMethod = "searchOn_{$key}";
        if (method_exists($this, $searchOnMethod))
        {
            return $this->$searchOnMethod($key, $value);
        }
        // Default condition is key=value
        return array("{$key}=:{$key}:", array($key => $value));
    }
}