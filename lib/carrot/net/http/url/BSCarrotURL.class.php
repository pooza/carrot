<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.url
 */

/**
 * CarrotアプリケーションのURL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCarrotURL extends BSHTTPURL {
	private $module;
	private $action;
	private $id;

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return BSCarrotURL 自分自身
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'module':
			case 'module_name':
				return $this->setModuleName($value);
			case 'action':
			case 'action_name':
				return $this->setActionName($value);
			case 'record':
			case 'record_id':
				return $this->setRecordID($value);
		}
		return parent::setAttribute($name, $value);
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param mixed $contents URL
	 */
	public function setContents ($contents) {
		if (!BSString::isBlank($contents)) {
			if (is_string($contents)) {
				throw new BSNetException('BSCarrotURLは文字列から生成できません。');
			}
			if (is_array($contents) || ($contents instanceof BSParameterHolder)) {
				$contents = BSArray::create($contents);
			}
			if (BSString::isBlank($contents['module'])) {
				if (BSString::isBlank($contents['action'])) {
					$action = $this->controller->getAction();
					$contents['action'] = $action->getName();
					$contents['module'] = $action->getModule()->getName();
				} else {
					$contents['module'] = $this->controller->getModule();
				}
			}
		}
		parent::setContents($contents);
	}

	/**
	 * モジュール名を返す
	 *
	 * @access public
	 * @return string モジュール名
	 */
	public function getModuleName () {
		if (!$this->module) {
			$this->module = BS_MODULE_DEFAULT_MODULE;
		}
		return $this->module;
	}

	/**
	 * モジュール名を設定
	 *
	 * @access public
	 * @param mixed $module モジュール又はその名前
	 * @return BSCarrotURL 自分自身
	 */
	public function setModuleName ($module) {
		if ($module instanceof BSModule) {
			$this->module = $module->getName();
		} else {
			$this->module = $module;
		}
		$this->parsePath();
		return $this;
	}

	/**
	 * アクション名を返す
	 *
	 * @access public
	 * @return string アクション名
	 */
	public function getActionName () {
		if (!$this->action) {
			$this->action = BS_MODULE_DEFAULT_ACTION;
		}
		return $this->action;
	}

	/**
	 * アクション名を設定
	 *
	 * @access public
	 * @param mixed $action アクション又はその名前
	 * @return BSCarrotURL 自分自身
	 */
	public function setActionName ($action) {
		if ($action instanceof BSAction) {
			$this->module = $action->getModule()->getName();
			$this->action = $action->getName();
		} else {
			$this->action = $action;
		}
		$this->parsePath();
		return $this;
	}

	/**
	 * レコードのIDを返す
	 *
	 * @access public
	 * @return integer レコードのID
	 */
	public function getRecordID () {
		return $this->id;
	}

	/**
	 * レコードのIDを設定
	 *
	 * @access public
	 * @param mixed $id レコード又はそのID
	 * @return BSCarrotURL 自分自身
	 */
	public function setRecordID ($id) {
		if ($id instanceof BSRecord) {
			$this->id = $id->getID();
		} else {
			$this->id = $id;
		}
		$this->parsePath();
		return $this;
	}

	private function parsePath () {
		$path = BSArray::create();
		$path[] = null;
		$path[] = $this->getModuleName();
		$path[] = $this->getActionName();
		if ($id = $this->getRecordID()) {
			$path[] = $id;
		}

		// path属性をsetAttributeすると、queryやflagmentが初期化されてしまう。
		$this->attributes['path'] = $path->join('/');
	}
}

