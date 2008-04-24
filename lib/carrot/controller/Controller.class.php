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
 * Controller directs application flow.
 *
 * @package jp.co.b-shock.carrot
 * @subpackage controller
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     1.0.0
 * @version   $Id$
 */
abstract class Controller
{

    // +-----------------------------------------------------------------------+
    // | PROTECTED VARIABLES                                                   |
    // +-----------------------------------------------------------------------+

    protected
        $actionStack     = null,
        $context         = null,
        $maxForwards     = 20,
        $renderMode      = BSView::RENDER_CLIENT,
        $request         = null,
        $securityFilter  = null,
        $storage         = null,
        $user            = null;

    private static
        $instance = null;

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Indicates whether or not a module has a specific action.
     *
     * @param string A module name.
     * @param string An action name.
     *
     * @return bool true, if the action exists, otherwise false.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  1.0.0
     */
    public function actionExists ($moduleName, $actionName)
    {

        $file = MO_MODULE_DIR . '/' . $moduleName . '/actions/' . $actionName .
                'Action.class.php';

        return is_readable($file);

    }

    // -------------------------------------------------------------------------

    /**
     * Forward the request to another action.
     *
     * @param string A module name.
     * @param string An action name.
     *
     * @return void
     *
     * @throws <b>ConfigurationException</b> If an invalid configuration setting
     *                                       has been found.
     * @throws <b>ForwardException</b> If an error occurs while forwarding the
     *                                 request.
     * @throws <b>InitializationException</b> If the action could not be
     *                                        initialized.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  1.0.0
     */
    public function forward ($moduleName, $actionName)
    {

        // replace periods with slashes for action sub-directories
        $actionName = str_replace('.', '/', $actionName);

        // replace unwanted characters
        $moduleName = preg_replace('/[^a-z0-9\-_]+/i', '', $moduleName);
        $actionName = preg_replace('/[^a-z0-9\-_\/]+/i', '', $actionName);

        if ($this->actionStack->getSize() >= $this->maxForwards)
        {

            // let's kill this party before it turns into cpu cycle hell
            $error = 'Too many forwards have been detected for this request';

            throw new ForwardException($error);

        }

        if (!MO_AVAILABLE)
        {

            // application is unavailable
            $moduleName = MO_UNAVAILABLE_MODULE;
            $actionName = MO_UNAVAILABLE_ACTION;

            if (!$this->actionExists($moduleName, $actionName))
            {

                // cannot find unavailable module/action
                $error = 'Invalid configuration settings: ' .
                         'MO_UNAVAILABLE_MODULE "%s", ' .
                         'MO_UNAVAILABLE_ACTION "%s"';

                $error = sprintf($error, $moduleName, $actionName);

                throw new ConfigurationException($error);

            }

        } else if (!$this->actionExists($moduleName, $actionName))
        {

            // the requested action doesn't exist

            // track the requested module so we have access to the data
            // in the error 404 page
            $this->request->setAttribute('requested_action', $actionName);
            $this->request->setAttribute('requested_module', $moduleName);

            // switch to error 404 action
            $moduleName = MO_ERROR_404_MODULE;
            $actionName = MO_ERROR_404_ACTION;

            if (!$this->actionExists($moduleName, $actionName))
            {

                // cannot find unavailable module/action
                $error = 'Invalid configuration settings: ' .
                         'MO_ERROR_404_MODULE "%s", ' .
                         'MO_ERROR_404_ACTION "%s"';

                $error = sprintf($error, $moduleName, $actionName);

                throw new ConfigurationException($error);

            }

        }

        // create an instance of the action
        $actionInstance = $this->getAction($moduleName, $actionName);

        // add a new action stack entry
        $this->actionStack->addEntry($moduleName, $actionName,
                                     $actionInstance);

        // include the module configuration
        ConfigCache::import('modules/' . $moduleName . '/config/module.ini');

        if (constant('MOD_' . strtoupper($moduleName) . '_ENABLED'))
        {

            // module is enabled

            // check for a module config.php
            $moduleConfig = MO_MODULE_DIR . '/' . $moduleName . '/config.php';

            if (is_readable($moduleConfig))
            {

                require_once($moduleConfig);

            }

            // initialize the action
            if ($actionInstance->initialize($this->context))
            {

                // create a new filter chain
                $filterChain = new FilterChain();

                if (MO_AVAILABLE)
                {

                    // the application is available so we'll register
                    // global and module filters, otherwise skip them

                    // does this action require security?
                    if ($actionInstance->isSecure())
                    {

                        // register security filter
                        $filterChain->register($this->securityFilter);

                    }

                    // load filters
                    $this->loadGlobalFilters($filterChain);
                    $this->loadModuleFilters($filterChain);

                }

                // register the execution filter
                $execFilter = new ExecutionFilter();

                $execFilter->initialize($this->context);
                $filterChain->register($execFilter);

                if ($moduleName == MO_ERROR_404_MODULE &&
                    $actionName == MO_ERROR_404_ACTION)
                {

                    header('HTTP/1.0 404 Not Found');
                    header('Status: 404 Not Found');
                                                
                }
                
                // process the filter chain
                $filterChain->execute();

            } else
            {

                // action failed to initialize
                $error = 'Action initialization failed for module "%s", ' .
                         'action "%s"';

                $error = sprintf($error, $moduleName, $actionName);

                throw new InitializationException($error);

            }

        } else
        {

            // module is disabled
            $moduleName = MO_MODULE_DISABLED_MODULE;
            $actionName = MO_MODULE_DISABLED_ACTION;

            if (!$this->actionExists($moduleName, $actionName))
            {

                // cannot find mod disabled module/action
                $error = 'Invalid configuration settings: ' .
                         'MO_MODULE_DISABLED_MODULE "%s", ' .
                         'MO_MODULE_DISABLED_ACTION "%s"';

                $error = sprintf($error, $moduleName, $actionName);

                throw new ConfigurationException($error);

            }

            $this->forward($moduleName, $actionName);

        }

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve an Action implementation instance.
     *
     * @param string A module name.
     * @param string An action name.
     *
     * @return Action An Action implementation instance, if the action exists,
     *                otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getAction ($moduleName, $actionName)
    {

        $file = MO_MODULE_DIR . '/' . $moduleName . '/actions/' . $actionName .
                'Action.class.php';

        require_once($file);

        $position = strrpos($actionName, '/');

        if ($position > -1)
        {

            $actionName = substr($actionName, $position + 1);

        }

        $class = $actionName . 'Action';

        // fix for same name classes
        $moduleClass = $moduleName . '_' . $class;

        if (class_exists($moduleClass, false))
        {

            $class = $moduleClass;

        }

        return new $class();

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the action stack.
     *
     * @return ActionStack An ActionStack instance, if the action stack is
     *                     enabled, otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getActionStack ()
    {

        return $this->actionStack;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the current application context.
     *
     * @return Context A Context instance.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getContext ()
    {

        return $this->context;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve the presentation rendering mode.
     *
     * @return int One of the following:
     *             - BSView::RENDER_CLIENT
     *             - BSView::RENDER_VAR
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  1.0.0
     */
    public function getRenderMode ()
    {

        return $this->renderMode;

    }

    // -------------------------------------------------------------------------

    /**
     * Retrieve a BSView implementation instance.
     *
     * @param string A module name.
     * @param string A view name.
     *
     * @return BSView A BSView implementation instance, if the model exists,
     *              otherwise null.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function getView ($moduleName, $viewName)
    {

        $file = MO_MODULE_DIR . '/' . $moduleName . '/views/' . $viewName .
                'View.class.php';

        require_once($file);

        $position = strrpos($viewName, '/');

        if ($position > -1)
        {

            $viewName = substr($viewName, $position + 1);

        }

        $class = $viewName . 'View';

        // fix for same name classes
        $moduleClass = $moduleName . '_' . $class;

        if (class_exists($moduleClass, false))
        {

            $class = $moduleClass;

        }

        return new $class();

    }

    // -------------------------------------------------------------------------

    /**
     * Initialize this controller.
     *
     * @return void
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    protected function initialize ()
    {

        $this->actionStack = new ActionStack();
        $this->storage = BSSessionStorage::getInstance();
        $this->user = new BSUser();
        $this->context = new Context($this, $this->request, $this->user, $this->storage);
        $this->storage->initialize($this->context, null);
        $this->request->initialize($this->context, null);
        $this->user->initialize($this->context, null);
        $this->securityFilter = new BSSecurityFilter();
        $this->securityFilter->initialize($this->context, null);

        register_shutdown_function(array($this, 'shutdown'));
    }

    // -------------------------------------------------------------------------

    /**
     * Load global filters.
     *
     * @param FilterChain A FilterChain instance.
     *
     * @return void
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    private function loadGlobalFilters ($filterChain)
    {

        static $list = array();

        // grab our global filter ini and preset the module name
        $config     = MO_CONFIG_DIR . '/filters.ini';
        $moduleName = 'global';

        if (!isset($list[$moduleName]) && is_readable($config))
        {

            // load global filters
            require_once(ConfigCache::checkConfig('config/filters.ini'));

        }

        // register filters
        foreach ($list[$moduleName] as $filter)
        {

            $filterChain->register($filter);

        }

    }

    // -------------------------------------------------------------------------

    /**
     * Load module filters.
     *
     * @param FilterChain A FilterChain instance.
     *
     * @return void
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    private function loadModuleFilters ($filterChain)
    {

        // filter list cache file
        static $list = array();

        // get the module name
        $moduleName = $this->context->getModuleName();

        if (!isset($list[$moduleName]))
        {

            // we haven't loaded a filter list for this module yet
            $config = MO_MODULE_DIR . '/' . $moduleName . '/config/filters.ini';

            if (is_readable($config))
            {

                require_once(ConfigCache::checkConfig($config));

            } else
            {

                // add an emptry array for this module since no filters
                // exist
                $list[$moduleName] = array();

            }

        }

        // register filters
        foreach ($list[$moduleName] as $filter)
        {

            $filterChain->register($filter);

        }

    }

    // -------------------------------------------------------------------------

    /**
     * Indicates whether or not a module exists.
     *
     * @param string A module name.
     *
     * @return bool true, if the module exists, otherwise false.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  1.0.0
     */
    public function moduleExists ($moduleName)
    {

        $file = MO_MODULE_DIR . '/' . $moduleName . '/config/module.ini';

        return is_readable($file);

    }

    // -------------------------------------------------------------------------

    /**
     * Set the presentation rendering mode.
     *
     * @param int A rendering mode.
     *
     * @return void
     *
     * @throws <b>RenderException</b> - If an invalid render mode has been set.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  2.0.0
     */
    public function setRenderMode ($mode)
    {

        if ($mode == BSView::RENDER_CLIENT || $mode == BSView::RENDER_VAR ||
            $mode == BSView::RENDER_NONE)
        {

            $this->renderMode = $mode;

            return;

        }

        // invalid rendering mode type
        $error = 'Invalid rendering mode: %s';
        $error = sprintf($error, $mode);

        throw new RenderException($error);

    }

    // -------------------------------------------------------------------------

    /**
     * Execute the shutdown procedure.
     *
     * @return void
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function shutdown ()
    {

        $this->user->shutdown();

        session_write_close();

        $this->storage->shutdown();
        $this->request->shutdown();

    }

    // -------------------------------------------------------------------------

    /**
     * Indicates whether or not a module has a specific view.
     *
     * @param string A module name.
     * @param string A view name.
     *
     * @return bool true, if the view exists, otherwise false.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function viewExists ($moduleName, $viewName)
    {

        $file = MO_MODULE_DIR . '/' . $moduleName . '/views/' . $viewName .
                'View.class.php';

        return is_readable($file);

    }

}

?>
