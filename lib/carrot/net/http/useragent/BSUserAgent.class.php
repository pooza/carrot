<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * ユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSUserAgent {
	private $type;
	protected $attributes;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getTypeName();
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
	}

	/**
	 * インスタンスを返す
	 *
	 * @access public
	 * @param string $useragent UserAgent名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function getInstance ($useragent) {
		$types = new BSArray;
		$name = sprintf('%s.%s', __CLASS__, __FUNCTION__);
		if ($values = BSController::getInstance()->getAttribute($name)) {
			$types->setAttributes($values);
		}

		if ($type = $types[$useragent]) {
			$class = 'BS' . $type . 'UserAgent';
			$instance = new $class($useragent);
		} else {
			foreach (self::getTypes() as $type) {
				$class = 'BS' . $type . 'UserAgent';
				$instance = new $class;
				if (preg_match($instance->getPattern(), $useragent)) {
					$instance->setName($useragent);
					break;
				}
			}
			$types[$useragent] = $type;
			BSController::getInstance()->setAttribute($name, $types->getParameters());
		}

		if (!$instance) {
			$instance = new BSUserAgent($useragent);
		}
		return $instance;
	}

	/**
	 * browscap.iniの情報をインポートする
	 *
	 * @access public
	 */
	public function importBrowscap () {
		$this->attributes->setParameters(
			BSBrowscap::getInstance()->getInfo($this->getName())
		);
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
	 */
	public function getPattern () {
		return null;
	}

	/**
	 * 登録済みのタイプを配列で返す
	 *
	 * @access public
	 * @return string[] タイプリスト
	 * @static
	 */
	static public function getTypes () {
		// 評価を行う順に記述すること
		return array(
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
		);
	}

	/**
	 * タイプを返す
	 *
	 * @access public
	 * @return string タイプ
	 */
	public function getType () {
		if (!$this->type) {
			if (preg_match('/BS([a-z0-9]+)UserAgent/i', get_class($this), $matches)) {
				$this->type = $matches[1];
			} else {
				$this->type = 'GenericUserAgent';
			}
		}
		return $this->type;
	}

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'タイプ不明';
	}
}

/* vim:set tabstop=4 ai: */
?>