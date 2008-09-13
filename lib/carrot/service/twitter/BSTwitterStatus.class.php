<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterステータス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTwitterStatus {
	private $source;
	private $attributes = array();
	private $date;
	private $account;

	/**
	 * @access public
	 * @param BSXMLElement $source status要素
	 */
	public function __construct (BSXMLElement $source = null) {
		$this->source = $source;
	}

	/**
	 * ステータスIDを返す
	 *
	 * @access public
	 * @return integer ステータスID
	 */
	public function getID () {
		return $this->getAttribute('id');
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @return string 本文
	 */
	public function getBody () {
		return $this->getAttribute('text');
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if (!$this->date && $this->source) {
			$this->date = new BSDate($this->getAttribute('created_at'));
		}
		return $this->date;
	}

	/**
	 * アカウントを返す
	 *
	 * @access public
	 * @return BSTwitterAccount アカウント
	 */
	public function getAccount () {
		if (!$this->account && $this->source) {
			if ($element = $this->source->getElement('user')) {
				$this->account = new BSTwitterAccount($element);
			}
		}
		return $this->account;
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		if (!isset($this->attributes[$name]) && $this->source) {
			if ($element = $this->source->getElement($name)) {
				$this->attributes[$name] = $element->getBody();
			}
		}
		return $this->attributes[$name];
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterステータス "%d"', $this->getID());
	}
}

/* vim:set tabstop=4 ai: */
?>