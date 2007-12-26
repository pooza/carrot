<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * レコードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSRecordValidator extends Validator {

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean そのIDを持ったレコードが存在すればTrue
	 */
	public function execute (&$value, &$error) {
		try {
			$class = BSString::pascalize($this->getParameter('table')) . 'Handler';
			$table = new $class;
			return ($table->getRecord($value) != null);
		} catch (Exception $e) {
			$error = $e->getMessage();
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>