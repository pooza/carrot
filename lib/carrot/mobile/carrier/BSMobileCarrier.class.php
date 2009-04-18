<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

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
	private $pictogramDirectory;
	static private $instances;
	const MPC_IMAGE = 'IMG';
	const MPC_RAW = 'RAW';
	const MPC_SMARTTAG = 'SMARTTAG';

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
			BSUtility::includeFile('MPC/MobilePictogramConverter.php');
			BSUtility::includeFile('MPC/Carrier/' . strtolower($this->getMPCCode()) . '.php');
			$class = 'MPC_' . $this->getMPCCode();
			$this->mpc = new $class;
			$this->mpc->setFromCharset('UTF-8');
			$this->mpc->setFrom($this->getMPCCode());
			$this->mpc->setStringType(BSMobileCarrier::MPC_RAW);
			$this->mpc->setImagePath('/carrotlib/images/pictogram');
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
	 * @access public
	 * @return string キャリア名
	 */
	public function getMPCCode () {
		return strtoupper($this->getName());
	}

	/**
	 * 絵文字ディレクトリの名前を返す
	 *
	 * @access protected
	 * @return string 絵文字ディレクトリの名前
	 */
	protected function getPictogramDirectoryName () {
		return strtolower(substr($this->getMPCCode(), 0, 1));
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
				$dir = BSController::getInstance()->getDirectory('pictogram');
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
		if ($code = $this->getPictogramCode($body)) {
			$body = $this->getPictogram($code);
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
	 * 絵文字を返す
	 *
	 * @access public
	 * @param integer $code 絵文字コード
	 * @return string 絵文字
	 */
	public function getPictogram ($code) {
		$this->getMPC()->setTo($this->getMPCCode());
		$this->getMPC()->setOption(BSMobileCarrier::MPC_RAW);
		return $this->getMPC()->encoder((int)$code);
	}

	/**
	 * 絵文字コードを返す
	 *
	 * @access public
	 * @param mixed $name 絵文字名、又は絵文字コード
	 * @return integer 絵文字コード
	 */
	public function getPictogramCode ($name) {
		require(BSConfigManager::getInstance()->compile('pictogram'));
		if (preg_match('/^[0-9]+$/', $name) && isset($config['codes'][$name])) {
			return $name;
		} else if (isset($config['names'][$name])) {
			if (isset($config['names'][$name][$this->getName()])) {
				$code = $config['names'][$name][$this->getName()];
			} else {
				$code = $config['names'][$name]['Docomo'];
			}
		}
	}

	/**
	 * 絵文字名を返す
	 *
	 * @access public
	 * @param mixed $name 絵文字名、又は絵文字コード
	 * @return integer 絵文字名
	 */
	public function getPictogramName ($name) {
		require(BSConfigManager::getInstance()->compile('pictogram'));
		if (preg_match('/^[0-9]+$/', $name) && isset($config['codes'][$name])) {
			return $config['codes'][$name];
		} else if (isset($config['names'][$name])) {
			return $name;;
		}
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
