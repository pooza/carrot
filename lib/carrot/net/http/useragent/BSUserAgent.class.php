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
	private $name;
	private $type;
	private $browscap;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->setName($name);
	}

	/**
	 * インスタンスを返す
	 *
	 * @access public
	 * @param string $name UserAgent名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function getInstance ($name) {
		foreach (self::getTypes() as $type) {
			$type = 'BS' . $type . 'UserAgent';
			$useragent = new $type;
			if (preg_match($useragent->getPattern(), $name)) {
				$useragent->setName($name);
				return $useragent;
			}
		}
		return new BSUserAgent($name);
	}

	/**
	 * ユーザーエージェント名を返す
	 *
	 * @access public
	 * @return string ユーザーエージェント名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * ユーザーエージェント名を設定
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function setName ($name) {
		$this->name = $name;
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
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		$attributes = $this->getAttributes();
		if (isset($attributes[$name])) {
			return $attributes[$name];
		}

		if (!$this->browscap) {
			$this->browscap = BSBrowscap::getInstance()->getInfo($this->getName());
		}
		if (isset($this->browscap[$name])) {
			return $this->browscap[$name];
		}
	}

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		return array(
			'name' => $this->getName(),
			'type' => $this->getTypeName(),
			'is_' . strtolower($this->getType()) => true,
			'is_mobile' => $this->isMobile(),
		);
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
			'Opera',
			'WebKit',
			'Gecko',
			'Tasman',
			'MSIE',
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