<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
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
	private $plathome;

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
	public static function createInstance ($name) {
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
	 * ユーザーエージェント名を設定する
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function setName ($name) {
		$this->name = $name;
	}

	/**
	 * メジャーバージョンを返す
	 *
	 * @access public
	 * @return string メジャーバージョン
	 */
	public function getMajorVersion () {
		return null;
	}

	/**
	 * マイナーバージョンを返す
	 *
	 * @access public
	 * @return string マイナーバージョン
	 */
	public function getMinorVersion () {
		return null;
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
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		return array(
			'name' => $this->getName(),
			'is_mobile' => $this->isMobile(),
			'is_' . strtolower($this->getType()) => true,
			'is_' . strtolower($this->getPlathome()) => true,
			'type' => $this->getTypeName(),
			'plathome' => $this->getPlathome(),
			'upload_button_label' => $this->getUploadButtonLabel(),
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
	 * ロボットか？
	 *
	 * @access public
	 * @return boolean ロボットならTrue
	 */
	public function isRobot () {
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
	 * プラットホーム名を返す
	 *
	 * @access public
	 * @return string プラットホーム名
	 */
	public function getPlathome () {
		if (!$this->plathome) {
			$this->plathome = 'UnknownPlathome';
			$patterns = array(
				'Windows' => '/windows/i',
				'MacOSX' => '/mac ?os ?x/i',
				'MacOS' => '/mac( ?os|_powerpc|_68k)/i',
				'X11' => '/x11/i',
			);
			foreach ($patterns as $key => $pattern) {
				if (preg_match($pattern, $this->getName())) {
					$this->plathome = $key;
					return $this->plathome;
				}
			}
		}
		return $this->plathome;
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
	public static function getTypes () {
		// 評価を行う順に記述すること
		return array(
			'Opera',
			'WebKit',
			'Gecko',
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