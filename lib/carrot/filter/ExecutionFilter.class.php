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
        $action = BSActionStack::getInstance()->getLastEntry();

        // get the current action information
        $moduleName = BSController::getInstance()->getModule()->getName();
        $actionName = BSActionStack::getInstance()->getLastEntry()->getName();

        // get the request method
        $method = BSRequest::getInstance()->getMethod();

        if (($action->getRequestMethods() & $method) != $method)
        {

            // this action will skip validation/execution for this method
            // get the default view
            $viewName = $action->getDefaultView();

        } else
        {

            // set default validated status
            $validated = true;

            // get the current action validation configuration
            $validationConfig = MO_MODULE_DIR . '/' . $moduleName .
                                '/validate/' . $actionName . '.ini';

            if (is_readable($validationConfig))
            {

                require_once(BSConfigManager::getInstance()->compile($validationConfig));

            }

            // manually load validators
            $action->registerValidators(ValidatorManager::getInstance());

            // process validators
            $validated = ValidatorManager::getInstance()->execute();

            // process manual validation
            if ($validated && $action->validate())
            {

                // execute the action
                $viewName = $action->execute();

            } else
            {

                // validation failed
                $viewName = $action->handleError();

            }

        }

        if ($viewName != BSView::NONE)
        {

            // get the view instance
            $view = $action->getView($viewName);

            // initialize the view
            if ($view->initialize())
            {

                // view initialization completed successfully
                $view->execute();

                // render the view and if data is returned, stick it in the
                // action entry which was retrieved from the execution chain
                $view->render();

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