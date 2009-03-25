<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent {
	private $carrier;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['id'] = $this->getID();
		$this->attributes['query'] = new BSArray;
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 * @abstract
	 */
	abstract public function getID ();

	/**
	 * Smartyを初期化する
	 *
	 * @access public
	 * @param BSSmarty
	 */
	public function initializeSmarty (BSSmarty $smarty) {
		$smarty->setAttribute('useragent', $this->getAttributes());
		$smarty->setEncoding('sjis-win');
		$smarty->addOutputFilter('mobile');
		$smarty->addOutputFilter('encoding');
		$smarty->addOutputFilter('trim');
	}

	/**
	 * セッションハンドラを設定
	 *
	 * @access public
	 * @param BSSessionHandler
	 */
	public function setSession (BSSessionHandler $session) {
		parent::setSession($session);

		$url = new BSURL;
		$url->setParameters($this->attributes['query']);
		$url->setParameter($session->getName(), $session->getID());
		if (BS_DEBUG) {
			$url->setParameter(BSRequest::USER_AGENT_ACCESSOR, $this->getName());
		}
		$this->attributes['query'] = $url->getParameters();
		$this->attributes['query_params'] = $url['query'];
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return true;
	}

	/**
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			$this->attributes['platform'] = $this->getType();
		}
		return $this->attributes['platform'];
	}

	/**
	 * キャリアを返す
	 *
	 * @access public
	 * @return BSMobileCarrier キャリア
	 */
	public function getCarrier () {
		if (!$this->carrier) {
			$this->carrier = BSClassLoader::getInstance()->getObject(
				$this->getType(),
				'MobileCarrier'
			);
		}
		return $this->carrier;
	}
}

/* vim:set tabstop=4: */
