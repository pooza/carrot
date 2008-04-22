<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * フリガナ変換フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSKanaFilter extends BSFilter {
	public function execute (FilterChain $filters) {
		foreach ($this->request->getParameters() as $key => $value) {
			if (preg_match('/_read$/', $key)) {
				$value = str_replace(' ', '', $value);
				$value = BSString::convertKana($value, 'KVC');
				$this->request->setParameter($key, $value);
			}
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>