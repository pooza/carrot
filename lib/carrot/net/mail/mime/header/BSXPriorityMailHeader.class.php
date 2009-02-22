<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * X-Priorityメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSXPriorityMailHeader extends BSMailHeader {

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (!in_array($contents, range(1, 5))) {
			throw new BSMailException('優先順位"%d"が正しくありません。', $contents);
		}
		parent::setContents($contents);
	}
}

/* vim:set tabstop=4: */
