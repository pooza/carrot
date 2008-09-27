<?php
/**
 * @package org.carrot-framework
 * @subpackage session
 */

/**
 * ケータイ用セッションハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMobileSessionHandler extends BSSessionHandler {
	static private $instance;

	/**
	 * @access protected
	 */
	protected function __construct () {
		ini_set('session.use_only_cookies', 0);
		$this->getStorage()->initialize();
		session_start();

		$useragent = BSRequest::getInstance()->getUserAgent();
		$url = new BSURL;
		$url->setParameter($this->getName(), $this->getID());
		if (BSController::getInstance()->isDebugMode()) {
			$url->setParameter(BSRequest::USER_AGENT_ACCESSOR, $useragent->getName());
		}
		$useragent->setAttribute('query', $url->getParameters()->getParameters());
		$useragent->setAttribute('query_params', $url->getAttribute('query'));
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSessionHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSMobileSessionHandler;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * セッションIDを返す
	 *
	 * @access public
	 * @return integer セッションID
	 */
	public function getID () {
		if ($id = BSRequest::getInstance()->getParameter($this->getName())) {
			session_id($id);
		}
		return session_id();
	}
}

/* vim:set tabstop=4 ai: */
?>