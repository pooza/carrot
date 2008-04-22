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
 * @version $Id: BSWebRequest.class.php 233 2008-04-22 08:38:49Z pooza $
 */
class BSWebRequest extends BSRequest {

	/**
	 * 初期化
	 *
	 * @access protected
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize (Context $context, $parameters = null) {
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
		if (isset($_FILES[$name])) {
			return $_FILES[$name];
		}
	}

	public function getFiles () {
		return $_FILES;
	}

	public function hasFile ($name) {
		return isset($_FILES[$name]);
	}
}

?>