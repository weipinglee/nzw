<?php
/**
 * @brief the TaoBao data packet dispose
 * @data 2013-8-30 15:32:44
 * @author nswe
 */
class taoBaoPacketHelper extends packetHelper
{
	//csv separator
	protected $separator = "	";

	//SKU cache
	private $skuCache = array();

	/**
	 * override abstract function
	 * @return array
	 */
	public function getDataTitle()
	{
		return array('宝贝名称','宝贝价格','宝贝数量','宝贝描述','新图片','宝贝属性','销售属性组合','物流重量','宝贝类目','自定义属性值','用户输入ID串','用户输入名-值对','销售属性别名');
	}
	/**
	 * override abstruact function
	 * @return array
	 */
	public function getTitleCallback()
	{
		return array();
	}
	/**
	 * column callback function
	 * @param string $content data content
	 * @return string
	 */
	protected function newImageCallback($content)
	{
		$record    = array();
		$content   = explode(';',trim($content,'"'));

		if(!$content)
		{
			return '';
		}

		$return  = array();
		foreach($content as $key => $val)
		{
			if($val)
			{
				$imageName = current(explode(':',$val));

				if(in_array($imageName,$record))
				{
					continue;
				}
				$record[] = $imageName;

				if(stripos($imageName,'http://') === 0)
				{
					$imageMd5 = md5($imageName);
					file_put_contents($this->sourceImagePath .'/'. $imageMd5.'.tbi',file_get_contents($imageName));
					$imageName = $imageMd5;
				}
				$source = $this->sourceImagePath .'/'. $imageName.'.tbi';
				if(!is_file($source))
				{
					$source = $this->sourceImagePath .'/'. $imageName.'.jpg';
				}
				$target = $this->targetImagePath .'/'. $imageName.'.jpg';
				$return[$source] = $target;
			}
		}
		return $return;
	}

	/**
	 * @brief 获取自定义SKU
	 * @param int 宝贝类目
	 * @param int 属性ID
	 * @return object
	 */
	public function attrAPI($cat_id,$pid)
	{
		require_once(dirname(__FILE__)."/taobaoApi/TopSdk.php");
		$c = new TopClient;
		$c->appkey    = '23268117';
		$c->secretKey = '6a46a106c6067ea98fd50c29d1eee205';
		$req = new ItempropsGetRequest;
		$req->setFields("pid,name");
		$req->setCid($cat_id);
		$req->setPid($pid);
		return $c->execute($req);
	}

	/**
	 * @brief 获取标准淘宝SKU
	 * @param int 宝贝类目
	 * @param string sku串
	 * @return object
	 */
	public function propAPI($cat_id,$pvs)
	{
		require_once(dirname(__FILE__)."/taobaoApi/TopSdk.php");
		$c = new TopClient;
		$c->appkey    = '23268117';
		$c->secretKey = '6a46a106c6067ea98fd50c29d1eee205';
		$req = new ItempropvaluesGetRequest;
		$req->setFields("pid,prop_name,vid,name");
		$req->setCid($cat_id);
		$req->setPvs($pvs);
		return $c->execute($req);
	}

