<?php
/**
 * @copyright Copyright(c) 2010 aircheng.com
 * @file tag_class.php
 * @brief 标签解析类文件
 * @author webning
 * @date 2010-12-17
 * @version 0.6
 */
 /**
  * @brief ITag 系统标签处理文件
  * @class ITag
  */

class ITag
{
    /**
     * @brief  解析给定的字符串
     * @param string $str 要解析的字符串
     * @return String 解析处理的字符串
     */
	public function resolve($str)
	{
		$tagObj  = new tag_base();
		return $tagObj->translate($str);
	}




	/**
	 * @brief 根据模板文件生成可以执行的PHP脚本
	 * @param string $fileParam 文件参数
	 * @param boolean $isLayout 模板是否带有布局
	 */
	public static function createRuntime($fileParam,$isLayout = false)
	{
		$pathInfo = explode("/",$fileParam);
		switch(count($pathInfo))
		{
			case 1:
			{
				$ctrlId   = IWeb::$app->getController()->getId();
				$actionId = $pathInfo[0];
			}
			break;

			case 2:
			{
				$ctrlId   = $pathInfo[0];
				$actionId = $pathInfo[1];
			}
			break;

			default:
			{
				throw new IException("模板标签 【include】或【require】 参数路径不规范");
			}
		}

		$ctrlObj = IWeb::$app->createController($ctrlId);
		if($isLayout == false)
		{
			$ctrlObj->layout = "";
		}
		$actionObj = new IViewAction($ctrlObj,$actionId);
		$viewFile  = $actionObj->resolveView();
		if(is_file($viewFile.$ctrlObj->extend))
		{
			return $ctrlObj->render($viewFile,null,true);
		}
		else
		{
			throw new IException("模板标签 【include】或【require】 路径 {$fileParam} 不存在");
		}
	}
}