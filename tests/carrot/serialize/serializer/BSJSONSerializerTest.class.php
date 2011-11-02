<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJSONSerializerTest extends BSTest {
	public function execute () {
		$serializer = new BSJSONSerializer;
		if ($serializer->initialize()) {
			$encoded = $serializer->encode(array('key' => '木の水晶球'));
			$this->assert('encode', $encoded == '{"key":"\u6728\u306e\u6c34\u6676\u7403"}');
		}
	}
}

/* vim:set tabstop=4: */
