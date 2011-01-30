<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * X-Priorityヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXPriorityMIMEHeader extends BSMIMEHeader {
	protected $name = 'X-Priority';

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (!in_array($contents, range(1, 5))) {
			$message = new BSStringFormat('優先順位"%d"が正しくありません。');
			$message[] = $contents;
			throw new BSMailException($message);
		}
		parent::setContents($contents);
	}
}

/* vim:set tabstop=4: */
