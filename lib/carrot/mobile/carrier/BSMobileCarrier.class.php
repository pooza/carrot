<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage mobile.carrier
 */

/**
 * ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMobileCarrier extends BSParameterHolder {
	static private $instances;
	const DEFAULT_CARRIER = 'Docomo';

	/**
	 * @access public
	 */
	public function __construct () {
		mb_ereg('^BS([[:alpha:]]+)MobileCarrier$', get_class($this), $matches);
		$this['name'] = $matches[1];
	}

	/**
	 * キャリア名を返す
	 *
	 * @access public
	 * @return string キャリア名
	 */
	public function getName () {
		return $this['name'];
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $carrier キャリア名
	 * @return BSMobileCarrier インスタンス
	 * @static
	 */
	static public function getInstance ($carrier = self::DEFAULT_CARRIER) {
		if (!self::$instances) {
			self::$instances = new BSArray;
			foreach (self::getNames() as $name) {
				$instance = BSLoader::getInstance()->createObject($name, 'MobileCarrier');
				self::$instances[BSString::underscorize($name)] = $instance;
			}
		}
		return self::$instances[BSString::underscorize($carrier)];
	}

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

	/**
	 * GPS情報を取得するリンクを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象リンク
	 * @param string $label ラベル
	 * @return BSAnchorElement リンク
	 * @abstract
	 */
	abstract public function createGPSAnchorElement (BSHTTPRedirector $url, $label);

	/**
	 * GPS情報を返す
	 *
	 * @access public
	 * @return BSArray GPS情報
	 */
	public function getGPSInfo () {
		$request = BSRequest::getInstance();
		if ($request['lat'] && ($request['lng'] || $request['lon'])) {
			if (BSString::isBlank($request['lon'])) {
				$request['lon'] = $request['lng'];
			}
			return new BSArray([
				'lat' => BSGeocodeEntryHandler::dms2deg($request['lat']),
				'lng' => BSGeocodeEntryHandler::dms2deg($request['lon']),
			]);
		}
	}

	/**
	 * デコメールの形式を返す
	 *
	 * @access public
	 * @return string デコメールの形式
	 */
	public function getDecorationMailType () {
		$constants = new BSConstantHandler('DECORATION_MAIL_TYPE');
		return $constants[$this->getName()];
	}

	/**
	 * 全てのキャリア名を返す
	 *
	 * @access public
	 * @return BSArray キャリア名の配列
	 * @static
	 */
	static public function getNames () {
		return new BSArray([
			'Docomo',
			'Au',
			'SoftBank',
		]);
	}
}

/* vim:set tabstop=4: */
