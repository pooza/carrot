<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.feed.atom10
 */

/**
 * Atom1.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAtom10Document extends BSAtom03Document {
	protected $version = '1.0';
	protected $namespace = 'http://www.w3.org/2005/Atom';

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getElement('updated')) {
			return BSDate::create($element->getBody());
		}
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		if (!$element = $this->getElement('updated')) {
			$element = $this->createElement('updated');
		}
		$element->setBody($date->format(DateTime::ATOM));
	}
}

