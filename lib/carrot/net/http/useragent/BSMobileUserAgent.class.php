<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		$query = array(session_name() => $this->getSessionID());
		if (BSController::getInstance()->isDebugMode()) {
			$query['ua'] = BSController::getInstance()->getUserAgent()->getName();
		}
		$attributes = parent::getAttributes();
		$attributes['query'] = $query;
		$attributes['query_params'] = BSString::toString($query, '=', '&');
		$attributes['is_unsupported'] = $this->isUnsupported();
		return $attributes;
	}

	/**
	 * セッションIDを返す
	 *
	 * @access private
	 * @return string セッションID
	 */
	public function getSessionID () {
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
		require_once(ConfigCache::checkConfig('config/mobile/unsupport_terminals.ini'));
		if (!isset($terminals[$this->getTypeName()])) {
			return false;
		}
		$patterns = new BSArray($terminals[$this->getTypeName()]);

		foreach ($patterns as $pattern) {
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
	public static function getDomainSuffixes () {
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