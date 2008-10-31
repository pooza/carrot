<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent {

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
		$smarty->setEncoding('sjis');
		$smarty->addOutputFilter('mobile');
		$smarty->addOutputFilter('encoding');
		$smarty->addOutputFilter('trim');
	}

	/**
	 * browscap.iniの情報をインポートする
	 *
	 * @access public
	 */
	public function importBrowscap () {
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
		if (BSController::getInstance()->isDebugMode()) {
			$url->setParameter(BSRequest::USER_AGENT_ACCESSOR, $this->getName());
		}
		$this->attributes['query'] = $url->getParameters();
		$this->attributes['query_params'] = $url->getAttribute('query');
	}

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

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
	 * 全キャリアのドメインサフィックスを返す
	 *
	 * @access public
	 * @return string[] ドメインサフィックスの配列
	 * @static
	 */
	static public function getDomainSuffixes () {
		$patterns = array();
		foreach (array('Docomo', 'Au', 'SoftBank') as $carrier) {
			$name = sprintf('BS%sUserAgent', $carrier);
			$useragent = new $name;
			$patterns[$useragent->getType()] = $useragent->getDomainSuffix();
		}
		return $patterns;
	}
}

/* vim:set tabstop=4 ai: */
?>