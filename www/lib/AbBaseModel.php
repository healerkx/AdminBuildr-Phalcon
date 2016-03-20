<?php
use \Phalcon\Mvc\Model;

/**
 * Class AbBaseModel
 */
class AbBaseModel extends Model
{
    /**
     * @param $search
     * @param bool|false $order
     * @return mixed
     */
    public static function search($search, $order=false)
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

            if (empty($value)) {
                continue;
            }

            $params = array();
            if (strstr($key, '.') == null) {
                if ($clz::isLikeField($key)) {
                    $params['like'] = true;
                }
            } else {
                if (strstr($key, '.from')) {
                    $params['range'] = '>=';
                } else if (strstr($key, '.to')) {
                    $params['range'] = '<=';
                }
            }

            $cb = self::getCondition($key, $value, $params);

            $condition = $cb[0];
            $bind = $cb[1];

            $query->addCondition($condition);
            $binds = array_merge($binds, $bind);
        }

        if (method_exists($clz, 'beforeSearch'))
        {
            $clz::onSearch($query);
        }

        $params = array('binds' => $binds);
        if ($order) {
            $params['order'] = $order;
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