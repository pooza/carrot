<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 画像バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		if ($this['types']) {
			if (BSArray::isArray($this['types'])) {
				$types = new BSArray($this['types']);
			} else {
				$types = BSString::explode(',', $this['types']);
			}

			foreach ($types as $type) {
				if ($suggested = BSMediaType::getType($type)) {
					$type = $suggested;
				} else if (!preg_match('/^image\//', $type)) {
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
		$this['types'] = 'jpeg,gif,png';
		$this['types_error'] = '画像形式が正しくありません。';
		$this['min_height'] = null;
		$this['min_height_error'] = '画像の高さが低過ぎます。';
		$this['max_height'] = null;
		$this['max_height_error'] = '画像の高さが高過ぎます。';
		$this['min_width'] = null;
		$this['min_width_error'] = '画像の幅が狭過ぎます。';
		$this['max_width'] = null;
		$this['max_width_error'] = '画像の幅が広過ぎます。';
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
			$this->error = $this['types_error'];
			return false;
		}

		if (!$this->getAllowedTypes()->isIncluded($image->getType())) {
			$this->error = $this['types_error'];
		} else if ($this['min_width'] && ($image->getWidth() < $this['min_width'])) {
			$this->error = $this['min_width_error'];
		} else if ($this['min_height'] && ($image->getHeight() < $this['min_height'])) {
			$this->error = $this['min_height_error'];
		} else if ($this['max_width'] && ($this['max_width'] < $image->getWidth())) {
			$this->error = $this['max_width_error'];
		} else if ($this['max_height'] && ($this['max_height'] < $image->getHeight())) {
			$this->error = $this['max_height_error'];
		}
		return ($this->error == null);
	}
}

/* vim:set tabstop=4: */
