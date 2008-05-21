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
class BSHTMLFragmentValidator extends BSValidator {
	private $allowedTags = array();

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('element_error', '許可されていない要素又は属性が含まれています。');
		$this->setParameter('allowed_tags', 'a,br,div,li,ol,p,span,ul');
		$this->setParameter('javascript_allowed', false);
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
			$body = preg_replace('/&(#[0-9]+|[a-z]+);/i', '', $value); //実体参照を無視
			$body = '<div>' . $body . '</div>';
			$element = new BSXMLElement;
			$element->setContents($body);
			if (!self::isValidElement($element)) {
				throw new BSXMLException($this->getParameter('element_error'));
			}
		} catch (BSXMLException $e) {
			$this->error = $e->getMessage();
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
			$this->allowedTags[] = 'div';
			$tags = $this->getParameter('allowed_tags');
			if (!is_array($tags)) {
				$tags = explode(',', $tags);
			}
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