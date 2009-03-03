<?php
/**
 * Reporting abstraction class
 *
 * $Horde: ansel/lib/Report.php,v 1.9 2008-07-03 04:16:00 mrubinsk Exp $
 *
 * Copyright 2009 The Horde Project (http://www.horde.org)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * Copyright 2008 The Horde Project (http://www.horde.org)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Duck <duck@obala.net>
 * @package Folks
 */
class Folks_Report {

    var $_title = '';

    /**
     * Create instance
     */
    public static factory($driver = null, $params = array())
    {
        if ($driver === null) {
            $driver = $GLOBALS['conf']['report_content']['driver'];
        }

        if (empty($params)) {
            $params = $GLOBALS['conf']['report_content'];
        }

        require_once FOLKS_BASE . '/lib/Report/' . $driver  . '.php';
        $class_name = 'Folks_Report_' . $driver;
        if (!class_exists($class_name)) {
            return PEAR::RaiseError(_("Report driver does not exist."));
        }

        $report = new $class_name($params);

        return $report;
    }

    /**
     * Get reporting user email
     */
    public function getUserEmail()
    {
        return $this->_getUserEmail();
    }

    /**
     * Get user email
     */
    public function _getUserEmail($user = null)
    {
        require_once 'Horde/Identity.php';

        // Get user email
        $identity = &Identity::singleton('none', $user);
        return $identity->getValue('from_addr');
    }

    /**
     * Get scope admins
     */
    public function getAdmins()
    {
        $name = $GLOBALS['registry']->getApp() . ':admin';

        if ($GLOBALS['perms']->exists($name)) {
            $permission = $GLOBALS['perms']->getPermission($name);
            if ($permission instanceof PEAR_Error) {
                return $permission;
            }
            return $permission->getUserPermissions(PERM_DELETE);
        } else {
            return $GLOBALS['conf']['auth']['admins'];
        }
    }

    /**
     * Set title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Get report message title
     */
    public function getTitle()
    {
        if (empty($this->_title)) {
            return sprintf(_("Content abuse report in %s"), $GLOBALS['registry']->get('name'));
        } else {
            return $this->_title;
        }
    }

    /**
     * Get report message content
     */
    public function getMessage($message)
    {
        $message .=  "\n\n" . _("Report by user") . ': ' . Auth::getAuth()
                 . ' (' . $_SERVER['REMOTE_ADDR'] . ')';

        return $message;
    }

    /**
     * Report
     *
     * @param string $message to pass
     */
    public function report($message, $users = array())
    {
        return PEAR::raiseError(_("Unsupported"));
    }
}
