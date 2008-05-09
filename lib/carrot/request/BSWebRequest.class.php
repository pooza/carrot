<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * Webリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSWebRequest extends BSRequest {
	private static $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->setParameters($_GET);
		switch ($method = $_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$this->setMethod(self::POST);
				$this->setParameters($_POST);
				break;
			case 'GET':
				$this->setMethod(self::GET);
				break;
			default:
				throw new BSException('メソッド "%s" はサポートされていません。', $method);
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSWebRequest インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSWebRequest();
		}
		return self::$instance;
	}

	public function getFile ($name) {
		if ($this->hasFile($name)) {
			return new BSArray($_FILES[$name]);
		}
	}

	public function getFiles () {
		return $_FILES;
	}

	public function hasFile ($name) {
		return ($_FILES[$name]['name'] != '');
	}
}

/* vim:set tabstop=4 ai: */
?>
