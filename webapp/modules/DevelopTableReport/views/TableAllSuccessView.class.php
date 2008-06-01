<?php
/**
 * TableAllSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class TableAllSuccessView extends BSPDFView {
	public function execute () {
		$this->setEngine(new BSTableReportPDF());
		foreach ($this->request->getAttribute('profiles') as $profile) {
			$this->getEngine()->putHeading($profile['tablename']);
			$this->getEngine()->putAttributes($profile['attributes']);
			$this->getEngine()->putFields($profile['fields']);
			$this->getEngine()->putKeys($profile['keys']);
		}
		$this->setFileName('TableReport.pdf');
	}
}

/* vim:set tabstop=4 ai: */
?>