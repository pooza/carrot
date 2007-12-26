<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * JabberIDバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSJabberIDValidator extends RegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$defaults = array(
			'match' => 'Yes',
			'match_error' => '正しいJabberIDではありません。',
			'pattern' => BSJabberID::PATTERN,
		);
		$parameters = $defaults + (array)$parameters;
		return parent::initialize($context, $parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>