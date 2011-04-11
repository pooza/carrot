<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSExcelExporterTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $exporter = new BSExcelExporter);
	}
}

/* vim:set tabstop=4: */
