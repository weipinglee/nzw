<?php

/**
 * @copyright (c) nainaiwang.com
 * @file tag_base.php
 * @brief 标签解析基类
 * @author weipinglee
 * @date 2017-05-18
 * @version 1.0
 */

 class tag_base
{

     //解析对象数组
      protected static $tagClasses = array(
         'lang_base',
         'html_base',

     );

     public  function addTagClass($array=array()){
         self::$tagClasses = array_merge(self::$tagClasses,$array);
     }
     private static $tagObjArr = array();

     public function init()
     {
         print_r(self::$tagClasses);
        foreach(self::$tagClasses as $item){
            if(!isset(self::$tagObjArr[$item]) ){
                self::$tagObjArr[$item] = new $item();
            }

        }
     }


     /**
      * 解析字符串 匹配的标签包括：$、/英文字母（如：/if)、英文字母(如if、foreach)
      * @param $str string  要解析的字符串
      * @return mixed
      */
     public function translate($str){
        return  preg_replace_callback('/{(\$|\/[a-zA-Z]+|[a-zA-Z]+)\s*(:?)([^}]*)}/i', array($this,'resolve'), $str);

     }

     /**
      * 对正则匹配的数组解析
      * @param $matches
      * @return mixed
      */
     protected function resolve($matches){
         //如果标签类数据为空，进行初始化
         if(empty(self::$tagObjArr)){
             $this->init();
         }
        foreach(self::$tagClasses as $item){
            if(array_key_exists($matches[1],$item::$tagNameParse))//如果匹配的标签在标签类的转换数组中，进行转换标签，比如将$转为dollars
                $matches[1] = $item::$tagNameParse[$matches[1]];
            //如果在该标签类中存在这个标签方法，调用标签类该方法解析
            if(method_exists($item,'_'.$matches[1])){
                $obj = self::$tagObjArr[$item];
               $res = call_user_func_array(array($obj,'_'.$matches[1]),array($matches[3]));
                if(false === $res)//如果返回false,则返回原字符串
                    return $matches[0];
                return $res;
            }

        }
         return $matches[0];//如果匹配不到相应的解析函数，则返回原字符串
     }

    /**
     * @brief 分析标签属性
     * @param string $str
     * @return array以数组的形式返回属性值
     */
    protected function getAttrs($str)
    {
        preg_match_all('/\w+\s*=(?:[^=]+?)(?=(\S+\s*=)|$)/i', trim($str), $attrs);
        $attr = array();
        foreach($attrs[0] as $value)
        {
            $tem = explode('=',$value);
            $attr[trim($tem[0])] = trim($tem[1]);
        }
        return $attr;
    }

     /**
      * @brief 变量替换操作
      * @param string $str
      * @return string
      */
     protected function varReplace($str)
     {
         return preg_replace(array("#(\\$.*?(?=$|\/))#","#(\\$\w+)\[(\w+)\]#"),array("\".$1.\"","$1['$2']"),$str);
     }
}