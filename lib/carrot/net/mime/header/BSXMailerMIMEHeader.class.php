<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * X-MailerMIMEヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSXMailerMIMEHeader extends BSMIMEHeader {

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (BSString::isBlank($contents)) {
			$contents = sprintf(
				'%s (Powered by %s %s)',
				BSController::getFullName('en'),
				BS_CARROT_NAME,
				BS_CARROT_VER
			);
		}
		parent::setContents($contents);
	}
}

/* vim:set tabstop=4: */
