<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * ジオコードエントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGeocodeEntry extends BSParameterHolder {
	private $stations;

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($params = []) {
		$this->setParameters($params);
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		if ($name == 'lon') {
			$name = 'lng';
		}
		parent::setParameter($name, $value);
	}

	/**
	 * 書式化して返す
	 *
	 * @access public
	 * @param string $separator 区切り文字
	 * @return string 書式化した文字列
	 */
	public function format ($separator = ',') {
		return $this['lat'] . $separator . $this['lng'];
	}

	/**
	 * script要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement
	 */
	public function createElement (BSParameterHolder $params) {
		$params = BSArray::create($params);
		$container = new BSDivisionElement;
		$inner = $container->addElement(new BSDivisionElement);
		$script = $container->addElement(new BSScriptElement);

		if (BSString::isBlank($id = $params['container_id'])) {
			$id = 'map_' . BSCrypt::digest($params['address']);
		}
		$inner->setID($id);
		$inner->setStyle('width', $params['width']);
		$inner->setStyle('height', $params['height']);
		$inner->setBody('Loading...');

		$serializer = new BSJSONSerializer;
		$statement = new BSStringFormat('CarrotMapsLib.handleMap($(%s), %f, %f, %d);');
		$statement[] = $serializer->encode($inner->getID());
		$statement[] = $this['lat'];
		$statement[] = $this['lng'];
		$statement[] = $params['zoom'];
		$script->setBody($statement);

		if ($params['align']) {
			$container->setStyle('width', $params['width']);
			$container = $container->setAlignment($params['align']);
		}
		return $container;
	}

	/**
	 * 最寄り駅を返す
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   BSHeartRailsExpressService::FORCE_QUERY 新規取得を強制
	 * @return BSArray 最寄り駅
	 */
	public function getStations ($flags = null) {
		if (!$this->stations) {
			$this->stations = new BSArray;
			try {
				$service = new BSHeartRailsExpressService;
				$this->stations->setParameters($service->getStations($this, $flags));
			} catch (Exception $e) {
			}
		}
		return $this->stations;
	}
}

/* vim:set tabstop=4: */
