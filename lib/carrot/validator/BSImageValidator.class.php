<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 画像バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSImageValidator extends Validator {

	/**
	 * 許可されるメディアタイプを返す
	 *
	 * @access private
	 * @return BSArray 許可されるメディアタイプ
	 */
	private function getAllowedTypes () {
		if ($this->getParameter('types')) {
			$types = new BSArray;
			foreach (BSString::explode(',', $this->getParameter('types')) as $type) {
				if (!preg_match('/^image\//', $type)) {
					$type = 'image/' . $type;
				}
				$types[] = $type;
			}
			return $types;
		} else {
			return BSImage::getTypes();
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		$this->setParameter('types', 'jpeg,gif,png');
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
		try {
			$file = new BSImageFile($value['tmp_name']);
			if (!$file->isExists()) {
				$error = 'アップロードされていません。';
				return false;
			}
			$image = $file->getEngine();
		} catch (BSException $e) {
			$error = '画像フォーマットが正しくありません。';
			return false;
		}

		$params = new BSArray($this->getParameters());
		if (!$this->getAllowedTypes()->isIncluded($image->getType())) {
			$error = '画像フォーマットが正しくありません。';
		} else if ($params['min_width'] && ($image->getWidth() < $params['min_width'])) {
			$error = '画像の幅が狭過ぎます。';
		} else if ($params['min_height'] && ($image->getHeight() < $params['min_height'])) {
			$error = '画像の高さが低過ぎます。';
		} else if ($params['max_width'] && ($params['max_width'] < $image->getWidth())) {
			$error = '画像の幅が広過ぎます。';
		} else if ($params['max_height'] && ($params['max_height'] < $image->getHeight())) {
			$error = '画像の高さが高過ぎます。';
		}
		return ($error == null);
	}
}

/* vim:set tabstop=4 ai: */
?>