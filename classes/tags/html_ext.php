<?php

/**
 * @copyright (c) nainaiwang.com
 * @file tag_base.php
 * @brief Html扩展标签解析类
 * @author weipinglee
 * @date 2017-05-18
 * @version 1.0
 */

namespace tags;
class html_ext extends \tag_base
{


    //对特殊字符标签名转成字母函数
    public static $tagNameParse = array();

        public function _input($str){
            return '<input type="text" />';

        }










}

