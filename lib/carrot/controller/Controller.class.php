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
        $maxForwards     = 20,
        $securityFilter  = null;

    private static
        $instance = null;

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

	public function __get ($name) {
		switch ($name) {
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
		}
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

        // replace unwanted characters
        $moduleName = preg_replace('/[^a-z0-9\-_]+/i', '', $moduleName);
        $actionName = preg_replace('/[^a-z0-9\-_\/]+/i', '', $actionName);

        if (ActionStack::getInstance()->getSize() >= $this->maxForwards)
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

        }

        // create an instance of the action
        try {
            $module = BSModule::getInstance($moduleName);
            $action = $module->getAction($actionName);
        } catch (BSFileException $e) {
            $module = BSModule::getInstance(MO_ERROR_404_MODULE);
            $action = $module->getAction(MO_ERROR_404_ACTION);
        }

        // add a new action stack entry
        ActionStack::getInstance()->addEntry($action);

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
            if ($action->initialize())
            {

                // create a new filter chain
                $filterChain = new FilterChain();

                if (MO_AVAILABLE)
                {

                    // the application is available so we'll register
                    // global and module filters, otherwise skip them

                    // does this action require security?
                    if ($action->isSecure())
                    {

                        // register security filter
                        $filterChain->register($this->securityFilter);

                    }

                    // load filters
                    $this->loadFilters($filterChain);
                    $module->loadFilters($filterChain);

                }

                // register the execution filter
                $execFilter = new ExecutionFilter();

                $execFilter->initialize();
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

            $this->forward($moduleName, $actionName);

        }

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

        BSSessionStorage::getInstance()->initialize();
        $this->request->initialize();
        $this->user->initialize();
        $this->securityFilter = new BSSecurityFilter();
        $this->securityFilter->initialize();

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
    private function loadFilters ($filterChain)
    {

			$filters = array();
			require_once(ConfigCache::checkConfig('config/filters.ini'));
			if ($filters) {
				foreach ($filters as $filter) {
					$filterChain->register($filter);
				}
			}

    }

}

?>
