<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSBlogUpdatePingServiceTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$params = new BSArray(array(
			'weblogname' => 'b-shock. Fortress',
			'weblogurl' => 'http://d.hatena.ne.jp/pooza/',
			'changeurl' => 'http://d.hatena.ne.jp/pooza/',
			'categoryname' => 'http://d.hatena.ne.jp/pooza/opensearch/diary.xml',
		));
		$this->assert('sendPings', !BSBlogUpdatePingService::sendPings($params));
	}
}

/* vim:set tabstop=4: */
