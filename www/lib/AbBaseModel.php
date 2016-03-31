<?php
use \Phalcon\Mvc\Model;

/**
 * Class AbBaseModel
 */
class AbBaseModel extends Model
{
    /**
     * @param $search
     * @param $joins
     * @param bool|false $order
     * @return mixed
     */
    public static function search($search, $joins=array(), $order=false)
    {
        $clz = get_called_class();
        $query = new AbBaseQuery($clz);
        $count = $query->count();
        $binds = array();
        $page = 1;
        $pageSize = ApplicationConfig::getDefaultPageSize();
        foreach ($search as $key => $value)
        {
            if ($key == '_url') {
                continue;
            } else if ($key == '__pager_current') {
                $page = $value ? intval($value) : 1;
                continue;
            } else if ($key == '__pager_size') {
                $pageSize = $value ? intval($value) : $pageSize;
                continue;
            }

            $params = array();
            if (is_array($value)) {
                if (array_key_exists('from', $value)) {
                    $params['range'] = '>=';
                    $from = $value['from'];

                    if (!empty($from)) {
                        $binds = array_merge($binds, self::addCondition($query, $key, $from, $params));
                    }
                }

                if (array_key_exists('to', $value) && !empty($value['to'])) {
                    $params['range'] = '<=';
                    $to = $value['to'];

                    if (!empty($to)) {
                        $binds = array_merge($binds, self::addCondition($query, $key, $from, $params));
                    }
                }
            } else {
                if ($clz::isLikeField($key)) {
                    $params['like'] = true;
                }

                if (!empty($value)) {
                    $binds = array_merge($binds, self::addCondition($query, $key, $value, $params));
                }
            }
        }

        if (method_exists($clz, 'beforeSearch'))
        {
            $clz::onSearch($query);
        }

        $params = array('binds' => $binds);
        if ($order) {
            $params['order'] = $order;
        }

        if (!empty($joins) && is_array($joins)) {
            foreach ($joins as $modelName => $fieldPair) {
                $query->addJoin($modelName, $fieldPair);
            }
        }

        $params['limit'] = array($pageSize, ($page - 1) * $pageSize);
        // Get results
        $items = $query->execute($params);
        return array(
            'count' => $count,
            'items' => $items
        );
    }

    /**
     * @param $key
     * @param $value
     * @param array $params
     * @return array
     */
    public static function getCondition($key, $value, $params = array())
    {
        if (array_key_exists('like', $params)) {
            return array("{$key} LIKE :{$key}:", array($key => $value . '%'));
        } elseif (array_key_exists('range', $params)) {
            $op = $params['range'];
            return array("{$key} {$op} :{$key}:", array($key => $value));
        } else {
            return array("{$key}=:{$key}:", array($key => $value));
        }

    }

    private static function addCondition($query, $key, $value, $params) {
        $cb = self::getCondition($key, $value, $params);
        $condition = $cb[0];
        $bind = $cb[1];

        $query->addCondition($condition);
        return $bind;
    }

    public static function getEmptyItem() {
        $clz = get_called_class();
        $headers = $clz::headers();

        $item = array();
        foreach ($headers as $header => $_) {
            $item[$header] = '';
        }
        return $item;
    }

    public static function getItemById($id) {
        $clz = get_called_class();
        $item = $clz::findFirst($id);
        return $item->toArray();
    }
}