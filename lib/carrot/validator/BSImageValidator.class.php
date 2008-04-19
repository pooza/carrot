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
		$this->setParameter('types_error', '画像形式が正しくありません。');
		$this->setParameter('min_height', null);
		$this->setParameter('min_height_error', '画像の高さが低過ぎます。');
		$this->setParameter('max_height', null);
		$this->setParameter('max_height_error', '画像の高さが高過ぎます。');
		$this->setParameter('min_width', null);
		$this->setParameter('min_width_error', '画像の幅が狭過ぎます。');
		$this->setParameter('max_width', null);
		$this->setParameter('max_width_error', '画像の幅が広過ぎます。');
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
			$image = $file->getEngine();
		} catch (BSException $e) {
			$error = $this->getParameter('types_error');
			return false;
		}

		$params = new BSArray($this->getParameters());
		if (!$this->getAllowedTypes()->isIncluded($image->getType())) {
			$error = $this->getParameter('types_error');
		} else if ($params['min_width'] && ($image->getWidth() < $params['min_width'])) {
			$error = $this->getParameter('min_width_error');
		} else if ($params['min_height'] && ($image->getHeight() < $params['min_height'])) {
			$error = $this->getParameter('min_height_error');
		} else if ($params['max_width'] && ($params['max_width'] < $image->getWidth())) {
			$error = $this->getParameter('max_width_error');
		} else if ($params['max_height'] && ($params['max_height'] < $image->getHeight())) {
			$error = $this->getParameter('max_height_error');
		}
		return ($error == null);
	}
}

/* vim:set tabstop=4 ai: */
?>