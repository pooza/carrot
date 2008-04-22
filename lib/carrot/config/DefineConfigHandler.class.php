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
 * DefineConfigHandler allows you to turn ini categories and key/value pairs
 * into defined PHP values.
 *
 * <b>Optional initialization parameters:</b>
 *
 * # <b>prefix</b> - The text prepended to all defined constant names.
 *
 * @package jp.co.b-shock.carrot
 * @subpackage config
 *
 * @author    Sean Kerr (skerr@mojavi.org)
 * @copyright (c) Sean Kerr, {@link http://www.mojavi.org}
 * @since     3.0.0
 * @version   $Id$
 */
class DefineConfigHandler extends BSConfigHandler
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

        // get our prefix
        $prefix = $this->getParameter('prefix');

        if ($prefix == null)
        {

            // no prefix has been specified
            $prefix = '';

        }

        // init our data array
        $data = array();

        // let's do our fancy work
        foreach ($ini as $category => &$keys)
        {

            // categories starting without a period will be prepended to the key
            if ($category{0} != '.')
            {

                $category = $prefix . $category . '_';

            } else
            {

                $category = $prefix;

            }

            // loop through all key/value pairs
            foreach ($keys as $key => &$value)
            {

                // prefix the key
                $key = $category . $key;

                // replace constant values
                $value = parent::replaceConstants($value);

                // literalize our value
                $value = parent::literalize($value);

                // append new data
                $tmp    = "define('%s', %s);";
                $data[] = sprintf($tmp, $key, $value);

            }

        }

        // compile data
        $retval = "<?php\n" .
                  "// auth-generated by DefineConfigHandler\n" .
                  "// date: %s\n%s\n?>";
        $retval = sprintf($retval, date('Y/m/d H:i:s'), implode("\n", $data));

        return $retval;

    }

}

?>