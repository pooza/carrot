<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * ファイルバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSFileValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$this->setParameter('size', 2);
		$this->setParameter('size_error', 'ファイルサイズが大きすぎます。');
		$this->setParameter('invalid_error', '正しいファイルではありません。');
		return parent::initialize($context, $parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 結果
	 */
	public function execute (&$value, &$error) {
		if (!$value || !is_array($value)) {
			$error = $this->getParameter('invalid_error');
			return false;
		} else if ($value['name']) {
			if (($this->getParameter('size') * 1024 * 1024) < $value['size']) {
				$error = $this->getParameter('size_error');
				return false;
			} else if ($value['error'] == 2) {
				$error = $this->getParameter('size_error');
				return false;
			} else if ($value['error']) {
				$error = $this->getParameter('invalid_error');
				return false;
			}

			$file = new BSFile($value['tmp_name']);
			if (!$file->isExists() || !$file->isUploaded()) {
				$error = $this->getParameter('invalid_error');
				return false;
			}
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>