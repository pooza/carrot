<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConsoleRequest extends BSRequest {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize (Context $context, $parameters = null) {
		$options = array(
			BSController::MODULE_ACCESSOR,
			BSController::ACTION_ACCESSOR,
			null,
		);
		$options = implode(':', $options);
		$this->setParameters(getopt($options));
	}
}

?>