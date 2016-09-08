<?php
/**
 * @copyright (c) 2011 aircheng.com
 * @file pic.php
 * @brief 图库处理
 * @author chendeshan
 * @date 2010-12-16
 */
class Pic extends IController
{
	public $layout = '';

	function init()
	{

	}
	//规格图片上传
	function uploadFile()
	{
		//上传状态
		$state = false;

		//规格索引值
		$specIndex = IFilter::act(IReq::get('specIndex'));
		if($specIndex === null)
		{
			$message = '没有找到规格索引值';
		}
		else
		{
			//本地上传方式
			if(isset($_FILES['attach']) && $_FILES['attach']['name'][0]!='')
			{
				$photoInfo = $this->upload();
				if($photoInfo['flag']==1)
				{
					$fileName = $photoInfo['dir'].$photoInfo['name'];
					$state = true;
				}
			}

			//远程网络方式
			else if($fileName=IReq::get('outerSrc','post'))
			{
				$state = true;
			}

			//图库选择方式
			else if($fileName=IReq::get('selectPhoto','post'))
			{
				$state = true;
			}
		}

		//根据状态值进行
		if($state == true)
		{
			die("<script type='text/javascript'>parent.art.dialog({id:'addSpecWin'}).iframe.contentWindow.updatePic(".$specIndex.",'".$fileName."');</script>");
		}
		else
		{
			die("<script type='text/javascript'>alert('添加图片失败');window.history.go(-1);</script>");
		}
	}

	//本地上传方式
	function upload()
	{
		//图片上传
		$upObj = new IUpload();

		//目录散列
		$dir = IWeb::$app->config['upload'].'/'.date('Y')."/".date('m')."/".date('d');
		$upObj->setDir($dir);
		$upState = $upObj->execute();

		//实例化
		$obj = new IModel('spec_photo');

		//检查上传状态
		foreach($upState['attach'] as $val)
		{
			if($val['flag']==1)
			{
				$insertData = array(
					'address'     => $val['dir'].$val['name'],
					'name'        => $val['ininame'],
					'create_time' => ITime::getDateTime(),
				);
				$obj->setData($insertData);
				$obj->add();
			}
		}
		if(count($upState['attach'])==1)
			return $upState['attach'][0];
		else
			return $upState['attach'];
	}

	//获取图片列表
	function getPhotoList()
	{
		$obj = new IModel('spec_photo');
		$photoRs = $obj->query();
		echo JSON::encode($photoRs);
	}

	//kindeditor图片上传
	public function upload_json()
	{
		$save_path = $this->app->getBasePath().$this->app->config['upload'].'/';
		$save_url  = IUrl::creatUrl('').$this->app->config['upload'].'/';
		$realpath  = $this->app->getRuntimePath().'_systemjs/editor/php/upload_json.php';
		include($realpath);
	}

	//kindeditor flash多图片上传
	public function file_manager_json()
	{
		$root_path = $this->app->getBasePath().$this->app->config['upload'].'/';
		$root_url  = IUrl::creatUrl('').$this->app->config['upload'].'/';
		$realpath  = $this->app->getRuntimePath().'_systemjs/editor/php/file_manager_json.php';
		include($realpath);
	}

	//生成缩略图
	public function thumb()
	{
		//配置参数
		$mixData = IFile::dirExplodeDecode(IReq::get('img'));

		//http 304缓存
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == md5($mixData))
		{
			header("HTTP/1.1 304 Not Modified");
			exit;
		}

		if($mixData)
		{
			preg_match("#/w/(\d+)#",$mixData,$widthData);
			preg_match("#/h/(\d+)#",$mixData,$heightData);

			//有缩略图的形式
			if(isset($widthData[1]) && isset($heightData[1]))
			{
				$imgSrc = str_replace(array($widthData[0],$heightData[0]),"",$mixData);
				$width  = $widthData[1];
				$height = $heightData[1];
	 			$thumbSrc = Thumb::get($imgSrc,$width,$height);
			}
			//原图形式
			else
			{
				$thumbSrc = $mixData;
			}

			if(!$thumbSrc)
			{
				return;
			}

			$fileExt   = pathinfo($thumbSrc, PATHINFO_EXTENSION);
			$cacheTime = 31104000;
			$passMtime = gmdate('D, d M Y H:i:s',time() + $cacheTime).' GMT';

			header('Pragma: cache');
			header('Cache-Control: max-age='.$cacheTime);
 			header('Expires: '.$passMtime);
 			header('Content-type: image/'.$fileExt);
 			header("Etag: ".md5($mixData));
			readfile($thumbSrc);
		}
	}
}