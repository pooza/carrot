<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * Au 携帯電話キャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAuMobileCarrier extends BSMobileCarrier {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'ezweb.ne.jp';
	}

	/**
	 * キャリア名の別名を返す
	 *
	 * @access public
	 * @return BSArray 別名の配列
	 */
	public function getAltNames () {
		return new BSArray(array(
			'ezweb',
			'ez',
			'kddi',
			'tuka',
		));
	}

	/**
	 * MPC向けキャリア名を返す
	 *
	 * @access protected
	 * @return string キャリア名
	 */
	protected function getMPCCode () {
		return 'EZWEB';
	}

	/**
	 * 絵文字ディレクトリの名前を返す
	 *
	 * @access protected
	 * @return string 絵文字ディレクトリの名前
	 */
	protected function getPictogramDirectoryName () {
		return 'e';
	}
}

/* vim:set tabstop=4: */
