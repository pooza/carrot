<?php
/**
 * LoginInputビュー
 *
 * @package __PACKAGE__
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LoginInputView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.LoginForm');
		$this->setAttribute('body', array('id' => 'LoginFormContainer'));
	}
}

/* vim:set tabstop=4 ai: */
?>