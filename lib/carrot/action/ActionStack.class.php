<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Mojavi package.                                  |
// | Copyright (c) 2003, 2004 Sean Kerr.                                       |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.mojavi.org.                             |
// +---------------------------------------------------------------------------+

/**
 * ActionStack keeps a list of all requested actions and provides accessor
 * methods for retrieving individual entries.
 *
 * @package jp.co.b-shock.carrot
 * @subpackage action
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     3.0.0
 * @version   $Id$
 */
class ActionStack
{

    // +-----------------------------------------------------------------------+
    // | PRIVATE VARIABLES                                                     |
    // +-----------------------------------------------------------------------+

    private $stack;
	private static $instance;

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->stack = new BSArray;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return ActionStack インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new ActionStack();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

    /**
     * Add an entry.
     *
     * @param BSAction An action implementation instance.
     *
     * @return void
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function addEntry (BSAction $action)
    {

        $this->stack[] = $action;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the entry at a specific index.
     *
     * @param int An entry index.
     *
     * @return BSAction An action stack entry implementation.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getEntry ($index)
    {

        return $this->stack[$index];

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the first entry.
     *
     * @return BSAction An action stack entry implementation.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getFirstEntry ()
    {

        return $this->stack[0];

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the last entry.
     *
     * @return BSAction An action stack entry implementation.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getLastEntry ()
    {

        return $this->stack[count($this->stack) - 1];

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the size of this stack.
     *
     * @return int The size of this stack.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getSize ()
    {

        return count($this->stack);

    }

}

?>
