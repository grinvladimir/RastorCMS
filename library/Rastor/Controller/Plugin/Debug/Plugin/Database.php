<?php

/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 * @version    $Id: Database.php 74 2009-05-19 12:30:36Z gugakfugl $
 */
/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class Rastor_Controller_Plugin_Debug_Plugin_Database extends Rastor_Controller_Plugin_Debug_Plugin implements Rastor_Controller_Plugin_Debug_Plugin_Interface {

    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'database';

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->_identifier;
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab() {

        $profiler = Zend_Db_Table::getDefaultAdapter()->getProfiler();
        $adapterInfo[] = $profiler->getTotalNumQueries() . ' in ' . round($profiler->getTotalElapsedSecs() * 1000, 2) . ' ms';
        $html = implode(' / ', $adapterInfo);

        return $html;
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel() {
        $html = '<h4>Database queries</h4>';
        if (Zend_Db_Table_Abstract::getDefaultMetadataCache()) {
            $html .= 'Metadata cache is ENABLED';
        } else {
            $html .= 'Metadata cache is DISABLED';
        }

        if ($profiles = Zend_Db_Table::getDefaultAdapter()->getProfiler()->getQueryProfiles()) {
            $html .= '<h4>Adapter ' . $name . '</h4><ol>';
            foreach ($profiles as $profile) {
                $html .= '<li><strong>[' . round($profile->getElapsedSecs() * 1000, 2) . ' ms]</strong> '
                        . htmlspecialchars($profile->getQuery()) . '</li>';
            }
            $html .= '</ol>';
        }

        return $html;
    }

}