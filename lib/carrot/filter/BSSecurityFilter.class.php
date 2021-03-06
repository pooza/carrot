<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * クレデンシャル認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSecurityFilter extends BSFilter {
	public function initialize ($params = []) {
		$this['credential'] = $this->action->getCredential();
		return parent::initialize($params);
	}

	public function execute () {
		if (!$this->user->hasCredential($this['credential'])) {
			if ($this->request->isAjax() || $this->request->isFlash()) {
				return $this->controller->getAction('not_found')->forward();
			}
			if ($this->action->deny()) {
				return BSController::COMPLETED;
			}
		}
	}

	/**
	 * 二度目も実行するか
	 *
	 * @access public
	 * @return boolean 二度目も実行するならTrue
	 */
	public function isRepeatable () {
		return true;
	}
}

