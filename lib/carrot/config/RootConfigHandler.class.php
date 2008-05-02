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
 * RootConfigHandler allows you to specify configuration handlers for the
 * application or on a module level.
 *
 * @package jp.co.b-shock.carrot
 * @subpackage config
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     3.0.0
 * @version   $Id$
 */
class RootConfigHandler extends BSConfigHandler
{

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Execute this configuration handler.
     *
     * @param string An absolute filesystem path to a configuration file.
     *
     * @return string Data to be written to a cache file.
     *
     * @throws <b>ConfigurationException</b> If a requested configuration file
     *                                       does not exist or is not readable.
     * @throws <b>ParseException</b> If a requested configuration file is
     *                               improperly formatted.
     *
     * @author Sean Kerr (skerr@mojavi.org)
     * @since  3.0.0
     */
    public function execute ($config)
    {

        // parse the ini
        $ini = $this->parseIni($config);

        // determine if we're loading the system config_handlers.ini or a module
        // config_handlers.ini
        $moduleLevel = ($this->getParameter('module_level') === true)
                       ? true : false;

        if ($moduleLevel)
        {

            // get the current module name
            $moduleName = $this->getParameter('module_name');

        }

        // init our data and includes arrays
        $data     = array();
        $includes = array();

        // let's do our fancy work
        foreach ($ini as $category => &$keys)
        {

            if ($moduleLevel)
            {

                // module-level registration, so we must prepend the module
                // root to the category
                $category = 'modules/' . $moduleName . '/' .
                            $category;

            }

            if (!isset($keys['class']))
            {

                // missing class key
                $error = 'Configuration file "%s" specifies category ' .
                         '"%s" with missing class key';
                $error = sprintf($error, $config, $category);

                throw new ParseException($error);

            }

            $class =& $keys['class'];

            if (isset($keys['file']))
            {

                // we have a file to include
                $file =& $keys['file'];
                $file =  parent::replaceConstants($file);
                $file =  parent::replacePath($file);

                if (!is_readable($file))
                {

                    // handler file doesn't exist
                    $error = 'Configuration file "%s" specifies class "%s" ' .
                             'with nonexistent or unreadable file "%s"';
                    $error = sprintf($error, $config, $class, $file);

                    throw new ParseException($error);

                }

                // append our data
                $tmp        = "require_once('%s');";
                $includes[] = sprintf($tmp, $file);

            }

            // parse parameters
            $parameters = BSConfigHandler::parseParameters($keys);

            // append new data
            $tmp    = "self::\$handlers['%s'] = new %s();";
            $data[] = sprintf($tmp, $category, $class);

            if ($parameters != 'null')
            {

                // since we have parameters we will need to init the handler
                $tmp    = "self::\$handlers['%s']->initialize(%s);";
                $data[] = sprintf($tmp, $category, $parameters);

            }

        }

        // compile data
        $retval = "<?php\n" .
                  "// auth-generated by RootConfigHandler\n" .
                  "// date: %s\n%s\n%s\n?>";

        $retval = sprintf($retval, date('Y/m/d H:i:s'),
                          implode("\n", $includes), implode("\n", $data));

        return $retval;

    }

}

?>