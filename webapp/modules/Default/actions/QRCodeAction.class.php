<?php
/**
 * QRCodeアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class QRCodeAction extends BSAction {
	public function execute () {
		$qrcode = new BSQRCode;
		$qrcode->setData($this->request['value']);
		$this->request->setAttribute('renderer', $qrcode);
		return BSView::SUCCESS;
	}

	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest([
				$this->request['value'],
				$this->controller->getHost()->getName(),
				$this->getModule()->getName(),
				$this->getName(),
			]);
		}
		return $this->digest;
	}

	public function handleError () {
		$this->request->setAttribute(
			'renderer',
			BSFileUtility::getDirectory('images')->getEntry('spacer.gif')
		);
		return BSView::ERROR;
	}

	public function isCacheable () {
		return !$this->request->hasErrors();
	}
}

