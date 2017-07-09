<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBlogUpdatePingServiceTest extends BSTest {
	public function execute () {
		$params = BSArray::create([
			'weblogname' => 'b-shock. Fortress',
			'weblogurl' => 'http://d.hatena.ne.jp/pooza/',
			'changeurl' => 'http://d.hatena.ne.jp/pooza/',
			'categoryname' => 'http://d.hatena.ne.jp/pooza/opensearch/diary.xml',
		]);
		$this->assert('sendPings', !BSBlogUpdatePingService::sendPings($params));
	}
}

/* vim:set tabstop=4: */
