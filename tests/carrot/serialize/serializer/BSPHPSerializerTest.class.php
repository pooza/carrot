<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPHPSerializerTest extends BSTest {
	public function execute () {
		$serializer = new BSPHPSerializer;
		if ($serializer->initialize()) {
			$encoded = $serializer->encode(array('key' => '木の水晶球'));
			$this->assert('encode', $encoded == 'a:1:{s:3:"key";s:15:"木の水晶球";}');
		}
	}
}

/* vim:set tabstop=4: */
