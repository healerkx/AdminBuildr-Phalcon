<?php
/**
 * Created by PhpStorm.
 * User: Healer
 * Date: 2014/11/19
 * Time: 9:54
 */

class Strings
{
    public static function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if($length == 0)
        {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    // Only UTF-8
    public static function cutStr( $string, $sublen, $end = '...', $start = 0 )
    {
        if ( strlen( $string ) <= $sublen )
            return $string;

        if ( preg_match( '/^[^\x{2E80}-\x{9FFF}]+$/u', $string ) )
        {
            return self::singleByteCut( $string, $sublen, $end = '...' );
        }

        $string = str_replace( array( '&amp;', '&quot;', '&lt;', '&gt;' ), array( '&', '"', '<', '>' ), $string );

        $pa = "/[\x01-\x7f]{1,2}|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all( $pa, $string, $t_string );

        if( count($t_string[0]) - $start > $sublen )
            $sctString =  join( '', array_slice( $t_string[0], $start, $sublen ) )."{$end}";
        else
            $sctString =  join( '', array_slice( $t_string[0], $start, $sublen ) );

        return str_replace( array( '&', '"', '<', '>' ), array( '&amp;', '&quot;', '&lt;', '&gt;' ), $sctString );
    }

    public static function singleByteCut( $string, $sublen, $end = '...' )
    {
        $length = strlen( $string );
        if ($length > $sublen){
            $sub_string = substr( $string, $start, $sublen );   // ? $start?
            $space_position = strrpos( $sub_string, ' ' );
            if($space_position != FALSE){
                $sub_string = substr( $sub_string, 0, $space_position+1 );
            } else {
                if($space_position = strpos($string,' '))
                    $sub_string = substr($string,0, $space_position+1);
            }
            return $sub_string.$end;
        }else{
            return $string;
        }
    }

    public static function clean4Byte( $string )
    {
        return preg_replace('%(?:\xF0[\x90-\xBF][\x80-\xBF]{2}| [\xF1-\xF3][\x80-\xBF]{3}| \xF4[\x80-\x8F][\x80-\xBF]{2})%xs', '', $string);
    }

    public static function format($str, $arr)
    {
        $values = array_values($arr);
        $keys = array_keys($arr);

        foreach ($keys as $k => $v) {
            $keys[$k] = "/{{\\s*$v\\s*}}/";
        }

        $str = preg_replace($keys, $values, $str);
        return $str;
    }

    public static function tableNameToModelName($tableName) {
        $modelName = preg_replace_callback("/_([a-z])/", function($a) {
            return strtoupper($a[1]);
        }, $tableName);
        $modelName = ucfirst($modelName);
        return $modelName;
    }
}
