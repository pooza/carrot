<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

BSUtility::includeFile('mpc/MobilePictogramConverter.php');

/**
 * 携帯電話キャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileCarrier {
	private $attributes;
	private $mpc;
	static private $instances;
	const CARROT_INTERNAL = BS_CARROT_NAME;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray;
		preg_match('/^BS([a-z]+)MobileCarrier$/i', get_class($this), $matches);
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
	static public function getInstance ($carrier) {
		if (!self::$instances) {
			self::$instances = new BSArray;
			foreach (self::getNames() as $name) {
				$instance = BSClassLoader::getInstance()->getObject($name, 'MobileCarrier');
				self::$instances[$name] = $instance;
			}
		}

		$carrier = preg_replace('/[^a-z]/i', null, strtolower($carrier));
		foreach (self::$instances as $instance) {
			$names = new BSArray;
			$names[] = strtolower($instance->getName());
			$names[] = strtolower($instance->getMPCCode());
			$names->merge($instance->getAltNames());
			$names->uniquize();
			if ($names->isIncluded($carrier)) {
				return $instance;
			}
		}
		throw new BSMobileException('キャリア "%s" が見つかりません。', $name);
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
	 * @access public
	 * @return MPC_Common 絵文字変換器
	 */
	public function getMPC () {
		if (!$this->mpc) {
			BSUtility::includeFile('MPC/Carrier/' . strtolower($this->getMPCCode()) . '.php');
			$class = 'MPC_' . $this->getMPCCode();
			$this->mpc = new $class;
			$this->mpc->setFromCharset(MPC_FROM_CHARSET_UTF8);
			$this->mpc->setFrom($this->getMPCCode());
			$this->mpc->setStringType(MPC_FROM_OPTION_RAW);
			$this->mpc->setImagePath('/carrotlib/images/mpc');
		}
		return $this->mpc;
	}

	/**
	 * キャリア名の別名を返す
	 *
	 * @access public
	 * @return BSArray 別名の配列
	 */
	public function getAltNames () {
		return new BSArray;
	}

	/**
	 * MPC向けキャリア名を返す
	 *
	 * @access protected
	 * @return string キャリア名
	 * @abstract
	 */
	abstract protected function getMPCCode ();

	/**
	 * 絵文字を含んだ文字列を変換する
	 *
	 * @access public
	 * @param string $body 対象文字列
	 * @return string 変換後文字列
	 * @abstract
	 */
	public function convertPictogram ($body) {
		$this->getMPC()->setString($body);
		return $this->getMPC()->convert($this->getMPCCode(), self::CARROT_INTERNAL);
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
	 * 絵文字を返す
	 *
	 * @access public
	 * @param integer $code 絵文字コード
	 * @return string 絵文字
	 * @abstract
	 */
	public function getPictogram ($code) {
		$this->getMPC()->setTo($this->getMPCCode());
		$this->getMPC()->setOption(MPC_TO_OPTION_RAW);
		return $this->getMPC()->encoder((int)$code);
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

	/**
	 * 全キャリアのドメインサフィックスを返す
	 *
	 * @access public
	 * @return BSArray ドメインサフィックスの配列
	 * @static
	 */
	static public function getDomainSuffixes () {
		$suffiexes = new BSArray;
		foreach (self::getNames() as $name) {
			$suffiexes[$name] = self::getInstance($name)->getDomainSuffix();
		}
		return $suffiexes;
	}
}

/* vim:set tabstop=4: */
