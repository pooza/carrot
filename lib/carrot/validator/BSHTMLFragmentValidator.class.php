<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * HTMLフラグメントバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHTMLFragmentValidator extends Validator {
	private $allowedTags = array();

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 */
	public function execute (&$value, &$error) {
		try {
			$element = new BSXMLElement();
			$element->setContents('<div>' . $value . '</div>');
			if (!self::isValidElement($element)) {
				throw new BSXMLException('許可されていない要素又は属性が含まれています。');
			}
		} catch (BSXMLException $e) {
			$error = $e->getMessage();
			return false;
		}
		return true;
	}

	/**
	 * 許可された要素と属性だけで構成されているか
	 *
	 * @access private
	 * @param BSXMLElement $element 評価対象のフラグメント
	 * @return boolean 問題なしならTrue
	 */
	private function isValidElement (BSXMLElement $element) {
		if (0 < count($element->getElements())) {
			foreach ($element as $child) {
				if (!self::isValidElement($child)) {
					return false;
				}
			}
		} else {
			if (!in_array($element->getName(), $this->getAllowedTags())) {
				return false;
			}
			if (!$this->isJavaScriptAllowed()) {
				foreach ($element->getAttributes() as $name => $value) {
					if (preg_match('/^on/i', $name) || preg_match('/javascript:/i', $value)) {
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * 許可された要素名を配列で帰す
	 *
	 * @access private
	 * @return string[] 許可された要素名の配列
	 */
	private function getAllowedTags () {
		if (!$this->allowedTags) {
			$tags = explode(',', $this->getParameter('allowed_tags'));
			foreach ($tags as $tag) {
				if ($tag) {
					$this->allowedTags[] = strtolower($tag);
				}
			}
		}
		return $this->allowedTags;
	}

	/**
	 * JavaScriptは許可されているか？
	 *
	 * @access private
	 * @return boolean 許可されているならTrue
	 */
	private function isJavaScriptAllowed () {
		return ($this->getParameter('javascript_allowed') == true);
	}
}

/* vim:set tabstop=4 ai: */
?>