	//整合采集信息
	public function collect()
	{
		$result = parent::collect();
		foreach($result as $goodsKey => $goodsRow)
		{
			//自定义的属性值
			$customPropData = array();
			if($goodsRow['自定义属性值'])
			{
				$customPropData[] = $goodsRow['自定义属性值'];
			}

			if($goodsRow['销售属性别名'])
			{
				$customPropData[] = $goodsRow['销售属性别名'];
			}

			//替换自定义属性,CVS中已经存在值，只需要解析prop_id名字即可
			if($customPropData)
			{
				foreach($customPropData as $customData)
				{
					$apiAttr    = array();
					$customAttr = explode(';',trim($customData,";"));
					foreach($customAttr as $cval)
					{
						$cArray = explode(":",$cval);
						$from   = $cArray[0].":".$cArray[1];
						$to     = $cArray[0].":".$cArray[2];

						//自定义属性每个商品都是不同的，需要更新
						if(isset($this->skuCache[$from]))
						{
							list($prop_name,$old_prop_val) = explode(":",$this->skuCache[$from]);
							$this->skuCache[$from] = $prop_name.":".$cArray[2];
						}
						else
						{
							$apiAttr[$from] = $to;
						}
					}

					//是否调用淘宝API接口获取属性名字
					if($apiAttr)
					{
						foreach($apiAttr as $attrFrom => $attrTo)
						{
							$attrId    = current(explode(":",$attrTo));
							$apiResult = $this->attrAPI($goodsRow['宝贝类目'],$attrId);
							$this->skuCache[$attrFrom] = str_replace($attrId,$apiResult->item_props->item_prop->name,$attrTo);
						}
					}
				}
			}

			//SKU规格数据
			if($goodsRow['宝贝属性'])
			{
				$apiAttr   = array();
				$attrArray = explode(";",trim($goodsRow['宝贝属性'],";"));
				foreach($attrArray as $aKey => $aValue)
				{
					if(!isset($this->skuCache[$aValue]))
					{
						$apiAttr[] = $aValue;
					}
				}

				//是否调用淘宝API接口获取SKU数据,键值对形式prop_id => prop_value
				if($apiAttr)
				{
					$apiResult = $this->propAPI($goodsRow['宝贝类目'],join(";",$apiAttr));
					if($apiResult->prop_values->prop_value)
					{
						foreach($apiResult->prop_values->prop_value as $propValue)
						{
							$from = $propValue->pid.":".$propValue->vid;
							$to   = $propValue->prop_name.":".$propValue->name;
							$this->skuCache[$from] = $to;
						}
					}
				}
			}

			//SKU编码解码
			if($this->skuCache)
			{
				$result[$goodsKey]['宝贝属性'] = strtr($result[$goodsKey]['宝贝属性'],$this->skuCache);
			}

			//用户自定义ID串和键值对
			if($goodsRow['用户输入ID串'] && $goodsRow['用户输入名-值对'])
			{
				$customName  = explode(",",trim($goodsRow['用户输入ID串'],";"));
				$customValue = explode(",",trim($goodsRow['用户输入名-值对'],";"));
				foreach($customName as $cIndex => $cvalue)
				{
					$apiResult = $this->attrAPI($goodsRow['宝贝类目'],$cvalue);
					$replace   = $apiResult->item_props->item_prop->name.":".$customValue[$cIndex];
					$result[$goodsKey]['宝贝属性'] = preg_replace("|{$cvalue}:[\-\d]+|",$replace,$result[$goodsKey]['宝贝属性']);
				}
			}

			//处理图片包括主图和规格图片
			$mainPic = array();
			$specPic = array();
			if(isset($goodsRow['新图片']))
			{
				//原CVS里面的图片信息结构
				$picData = explode(";",trim($goodsRow['新图片'],";"));

				//生成图片路径下载关系
				$result[$goodsKey]['新图片'] = $this->newImageCallback($goodsRow['新图片']);

				foreach($picData as $picKey => $picVal)
				{
					$picMd5 = current(explode(":",$picVal));
					$picPath= current($result[$goodsKey]['新图片']);

					//判断图片是否是规格数据
					preg_match("/(?<=:)\d+:[\-\d]+(?=\|)/",$picVal,$picMatch);
					if($picMatch)
					{
						$specPic[$picMatch[0]] = $picPath;
					}
					else
					{
						$mainPic[] = $picPath;
					}

					//同步更新下一张图片
					next($result[$goodsKey]['新图片']);
				}
			}

			//处理货品规格数据
			if($goodsRow['销售属性组合'])
			{
				//去掉可能的货品货号
				$customValue = explode(",",trim($goodsRow['用户输入名-值对'],";"));
				if($customValue)
				{
					foreach($customValue as $v)
					{
						$goodsRow['销售属性组合'] = str_replace(":{$v}:","::",$goodsRow['销售属性组合']);
					}
				}

				//生成货品数据信息products
				$product = array();
				preg_match_all("|\d+:[\-\d]+|",$goodsRow['销售属性组合'],$specData);//对sku格式数据进行拆分

				foreach($specData[0] as $key => $val)
				{
					//被拆分的数据属于规格
					if(isset($this->skuCache[$val]))
					{
						list($specName,$specValue) = explode(":",$this->skuCache[$val]);
						list($skuId,$skuVal)       = explode(":",$val);
						$index                     = count($product) - 1;
						$specRow                   = array(
							"id"    => $skuId,
							"type"  => "1",
							"value" => $specValue,
							"name"  => $specName,
						);

						if($specPic && isset($specPic[$val]))
						{
							$specRow['type'] = "2";
							$specRow['value']= $specPic[$val];
						}
						$product[$index]['spec_array'][] = $specRow;
					}
					//价格和数量
					else
					{
						list($price,$store) = explode(":",$val);
						$product[] = array(
							'sell_price'   => $price,
							'store_nums'   => $store,
							'spec_array'   => array(),
						);
					}
				}
				//赋值货品
				$result[$goodsKey]['products'] = $product;
			}

			//赋值商品主图
			$result[$goodsKey]['mainPic']  = $mainPic;
		}
		return $result;
	}
}
/**
 * @brief taobao title to iwebshop cols mapping
 * @date 2013-9-7 12:22:11
 * @author nswe
 */
class taoBaoTitleToColsMapping
{
	/**
	 * taobao title to iwebshop cols mapping
	 */
	public static $mapping = array(
		'name'       => '宝贝名称',
		'sell_price' => '宝贝价格',
		'store_nums' => '宝贝数量',
		'content'    => '宝贝描述',
		'img'        => '新图片',
		'attr'       => '宝贝属性',
		'spec'       => '销售属性组合',
		'weight'     => '物流重量',
	);
}