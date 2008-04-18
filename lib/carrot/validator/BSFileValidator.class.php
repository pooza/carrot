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
		if (!$value || !is_array($value) || !$value['size']) {
			$error = '正しいファイルではありません。';
			return false;
		}

		$file = new BSFile($value['tmp_name']);
		if (!$file->isExists() || !$file->isUploaded()) {
			$error = '正しいファイルではありません。';
			return false;
		}

		$max = $this->getParameter('size') * 1024 * 1024;
		if ($max < $file->getSize()) {
			$error = 'ファイルが大きすぎます。';
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>