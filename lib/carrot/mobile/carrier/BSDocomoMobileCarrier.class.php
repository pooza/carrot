<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * Docomo 携帯電話キャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDocomoMobileCarrier extends BSMobileCarrier {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'docomo.ne.jp';
	}

	/**
	 * キャリア名の別名を返す
	 *
	 * @access public
	 * @return BSArray 別名の配列
	 */
	public function getAltNames () {
		return new BSArray(array(
			'imode',
			'foma',
			'mova',
		));
	}

	/**
	 * MPC向けキャリア名を返す
	 *
	 * @access protected
	 * @return string キャリア名
	 */
	protected function getMPCCode () {
		return 'FOMA';
	}
}

/* vim:set tabstop=4: */
