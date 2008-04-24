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
 * Validator allows you to apply constraints to user entered parameters.
 *
 * @package    mojavi
 * @subpackage validator
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     1.0.0
 * @version   $Id$
 */
abstract class Validator extends ParameterHolder
{

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Execute this validator.
     *
     * @param mixed A file or parameter value/array.
     * @param string An error message reference.
     *
     * @return bool true, if this validator executes successfully, otherwise
     *              false.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    abstract function execute (&$value, &$error);

    // -------------------------------------------------------------------------

    /**
     * Initialize this validator.
     *
     * @param array   An associative array of initialization parameters.
     *
     * @return bool true, if initialization completes successfully, otherwise
     *              false.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function initialize ($parameters = null)
    {

        if ($parameters != null)
        {

            $this->parameters = array_merge($this->parameters, $parameters);

        }

        return true;

    }

}

?>