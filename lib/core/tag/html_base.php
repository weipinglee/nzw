<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17 0017
 * Time: ���� 3:09
 */

class html_base extends tag_base
{

    //�������ַ���ǩ��ת����ĸ����
    public static $tagNameParse = array(


    );

    public function _theme($str){
        return '<?php echo $this->getWebViewPath()."'.$str.'";?>';
    }

    public function _skin($str){
        return '<?php echo $this->getWebSkinPath()."'.$str.'";?>';
    }

    public function _webroot($str){
        $str = $this->varReplace($str);
        return '<?php echo IUrl::creatUrl("")."'.$str.'";?>';
    }

    public function _js($str){
        return IJSPackage::load($str);
    }

    public function _url($str){
        $str = $this->varReplace($str);
        return '<?php echo IUrl::creatUrl("'.$str.'");?>';
    }






}

