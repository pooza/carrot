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
abstract class BSUserAgent implements BSAssignable {
	private $type;
	protected $attributes;
	protected $bugs;
	protected $session;
	static private $denied;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getType();
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
		$this->attributes['is_denied'] = $this->isDenied();
		$this->bugs = new BSArray;
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
		$class = BSClassLoader::getInstance()->getClassName($type, 'UserAgent');
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
			$instance = BSClassLoader::getInstance()->getObject($type, 'UserAgent');
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
		if ($type = $this->getDeniedTypes()->getParameter($this->getType())) {
			if (isset($type['denied']) && $type['denied']) {
				return true;
			}
			if (isset($type['denied_patterns']) && is_array($type['denied_patterns'])) {
				foreach ($type['denied_patterns'] as $pattern) {
					if (strpos($this->getName(), $pattern) !== false) {
						return true;
					}
				}
			}
		}
		return false;
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
		$smarty->setAttribute('useragent', $this);
		$smarty->addOutputFilter('trim');
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
	 * バグがあるか？
	 *
	 * @access public
	 * @param string $name バグ名
	 * @return boolean バグがあるならTrue
	 */
	public function hasBug ($name) {
		return ($this->bugs[$name] || $this->bugs['general']);
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
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			$pattern = '/^Mozilla\/[0-9]\.[0-9]+ \(([^;]+);/';
			if (preg_match($pattern, $this->getName(), $matches)) {
				$this->attributes['platform'] = $matches[1];
			}
		}
		return $this->attributes['platform'];
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
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getAttributes()->getParameters();
	}

	/**
	 * 全てのタイプ情報を返す
	 *
	 * @access private
	 * @return BSArray 全てのタイプ情報
	 * @static
	 */
	static private function getDeniedTypes () {
		if (!self::$denied) {
			self::$denied = new BSArray;
			require(BSConfigManager::getInstance()->compile('useragent/carrot'));
			self::$denied->setParameters($config);
			require(BSConfigManager::getInstance()->compile('useragent/application'));
			self::$denied->setParameters($config);
		}
		return self::$denied;
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
			'Gecko',
			'WebKit',
			'Opera',
			'Tasman',
			'LegacyMozilla',
			'Docomo',
			'Au',
			'SoftBank',
			'Console',
			'Default',
		));
	}
}

/* vim:set tabstop=4: */
