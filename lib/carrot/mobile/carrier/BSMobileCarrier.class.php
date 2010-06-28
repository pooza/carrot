<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

// MPC由来の定数
define('MPC_FROM_FOMA', 'FOMA');
define('MPC_FROM_EZWEB', 'EZWEB');
define('MPC_FROM_SOFTBANK', 'SOFTBANK');
define('MPC_FROM_OPTION_RAW', 'RAW');
define('MPC_FROM_OPTION_WEB', 'WEB');
define('MPC_FROM_OPTION_IMG', 'IMG');
define('MPC_FROM_CHARSET_SJIS', 'SJIS');
define('MPC_FROM_CHARSET_UTF8', 'UTF-8');
define('MPC_TO_CHARSET_SJIS', 'SJIS');
define('MPC_TO_CHARSET_UTF8', 'UTF-8');

/**
 * 携帯電話キャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileCarrier {
	protected $attributes;
	protected $mpc;
	protected $gpsProvider;
	protected $pictogramDirectory;
	static private $instances;
	const MPC_IMAGE = 'IMG';
	const MPC_RAW = 'RAW';
	const MPC_SMARTTAG = 'SMARTTAG';
	const DEFAULT_CARRIER = 'Docomo';

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray;
		mb_ereg('^BS([[:alpha:]]+)MobileCarrier$', get_class($this), $matches);
		$this->attributes['name'] = $matches[1];
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
				self::$instances[$name] = $instance;
			}
		}

		$carrier = mb_ereg_replace('[^[:alpha:]]', null, BSString::toLower($carrier));
		foreach (self::$instances as $instance) {
			$names = new BSArray;
			$names[] = BSString::toLower($instance->getName());
			$names[] = BSString::toLower($instance->getMPCCode());
			$names->merge($instance->getAlternativeNames());
			$names->uniquize();
			if ($names->isContain($carrier)) {
				return $instance;
			}
		}

		$message = new BSStringFormat('キャリア "%s" が見つかりません。');
		$message[] = $name;
		throw new BSMobileException($message);
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
	 * 絵文字変換器を返す
	 *
	 * BSEncodingRequestFilterの適用前、素のSJIS文字列に対してのみ有効。
	 *
	 * @access public
	 * @return MPC_Common 絵文字変換器
	 */
	public function getMPC () {
		if (!$this->mpc) {
			BSUtility::includeFile('MPC/Carrier/' . BSString::toLower($this->getMPCCode()));
			$class = 'MPC_' . $this->getMPCCode();
			$this->mpc = new $class;
			$this->mpc->setFromCharset('SJIS');
			$this->mpc->setFrom($this->getMPCCode());
			$this->mpc->setStringType(BSMobileCarrier::MPC_RAW);
			$this->mpc->setImagePath(BSFileUtility::getURL('pictogram')->getAttribute('path'));
		}
		return $this->mpc;
	}

	/**
	 * GPSプロバイダを返す
	 *
	 * @access public
	 * @param BSMobileUserAgent $useragent 端末
	 * @return Net_UserAgent_Mobile_GPS_Provider GPSプロバイダ
	 */
	public function getGPSProvider (BSMobileUserAgent $useragent) {
		if (!$this->gpsProvider) {
			require_once 'Net/UserAgent/Mobile/GPS.php';
			$this->gpsProvider = Net_UserAgent_Mobile_GPS::factory($useragent->getPEARDriver());
		}
		return $this->gpsProvider;
	}

	/**
	 * キャリア名の別名を返す
	 *
	 * @access public
	 * @return BSArray 別名の配列
	 */
	public function getAlternativeNames () {
		return new BSArray;
	}

	/**
	 * MPC向けキャリア名を返す
	 *
	 * @access public
	 * @return string キャリア名
	 */
	public function getMPCCode () {
		return BSString::toUpper($this->getName());
	}

	/**
	 * 絵文字ディレクトリの名前を返す
	 *
	 * @access protected
	 * @return string 絵文字ディレクトリの名前
	 */
	protected function getPictogramDirectoryName () {
		$code = $this->getMPCCode();
		return BSString::toLower($code[0]);
	}

	/**
	 * 絵文字ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory 絵文字ディレクトリ
	 */
	public function getPictogramDirectory () {
		if (!$this->pictogramDirectory) {
			try {
				$dir = BSFileUtility::getDirectory('pictogram');
				$this->pictogramDirectory = $dir->getEntry($this->getPictogramDirectoryName());
				if (!$this->pictogramDirectory->isDirectory()) {
					throw new BSMobileException('絵文字ディレクトリが見つかりません。');
				}
				$this->pictogramDirectory->setDefaultSuffix('.gif');
			} catch (BSFileException $e) {
			}
		}
		return $this->pictogramDirectory;
	}

	/**
	 * 絵文字を含んだ文字列を変換する
	 *
	 * @access public
	 * @param mixed $body 対象文字列, 絵文字コード, 絵文字名のいずれか
	 * @param string $format 出力形式
	 *   self::MPC_RAW
	 *   self::MPC_IMAGE
	 *   self::MPC_SMARTTAG
	 * @return string 変換後文字列
	 */
	public function convertPictogram ($body, $format = self::MPC_SMARTTAG) {
		if ($code = BSPictogram::getPictogramCode($body)) {
			$body = BSPictogram::getInstance($code)->getRaw();
		}
		$this->getMPC()->setString($body);
		return $this->getMPC()->convert($this->getMPCCode(), $format);
	}

	/**
	 * 文字列から絵文字を削除する
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return string 変換後文字列
	 */
	public function trimPictogram ($body) {
		$this->getMPC()->setString($body);
		return $this->getMPC()->except();
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
