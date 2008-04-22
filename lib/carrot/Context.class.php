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
 * Context provides information about the current application context, such as
 * the module and action names and the module directory. References to the
 * current controller, request, and user implementation instances are also
 * provided.
 *
 * @package jp.co.b-shock.carrot
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     3.0.0
 * @version   $Id: Context.class.php 224 2008-04-22 00:24:33Z pooza $
 */
class Context
{

    // +-----------------------------------------------------------------------+
    // | PRIVATE VARIABLES                                                     |
    // +-----------------------------------------------------------------------+

    private
        $actionStack     = null,
        $controller      = null,
        $request         = null,
        $storage         = null,
        $user            = null;

    // +-----------------------------------------------------------------------+
    // | CONSTRUCTOR                                                           |
    // +-----------------------------------------------------------------------+

    /**
     * Class constructor.
     *
     * @param Controller      The current Controller implementation instance.
     * @param Request         The current Request implementation instance.
     * @param User            The current User implementation instance.
     * @param Storage         The current Storage implementation instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function __construct ($controller, $request, $user, $storage)
    {

        $this->actionStack     = $controller->getActionStack();
        $this->controller      = $controller;
        $this->request         = $request;
        $this->storage         = $storage;
        $this->user            = $user;

    }

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Retrieve the action name for this context.
     *
     * @return string The currently executing action name, if one is set,
     *                otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getActionName ()
    {

        // get the last action stack entry
        $actionEntry = $this->actionStack->getLastEntry();

        return $actionEntry->getActionName();

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the controller.
     *
     * @return Controller The current Controller implementation instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getController ()
    {

        return $this->controller;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the module directory for this context.
     *
     * @return string An absolute filesystem path to the directory of the
     *                currently executing module, if one is set, otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getModuleDirectory ()
    {

        // get the last action stack entry
        $actionEntry = $this->actionStack->getLastEntry();

        return MO_MODULE_DIR . '/' . $actionEntry->getModuleName();

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the module name for this context.
     *
     * @return string The currently executing module name, if one is set,
     *                otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getModuleName ()
    {

        // get the last action stack entry
        $actionEntry = $this->actionStack->getLastEntry();

        return $actionEntry->getModuleName();

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the request.
     *
     * @return Request The current Request implementation instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getRequest ()
    {

        return $this->request;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the storage.
     *
     * @return Storage The current Storage implementation instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getStorage ()
    {

        return $this->storage;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the user.
     *
     * @return User The current User implementation instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getUser ()
    {

        return $this->user;

    }

}

?>