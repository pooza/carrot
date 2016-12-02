<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header
 */

/**
 * Statusヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSStatusMIMEHeader extends BSMIMEHeader {
	protected $name = 'Status';

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (mb_ereg('^([[:digit:]]{3}) ', $contents, $matches)) {
			return $this->setContents($matches[1]);
		} else if (!is_numeric($contents)) {
			$message = new BSStringFormat('ステータス"%s"は正しくありません。');
			$message[] = $contents;		
			throw new BSHTTPException($message);
		}

		$this['code'] = $contents;
		if (BSString::isBlank($status = BSHTTP::getStatus($contents))) {
			$message = new BSStringFormat('ステータス"%s"は正しくありません。');
			$message[] = $contents;		
			throw new BSHTTPException($message);
		}
		parent::setContents($status);
	}
}

/* vim:set tabstop=4: */
