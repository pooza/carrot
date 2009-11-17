<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * form要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_block_form ($params, $contents, &$smarty) {
	$params = new BSArray($params);
	$form = new BSFormElement;
	$form->setBody($contents);

	if (BSString::isBlank($params['method'])) {
		$params['method'] = 'POST';
	}
	$form->setMethod($params['method']);
	if (!!$params['attachable']) {
		$form->setAttachable(true);
		if (!BSString::isBlank($size = $params['attachment_size'])) {
			$form->addHiddenField('MAX_FILE_SIZE', $size * 1024 * 1024);
		}
	}
	$form->setAction($params);

	$params->removeParameter('method');
	$params->removeParameter('attchable');
	$params->removeParameter('path');
	$params->removeParameter('module');
	$params->removeParameter('action');
	$form->setAttributes($params);

	return $form->getContents();
}

/* vim:set tabstop=4: */
