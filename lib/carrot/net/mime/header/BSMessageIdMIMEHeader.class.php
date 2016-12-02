<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header
 */

/**
 * Message-IDヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMessageIdMIMEHeader extends BSMIMEHeader {
	protected $name = 'Message-ID';
	private $id;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSDate 実体
	 */
	public function getEntity () {
		return $this->id;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (BSString::isBlank($contents)) {
			$id = new BSStringFormat('%s.%s@%s');
			$id[] = BSDate::getNow('YmdHis');
			$id[] = BSUtility::getUniqueID();
			$id[] = BSController::getInstance()->getHost()->getName();
			$this->id = $id->getContents();
		} else {
			mb_ereg('^<?([^>]*)>?$', $contents, $matches);
			$this->id = $matches[1];
		}
		$this->contents = '<' . $this->id . '>';
	}

	/**
	 * 改行などの整形を行うか？
	 *
	 * @access protected
	 * @return boolean 整形を行うならTrue
	 */
	protected function isFormattable () {
		return false;
	}
}

/* vim:set tabstop=4: */
