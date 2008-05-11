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
class BSImageValidator extends BSValidator {

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
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
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
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		try {
			if (!$name = $value['tmp_name']) {
				throw new BSImageException('ファイルが存在しない、又は正しくありません。');
			}
			$file = new BSImageFile($name);
			$image = $file->getEngine();
		} catch (BSException $e) {
			$this->error = $this->getParameter('types_error');
			return false;
		}

		$params = new BSArray($this->getParameters());
		if (!$this->getAllowedTypes()->isIncluded($image->getType())) {
			$this->error = $this->getParameter('types_error');
		} else if ($params['min_width'] && ($image->getWidth() < $params['min_width'])) {
			$this->error = $this->getParameter('min_width_error');
		} else if ($params['min_height'] && ($image->getHeight() < $params['min_height'])) {
			$this->error = $this->getParameter('min_height_error');
		} else if ($params['max_width'] && ($params['max_width'] < $image->getWidth())) {
			$this->error = $this->getParameter('max_width_error');
		} else if ($params['max_height'] && ($params['max_height'] < $image->getHeight())) {
			$this->error = $this->getParameter('max_height_error');
		}
		return ($this->error == null);
	}
}

/* vim:set tabstop=4 ai: */
?>