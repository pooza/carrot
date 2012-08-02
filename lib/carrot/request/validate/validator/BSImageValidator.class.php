<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 画像バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImageValidator extends BSValidator {
	private function getAllowedTypes () {
		if (BSString::isBlank($types = $this['types'])) {
			return BSImage::getTypes();
		} else {
			if (is_array($types) || ($types instanceof BSParameterHolder)) {
				$types = BSArray::create($types);
			} else {
				$types = BSString::explode(',', $types);
			}

			foreach ($types as $type) {
				if ($suggested = BSMIMEType::getType($type)) {
					$type = $suggested;
				} else if (!mb_ereg('^image/', $type)) {
					$type = 'image/' . $type;
				}
				$types[] = $type;
			}
			return $types;
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['types'] = 'jpg,gif,png';
		$this['types_error'] = '画像形式が正しくありません。';
		$this['min_height'] = null;
		$this['max_height'] = null;
		$this['min_width'] = null;
		$this['max_width'] = null;
		return parent::initialize($params);
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
			if (BSString::isBlank($name = $value['tmp_name'])) {
				throw new BSImageException('ファイルが存在しない、又は正しくありません。');
			}
			$file = new BSImageFile($name);
			$image = $file->getRenderer();
		} catch (Exception $e) {
			$this->error = $this['types_error'];
			return false;
		}

		if (!$this->getAllowedTypes()->isContain($image->getType())) {
			$this->error = $this['types_error'];
		} else if ($this['min_width'] && ($image->getWidth() < $this['min_width'])) {
			$this->error = '幅が' . $this['min_width'] . 'ピクセルより不足しています。';
		} else if ($this['min_height'] && ($image->getHeight() < $this['min_height'])) {
			$this->error = '高さが' . $this['min_height'] . 'ピクセルより不足しています。';
		} else if ($this['max_width'] && ($this['max_width'] < $image->getWidth())) {
			$this->error = '幅が' . $this['max_width'] . 'ピクセルを超えています。';
		} else if ($this['max_height'] && ($this['max_height'] < $image->getHeight())) {
			$this->error = '高さが' . $this['max_height'] . 'ピクセルを超えています。';
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
