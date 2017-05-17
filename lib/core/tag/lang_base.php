<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17 0017
 * Time: 下午 3:09
 */

class lang_base extends tag_base
{

    //对特殊字符标签名转成字母函数
    public static $tagNameParse = array(
        '$' => 'dollars',
        '/' => ' slash',
        'require' => 'include',
        '/code'   => 'codeend',
        '/if'     => 'slash',
        '/foreach'=> 'slash',
        '/for'    => 'slash',
        '/while'  => 'slash',


    );
    //解析$,输出变量
    public function _dollars($str){
        //$matches = $str;
        $str = trim($str);
        $first = $str[0];
        if($first != '.' && $first != '(')
        {
            if( strpos($str,'(') !== false || (strpos($str,'[') === false && strpos($str,'->') !== false) )
            {
                return '<?php echo $'.$str.';?>';
            }
            else
            {
                return '<?php echo isset($'.$str.')?$'.$str.':"";?>';
            }
        }
        else return false;//返回原字符串

    }

    public function _slash($str){
        return '<?php }?>';
    }

    public function _echo($str){
        return '<?php echo '.rtrim($str,';/').';?>';
    }

    public function _url($str){
        $str = $this->varReplace($str);
		return '<?php echo IUrl::creatUrl("'.$str.'");?>';
    }

    public function _if($str){
        return '<?php if('.$str.'){?>';
    }

    public function _elseif($str){
        return '<?php }elseif('.$str.'){?>';
    }

    public function _else($str){
        return '<?php }else{'.$str.'?>';
    }

    public function _set($str){
        return '<?php '.$str.'?>';
    }



    public function _foreach($str){
        $attr = $this->getAttrs($str);
        if(!isset($attr['items'])) $attr['items'] = '$items';
        else $attr['items'] = $attr['items'];
        if(!isset($attr['key'])) $attr['key'] = '$key';
        else $attr['key'] = $attr['key'];
        if(!isset($attr['item'])) $attr['item'] = '$item';
        else $attr['item'] = $attr['item'];

        return '<?php foreach('.$attr['items'].' as '.$attr['key'].' => '.$attr['item'].'){?>';
    }

    public function _for($str){
        $attr = $this->getAttrs($str);
        if(!isset($attr['item'])) $attr['item'] = '$i';
        else $attr['item'] = $attr['item'];
        if(!isset($attr['from'])) $attr['from'] = 0;

        if(!isset($attr['upto']) && !isset($attr['downto'])) $attr['upto'] = 10;
        if(isset($attr['upto']))
        {
            $op = '<=';
            $end = $attr['upto'];
            if($attr['upto']<$attr['from']) $attr['upto'] = $attr['from'];
            if(!isset($attr['step'])) $attr['step'] = 1;
        }
        else
        {
            $op = '>=';
            $end = $attr['downto'];
            if($attr['downto']>$attr['from'])$attr['downto'] = $attr['from'];
            if(!isset($attr['step'])) $attr['step'] = -1;
        }
        return '<?php for('.$attr['item'].' = '.$attr['from'].' ; '.$attr['item'].$op.$end.' ; '.$attr['item'].' = '.$attr['item'].'+'.$attr['step'].'){?>';

    }

    public function _code($str){
        return '<?php '.$str.'?>';
    }


    public function _while($str){
        return '<?php while('.$str.'){?>';
    }

    public function _query($str){
        $endchart=substr(trim($str),-1);
        $attrs = $this->getAttrs(rtrim($str,'/'));
        if(!isset($attrs['id']))
        {
            $id = '$query';
        }
        else
        {
            $id = $attrs['id'];
        }

        if(!isset($attrs['items']))
        {
            $items = '$items';
        }
        else
        {
            $items = $attrs['items'];
        }
        $tem = "$id".' = new IQuery("'.$attrs['name'].'");';
        //实现属性中符号表达式的问题
        $old_char=array(' eq ',' l ',' g ',' le ',' ge ', ' neq ');
        $new_char=array(' = ' ,' < ',' > ',' <= ',' >= ', ' !=  ');
        foreach($attrs as $k => $v)
        {
            if($k != 'name' && $k != 'id' && $k != 'items' && $k != 'item')
            {
                $v    = preg_replace('%(\$\w+\->\w+\[[\'|\"]\w+[\'|\"]\])%','{$1}',$v);//对变量处理增加花括号
                $tem .= "{$id}->".$k.' = "'.str_replace($old_char,$new_char,$v).'";';
            }
        }
        $tem .= $items.' = '.$id.'->find();';
        if(!isset($attrs['key']))
        {
            $attrs['key'] = '$key';
        }
        else
        {
            $attrs['key'] = $attrs['key'];
        }
        if(!isset($attrs['item']))
        {
            $attrs['item'] = '$item';
        }
        else
        {
            $attrs['item'] = $attrs['item'];
        }
        if($endchart=='/')
        {
            return '<?php '.$tem.'?>';
        }
        else
        {
            return '<?php '.$tem.' foreach('.$items.' as '.$attrs['key'].' => '.$attrs['item'].'){?>';
        }
    }

    public function _include($str){
        $fileName = trim($str,"/ ");
        return '<?php require(ITag::createRuntime("'.$fileName.'"));?>';
    }

    public function _codeend($str){
        return '?>';
    }






}

