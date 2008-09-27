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
		$this->attributes['is_unsupported'] = $this->isUnsupported();

		$query = array(session_name() => $this->getSessionID());
		if (BSController::getInstance()->isDebugMode()) {
			$query['ua'] = BSRequest::getInstance()->getUserAgentName();
		}
		$this->attributes['query'] = $query;
		$this->attributes['query_params'] = BSString::toString($query, '=', '&');
	}

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
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

	/**
	 * セッションIDを返す
	 *
	 * @access private
	 * @return string セッションID
	 */
	private function getSessionID () {
		if ($id = BSRequest::getInstance()->getParameter(session_name())) {
			session_id($id);
		}
		return session_id();
	}

	/**
	 * 非対応端末か？
	 *
	 * @access public
	 * @return boolean 非対応端末ならTrue
	 */
	public function isUnsupported () {
		$config = array();
		require(BSConfigManager::getInstance()->compile('mobile/unsupport_terminals'));
		if (!isset($config[$this->getType()]['terminals'])) {
			return false;
		}
		foreach ($config[$this->getType()]['terminals'] as $pattern) {
			if (strpos($this->getName(), $pattern) !== false) {
				return true;
			}
		}
		return false;
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