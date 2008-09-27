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
	protected $attributes;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getType();
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $useragent UserAgent名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function getInstance ($useragent) {
		$class = 'BS' . self::getDefaultType($useragent) . 'UserAgent';
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
		$types = new BSArray;
		$name = sprintf('%s.%s', __CLASS__, __FUNCTION__);
		if ($values = BSController::getInstance()->getAttribute($name)) {
			$types->setAttributes($values);
		}

		if (!$type = $types[BSCrypt::getSHA1($useragent)]) {
			foreach (self::getTypes() as $type) {
				$class = 'BS' . $type . 'UserAgent';
				$instance = new $class;
				if (preg_match($instance->getPattern(), $useragent)) {
					break;
				}
			}
			$types[BSCrypt::getSHA1($useragent)] = $type;
			BSController::getInstance()->setAttribute($name, $types->getParameters());
		}

		return $type;
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
		if (BSController::getInstance()->isResolvable()) {
			$this->attributes->setParameters(
				BSBrowscap::getInstance()->getInfo($this->getName())
			);
		}
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
	public function hasCachingBug () {
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
		// 評価を行う順に記述すること
		return new BSArray(array(
			'Tasman',
			'MSIE',
			'Chrome',
			'Safari',
			'WebKit',
			'Gecko',
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