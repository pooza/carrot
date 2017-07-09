<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSExcelExporterTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $exporter = new BSExcelExporter);
		$exporter->addRecord(BSArray::create([
			'name' => 'pooza',
			'point' => 100,
		]));
		$exporter->addRecord(BSArray::create([
			'name' => 'ビーショック',
			'point' => 900,
		]));
		$this->assert('getType', $exporter->getType() == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$this->assert('getContents', !BSString::isBlank($exporter->getContents()));
		$exporter->getFile()->delete();
	}
}

/* vim:set tabstop=4: */
