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
	$form->setAttachable(!!$params['attachable']);
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
