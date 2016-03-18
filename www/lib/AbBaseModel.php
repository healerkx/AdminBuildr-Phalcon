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
                $page = $value ? intval($value): 1;
                continue;
            } else if ($key == '__pager_size') {
                $pageSize = $value ? intval($value) : $pageSize;
                continue;
            }

            if (empty($value)) {
                continue;
            }

            $useLike = false; // TODO:
            $cb = self::getCondition($key, $value, $useLike);
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

    public static function getCondition($key, $value, $like = false)
    {
        if (!$like)
        {
            return array("{$key}=:{$key}:", array($key => $value));
        }
        else
        {
            return array("{$key} LIKE :{$key}:", array($key => $value . '%'));
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