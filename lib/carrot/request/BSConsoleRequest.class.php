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
 * @version $Id: BSConsoleRequest.class.php 232 2008-04-22 08:08:16Z pooza $
 */
class BSConsoleRequest extends BSRequest {

	/**
	 * 初期化
	 *
	 * @access protected
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize ($context, $parameters = null) {
		$this->setMethod(self::NONE);

		$options = array(
			BSController::MODULE_ACCESSOR,
			BSController::ACTION_ACCESSOR,
			null,
		);
		$options = implode(':', $options);
		$this->request->setParameters(getopt($options));
	}
}

?>