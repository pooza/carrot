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
abstract class BSMobileCarrier {
	protected $attributes;
	protected $emoji;
	static private $instances;
	const DEFAULT_CARRIER = 'Docomo';

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray;
		mb_ereg('^BS([[:alpha:]]+)MobileCarrier$', get_class($this), $matches);
		$this->attributes['name'] = $matches[1];

		require_once 'HTML/Emoji.php';
		$this->emoji = HTML_Emoji::getInstance(
			BSString::toLower($this->attributes['name'])
		);
	}

	/**
	 * キャリア名を返す
	 *
	 * @access public
	 * @return string キャリア名
	 */
	public function getName () {
		return $this->attributes['name'];
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
				$instance = BSClassLoader::getInstance()->getObject($name, 'MobileCarrier');
				self::$instances[BSString::underscorize($name)] = $instance;
			}
		}
		return self::$instances[BSString::underscorize($carrier)];
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return BSArray 属性の配列
	 */
	public function getAttributes () {
		return $this->attributes;
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
	abstract public function getGPSAnchorElement (BSHTTPRedirector $url, $label);

	/**
	 * GPS情報を返す
	 *
	 * @access public
	 * @return BSArray GPS情報
	 */
	public function getGPSInfo () {
		$request = BSRequest::getInstance();
		if ($request['lat'] && $request['lon']) {
			return new BSArray(array(
				'lat' => BSGeocodeEntryHandler::dms2deg($request['lat']),
				'lng' => BSGeocodeEntryHandler::dms2deg($request['lon']),
			));
		}
	}

	/**
	 * 絵文字を含んだ文字列を変換する
	 *
	 * @access public
	 * @param mixed $body 対象文字列
	 * @return string 変換後文字列
	 */
	public function convertPictogram ($body) {
		$body = $this->emoji->filter($body, 'input');
		return $this->emoji->convertCarrier($body);
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
		$constants = BSConstantHandler::getInstance();
		return $constants['DECORATION_MAIL_TYPE_' . $this->getName()];
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
