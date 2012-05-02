<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMobileCarrier extends BSParameterHolder {
	protected $emoji;
	static private $instances;
	const DEFAULT_CARRIER = 'Docomo';

	/**
	 * @access public
	 */
	public function __construct () {
		mb_ereg('^BS([[:alpha:]]+)MobileCarrier$', get_class($this), $matches);
		$this['name'] = $matches[1];

		$name = BSString::toLower($this['name']);
		BSUtility::includeFile('pear/HTML/Emoji');
		BSUtility::includeFile('pear/HTML/Emoji/' . ucfirst($name));
		$class = 'HTML_Emoji_' . ucfirst($name);
		$this->emoji = new $class;
		$this->emoji->_carrier = $name;
		$this->emoji->setConversionRule('kokogiko');
		$this->emoji->useHalfwidthKatakana(true);
		$this->emoji->disableEscaping();
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
			return new BSArray(array(
				'lat' => BSGeocodeEntryHandler::dms2deg($request['lat']),
				'lng' => BSGeocodeEntryHandler::dms2deg($request['lon']),
			));
		}
	}

	/**
	 * 文字列から絵文字を削除する
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return string 変換後文字列
	 */
	public function trimPictogram ($body) {
		$body = $this->emoji->filter($body, 'input');
		return $this->emoji->removeEmoji($body);
	}

	/**
	 * 文字列に絵文字が含まれているか？
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return boolean 絵文字が含まれていればTrue
	 */
	public function isContainPictogram ($body) {
		$body = $this->emoji->filter($body, 'input');
		return $this->emoji->hasEmoji($body);
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
		return new BSArray(array(
			'Docomo',
			'Au',
			'SoftBank',
		));
	}
}

/* vim:set tabstop=4: */
