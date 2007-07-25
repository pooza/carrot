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
 * @version $Id: BSJabberIDValidator.class.php 87 2006-05-28 16:42:49Z pooza $
 */
class BSJabberIDValidator extends RegexValidator {
	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = null) {
		$defaults = array(
			'match' => 'Yes',
			'match_error' => '書式が異なります。',
			'pattern' => BSJabberID::PATTERN,
		);
		$parameters = $defaults + (array)$parameters;
		return parent::initialize($context, $parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>