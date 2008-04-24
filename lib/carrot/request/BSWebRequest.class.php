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

	/**
	 * 初期化
	 *
	 * @access public
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize ($parameters = null) {
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

	public function getFile ($name) {
		if (isset($_FILES[$name]['name'])) {
			return $_FILES[$name];
		}
	}

	public function getFiles () {
		return $_FILES;
	}

	public function hasFile ($name) {
		return isset($_FILES[$name]['name']);
	}
}

?>