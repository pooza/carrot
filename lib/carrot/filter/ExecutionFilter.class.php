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
 * ExecutionFilter is the last filter registered for each filter chain. This
 * filter does all action and view execution.
 *
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     1.0.0
 * @version   $Id$
 */
class ExecutionFilter extends BSFilter
{

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Execute this filter.
     *
     * @param FilterChain The filter chain.
     *
     * @return void
     *
     * @throws <b>InitializeException</b> If an error occurs during view
     *                                    initialization.
     * @throws <b>ViewException</b>       If an error occurs while executing
     *                                    the view.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  1.0.0
     */
    public function execute (FilterChain $filterChain)
    {

        // get the current action instance
        $actionEntry    = BSController::getInstance()->getActionStack()->getLastEntry();
        $actionInstance = $actionEntry->getActionInstance();

        // get the current action information
        $moduleName = BSController::getInstance()->getModuleName();
        $actionName = BSController::getInstance()->getActionName();

        // get the request method
        $method = BSRequest::getInstance()->getMethod();

        if (($actionInstance->getRequestMethods() & $method) != $method)
        {

            // this action will skip validation/execution for this method
            // get the default view
            $viewName = $actionInstance->getDefaultView();

        } else
        {

            // set default validated status
            $validated = true;

            // get the current action validation configuration
            $validationConfig = MO_MODULE_DIR . '/' . $moduleName .
                                '/validate/' . $actionName . '.ini';

            if (is_readable($validationConfig))
            {

                // load validation configuration
                // do NOT use require_once
                $validationConfig = 'modules/' . $moduleName .
                                    '/validate/' . $actionName . '.ini';

                require(ConfigCache::checkConfig($validationConfig));

            }

            // manually load validators
            $actionInstance->registerValidators(ValidatorManager::getInstance());

            // process validators
            $validated = ValidatorManager::getInstance()->execute();

            // process manual validation
            if ($validated && $actionInstance->validate())
            {

                // execute the action
                $viewName = $actionInstance->execute();

            } else
            {

                // validation failed
                $viewName = $actionInstance->handleError();

            }

        }

        if ($viewName != BSView::NONE)
        {

            if (is_array($viewName))
            {

                // we're going to use an entirely different action for this view
                $moduleName = $viewName[0];
                $viewName   = $viewName[1];

            } else
            {

                // use a view related to this action
                $viewName = $actionName . $viewName;

            }

            // display this view
            if (!BSController::getInstance()->viewExists($moduleName, $viewName))
            {

                // the requested view doesn't exist
                $file = MO_MODULE_DIR . '/' . $moduleName . '/views/' .
                        $viewName . 'View.class.php';

                $error = 'Module "%s" does not contain the view "%sView" or ' .
                         'the file "%s" is unreadable';

                $error = sprintf($error, $moduleName, $viewName, $file);

                throw new ViewException($error);

            }

            // get the view instance
            $viewInstance = BSController::getInstance()->getView($moduleName, $viewName);

            // initialize the view
            if ($viewInstance->initialize())
            {

                // view initialization completed successfully
                $viewInstance->execute();

                // render the view and if data is returned, stick it in the
                // action entry which was retrieved from the execution chain
                $viewData = $viewInstance->render();

            } else
            {

                // view failed to initialize
                $error = 'View initialization failed for module "%s", ' .
                         'view "%sView"';
                $error = sprintf($error, $moduleName, $viewName);

                throw new InitializationException($error);

            }

        }

    }

}

?>