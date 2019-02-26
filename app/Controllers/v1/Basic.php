<?php
namespace App\Controllers\v1;

trait Basic
{

    public static $data = [];

    //输入格式调整
    public static function dataScanf( $data ){
        if( !$data ) $data = [];
        foreach ($data  as $k=>$v){

        }
        return $data;
    }

    // 数组转json，解除中文转换问题
    public static function json_en($array)
    {
        $_urlencode = function (&$str) {
            if ($str !== true && $str !== false && $str !== null) {
                if (stripos($str, '"') !== false || stripos($str, '\\') !== false || stripos($str, '/') !== false ||
                    stripos($str, '\b') !== false || stripos($str, '\f') !== false || stripos($str, '\n') !== false ||
                    stripos($str, '\r') !== false || stripos($str, '\t') !== false) {
                    $newstr = '';
                    for($i=0;$i<strlen($str);$i++){
                        $c = $str[$i];
                        switch ($c) {
                            case '"':
                                $newstr .="\\\"";
                                break;
                            case '\\':
                                $newstr .="\\\\";
                                break;
                            case '/':
                                $newstr .="\\/";
                                break;
                            case '\b':
                                $newstr .="\\b";
                                break;
                            case '\f':
                                $newstr .="\\f";
                                break;
                            case '\n':
                                $newstr .="\\n";
                                break;
                            case '\r':
                                $newstr .="\\r";
                                break;
                            case '\t':
                                $newstr .="\\t";
                                break;
                            default:
                                $newstr .=$c;
                        }
                    }
                    $str = $newstr;
                }
                $str = urlencode($str);
            }
        };
        array_walk_recursive($array, $_urlencode);
        $json = json_encode($array);
        return urldecode($json);
    }

    public static function filter($val,$type='',$de=''){
        $val=Basic::daddslashes($val);   //使用反斜线引用字符串 (对提交数据的过滤)
        //过滤字符
        $filterList = [ ';',':','#','%','select','from','insert','update','delete'];
        $val = str_replace($filterList,'',$val);
        switch ($type) {

            case 'int':
                return intval($val);
                break;

            case 'float':
                return floatval($val);
                break;

            default:
                return htmlspecialchars($val,ENT_QUOTES);   //把预定义的字符转换为 HTML 实体
                break;
        }
    }

    public static function daddslashes($string, $force = 1) {
        if(is_array($string)) {
            $keys = array_keys($string);
            foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = Basic::daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);           //在预定义字符之前添加反斜杠的字符串
        }
        return $string;
    }

}

