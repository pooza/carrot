<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 英字項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSEnglishValidator extends RegexValidator {

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
			'match_error' => '使用出来ない文字が含まれています。',
			'pattern' => "/^[[:print:]]*$/",
		);
		return parent::initialize($context, array_merge($defaults, $parameters));
	}
}

/* vim:set tabstop=4 ai: */
?>