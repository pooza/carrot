<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * ユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSUserAgent {
	private $type;
	private $denied = null;
	protected $attributes;
	protected $session;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getType();
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
		$this->attributes['is_unsupported'] = $this->isDenied();
		$this->attributes['is_denied'] = $this->isDenied();
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $useragent UserAgent名
	 * @param string $type タイプ名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function getInstance ($useragent, $type = null) {
		if (!$type) {
			$type = self::getDefaultType($useragent);
		}
		$class = 'BS' . $type . 'UserAgent';
		return new $class($useragent);
	}

	/**
	 * 規定タイプ名を返す
	 *
	 * @access public
	 * @param string $useragent UserAgent名
	 * @return string タイプ名
	 * @static
	 */
	static public function getDefaultType ($useragent) {
		foreach (self::getTypes() as $type) {
			$class = 'BS' . $type . 'UserAgent';
			$instance = new $class;
			if (preg_match($instance->getPattern(), $useragent)) {
				return $type;
			}
		}
		return 'Console';
	}

	/**
	 * 非対応のUserAgentか？
	 *
	 * @access public
	 * @return boolean 非対応のUserAgentならTrue
	 */
	public function isDenied () {
		if ($this->denied === null) {
			$config = array();
			require(BSConfigManager::getInstance()->compile('useragent/carrot'));
			$types = $config['Deny'];
			require(BSConfigManager::getInstance()->compile('useragent/application'));
			if (is_array($config['Deny'])) {
				$types += $config['Deny'];
			}

			if (!isset($types[$this->getType()])) {
				$this->denied = false;
			} else if (is_array($types[$this->getType()])) {
				foreach ($types[$this->getType()] as $pattern) {
					if (strpos($this->getName(), $pattern) !== false) {
						return $this->denied = true;
					}
				}
				$this->denied = false;
			} else {
				$this->denied = true;
			}
		}
		return $this->denied;
	}

	/**
	 * 非対応のUserAgentか？
	 *
	 * isDeniedのエイリアス
	 *
	 * @access public
	 * @return boolean 非対応のUserAgentならTrue
	 * @final
	 */
	final public function isUnsupported () {
		return $this->isDenied();
	}

	/**
	 * Smartyを初期化する
	 *
	 * @access public
	 * @param BSSmarty
	 */
	public function initializeSmarty (BSSmarty $smarty) {
		$this->importBrowscap();
		$smarty->setAttribute('useragent', $this->getAttributes());
		$smarty->addOutputFilter('trim');
	}

	/**
	 * browscap.iniの情報をインポートする
	 *
	 * @access public
	 */
	public function importBrowscap () {
		$browscap = BSBrowscap::getInstance();
		if ($browscap->isEnable()) {
			$this->attributes->setParameters($browscap->getInfo($this->getName()));
		}
	}

	/**
	 * セッションハンドラを設定
	 *
	 * @access public
	 * @param BSSessionHandler
	 */
	public function setSession (BSSessionHandler $session) {
		$this->session = $session;
	}

	/**
	 * ユーザーエージェント名を返す
	 *
	 * @access public
	 * @return string ユーザーエージェント名
	 */
	public function getName () {
		return $this->getAttributes()->getParameter('name');
	}

	/**
	 * ユーザーエージェント名を設定
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function setName ($name) {
		return $this->getAttributes()->setParameter('name', $name);
	}

	/**
	 * キャッシングに関するバグがあるか？
	 *
	 * @access public
	 * @return boolean バグがあるならTrue
	 */
	public function isBuggy () {
		return false;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		if (!$value = $this->getAttributes()->getParameter($name)) {
			$this->importBrowscap();
			$value = $this->getAttributes()->getParameter($name);
		}
		return $value;
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
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return false;
	}

	/**
	 * アップロードボタンのラベルを返す
	 *
	 * @access public
	 * @return string アップロードボタンのラベル
	 */
	public function getUploadButtonLabel () {
		return '参照...';
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function getEncodedFileName ($name) {
		$name = BSSMTP::base64Encode($name);
		return BSString::sanitize($name);
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 * @abstract
	 */
	abstract public function getPattern ();

	/**
	 * タイプを返す
	 *
	 * @access public
	 * @return string タイプ
	 */
	public function getType () {
		if (!$this->type) {
			preg_match('/BS([a-z0-9]+)UserAgent/i', get_class($this), $matches);
			$this->type = $matches[1];
		}
		return $this->type;
	}

	/**
	 * 登録済みのタイプを配列で返す
	 *
	 * @access private
	 * @return BSArray タイプリスト
	 * @static
	 */
	static private function getTypes () {
		return new BSArray(array(
			'Trident',
			'Tasman',
			'Gecko',
			'WebKit',
			'Opera',
			'LegacyMozilla',
			'Docomo',
			'Au',
			'SoftBank',
			'Console',
		));
	}
}

/* vim:set tabstop=4 ai: */
?>