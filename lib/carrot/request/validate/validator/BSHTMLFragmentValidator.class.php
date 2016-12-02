<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * HTMLフラグメントバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTMLFragmentValidator extends BSValidator {
	private $allowedTags;
	private $invalidNode;

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['element_error'] = '許可されていない要素又は属性が含まれています。';
		$this['allowed_tags'] = 'a,br,div,li,ol,p,span,ul';
		$this['javascript_allowed'] = false;
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
			$body = str_replace('&', '', $value); //実体参照を無視
			$body = '<div>' . $body . '</div>';
			$element = new BSDivisionElement;
			$element->setContents($body);
			if (!self::isValidElement($element)) {
				$message = new BSStringFormat('%s (%s)');
				$message[] = $this['element_error'];
				$message[] = $this->invalidNode;
				throw new BSXMLException($message);
			}
		} catch (BSXMLException $e) {
			$this->error = $e->getMessage();
			return false;
		}
		return true;
	}

	private function isValidElement (BSXMLElement $element) {
		if (!!$element->getElements()->count()) {
			foreach ($element as $child) {
				if (!self::isValidElement($child)) {
					return false;
				}
			}
		}

		$tags = $this->getAllowedTags();
		if (!!$tags->count() && !$tags->isContain($element->getName())) {
			$this->invalidNode = $element->getName() . '要素';
			return false;
		}
		if (!$this->isJavaScriptAllowed()) {
			if (BSString::toLower($element->getName()) == 'script') {
				$this->invalidNode = $element->getName() . '要素';
				return false;
			}
			foreach ($element->getAttributes() as $name => $value) {
				if (mb_eregi('^on', $name) || mb_eregi('javascript:', $value)) {
					$this->invalidNode = sprintf('%s要素/%s属性', $element->getName(), $name);
					return false;
				}
			}
		}

		return true;
	}

	private function getAllowedTags () {
		if (!$this->allowedTags) {
			$this->allowedTags = new BSArray;
			if ($tags = $this['allowed_tags']) {
				$this->allowedTags[] = 'div';
				$this->allowedTags[] = 'span';
				if (BS_VIEW_HTML5) {
					$this->allowedTags[] = 'nav';
					$this->allowedTags[] = 'section';
					$this->allowedTags[] = 'article';
				}

				if (!is_array($tags)) {
					$tags = BSString::explode(',', $tags);
				}
				$this->allowedTags->merge($tags);
				$this->allowedTags->trim();
				$this->allowedTags->uniquize();
			}
		}
		return $this->allowedTags;
	}

	private function isJavaScriptAllowed () {
		return !!$this['javascript_allowed'];
	}
}

/* vim:set tabstop=4: */
