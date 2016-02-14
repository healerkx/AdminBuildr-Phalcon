<?php

class SysRegion extends AbBaseModel
{
    public static function provinces()
    {
        $provinces = SysRegion::find('parent_id=0');
        return $provinces->toArray();
    }

    public static function cities($provinceId)
    {
        $cities = SysRegion::find("parent_id=$provinceId");
        return $cities->toArray();
    }

    public static function counties($cityId)
    {
        $counties = SysRegion::find("parent_id=$cityId");
        return $counties->toArray();
    }
}