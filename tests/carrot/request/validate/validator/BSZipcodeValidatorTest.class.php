<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSZipcodeValidatorTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $validator = new BSZipcodeValidator);
		$this->assert('execute', $validator->execute('000-0000'));
		$this->assert('execute', !$validator->execute('0000000'));
		$this->assert('execute', !$validator->execute('000-00000'));

		$this->request['zipcode1'] = '000';
		$this->request['zipcode2'] = '0000';
		$this->assert('__construct', $validator = new BSZipcodeValidator);
		$validator->initialize(array(
			'fields' => array('zipcode1', 'zipcode2'),
		));
		$this->assert('execute', $validator->execute(null));
	}
}

/* vim:set tabstop=4: */
