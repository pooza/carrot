<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * バリデータ設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSValidatorConfigCompiler extends BSConfigCompiler {
	private $fields;
	private $validators;

	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$config = $file->getResult();

		$this->setMethods($config['methods']);
		$this->setNames($config['names']);
		$this->setValidators($config);

		$this->putLine('$manager = BSValidatorManager::getInstance();');
		foreach (array('POST', 'GET') as $method) {
			$this->putMethod($method);
		}
		return $this->getBody();
	}

	private function setMethods ($methods) {
		$this->fields = new BSArray;
		foreach ($methods as $method => $fields) {
			$method = strtoupper($method);
			$this->fields[$method] = new BSArray;
			foreach (explode(',', $fields) as $field) {
				if ($field) {
					$this->fields[$method][$field] = new BSArray;
					$this->fields[$method][$field]['validators'] = new BSArray;
				}
			}
		}
	}

	private function setNames ($names) {
		$this->validators = new BSArray;
		foreach (array('POST', 'GET') as $method) {
			foreach ($names as $name => $value) {
				$name = explode('.', $name);
				$field = $name[0];
				$param = $name[1];
				if ($this->fields[$method][$field]) {
					if ($param == 'validators') {
						foreach (explode(',', $value) as $validator) {
							$this->fields[$method][$field]['validators'][] = $validator;
							$this->validators[$validator] = new BSArray;
							$this->validators[$validator]['params'] = new BSArray;
						}
					} else {
						$this->fields[$method][$field][$param] = $value;
					}
				}
			}
		}
	}

	private function setValidators ($config) {
		foreach ($this->validators as $validator => $values) {
			foreach ($config[$validator] as $param => $value) {
				$this->validators[$validator][$param] = $value;
			}
		}
	}

	private function putMethod ($method) {
		$line = sprintf(
			'if ($_SERVER[%s] == %s) {',
			self::quote('REQUEST_METHOD'),
			self::quote($method)
		);
		$this->putLine($line);

		foreach ($this->fields[$method] as $name => $field) {
			$this->putField($name, $field);
		}

		$this->putLine('}');
	}

	private function putField ($name, $field) {
		$line = sprintf(
			'  $manager->register(%s, %s, %s, %s);',
			self::quote($name),
			self::quote($field['required']),
			self::quote($field['required_msg']),
			self::quote($field['file'])
		);
		$this->putLine($line);

		foreach ($field['validators'] as $validator) {
			$this->putValidator($name, $validator);
		}
	}

	private function putValidator ($name, $validator) {
		$line = sprintf('  $validator = new %s;', $this->validators[$validator]['class']);
		$this->putLine($line);

		$line = sprintf(
			'  $validator->initialize(%s);',
			self::parseParameters($this->validators[$validator])
		);
		$this->putLine($line);

		$line = sprintf('  $manager->registerValidator(%s, $validator);', self::quote($name));
		$this->putLine($line);
	}
}

/* vim:set tabstop=4 ai: */
?>