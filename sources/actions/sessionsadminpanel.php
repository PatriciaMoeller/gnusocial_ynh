<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Sessions administration panel
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Settings
 * @package   StatusNet
 * @author    Zach Copley <zach@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

/**
 * Admin site sessions
 *
 * @category Admin
 * @package  StatusNet
 * @author   Zach Copley <zach@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 */
class SessionsadminpanelAction extends AdminPanelAction
{
    /**
     * Returns the page title
     *
     * @return string page title
     */
    function title()
    {
        // TRANS: Title for the sessions administration panel.
        return _m('TITLE','Sessions');
    }

    /**
     * Instructions for using this form.
     *
     * @return string instructions
     */
    function getInstructions()
    {
        // TRANS: Instructions for the sessions administration panel.
        return _('Session settings for this StatusNet site');
    }

    /**
     * Show the site admin panel form
     *
     * @return void
     */
    function showForm()
    {
        $form = new SessionsAdminPanelForm($this);
        $form->show();
        return;
    }

    /**
     * Save settings from the form
     *
     * @return void
     */
    function saveSettings()
    {
        static $booleans = array('sessions' => array('handle', 'debug'));

        $values = array();

        foreach ($booleans as $section => $parts) {
            foreach ($parts as $setting) {
                $values[$section][$setting] = ($this->boolean($setting)) ? 1 : 0;
            }
        }

        // This throws an exception on validation errors

        $this->validate($values);

        // assert(all values are valid);

        $config = new Config();

        $config->query('BEGIN');

        foreach ($booleans as $section => $parts) {
            foreach ($parts as $setting) {
                Config::save($section, $setting, $values[$section][$setting]);
            }
        }

        $config->query('COMMIT');

        return;
    }

    function validate(&$values)
    {
        // stub
    }
}

// @todo FIXME: Class documentation missing.
class SessionsAdminPanelForm extends AdminForm
{
    /**
     * ID of the form
     *
     * @return int ID of the form
     */
    function id()
    {
        return 'sessionsadminpanel';
    }

    /**
     * class of the form
     *
     * @return string class of the form
     */
    function formClass()
    {
        return 'form_settings';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action()
    {
        return common_local_url('sessionsadminpanel');
    }

    /**
     * Data elements of the form
     *
     * @return void
     */
    function formData()
    {
        $this->out->elementStart('fieldset', array('id' => 'settings_user_sessions'));
        // TRANS: Fieldset legend on the sessions administration panel.
        $this->out->element('legend', null, _m('LEGEND','Sessions'));

        $this->out->elementStart('ul', 'form_data');

        $this->li();
        // TRANS: Checkbox title on the sessions administration panel.
        // TRANS: Indicates if StatusNet should handle session administration.
        $this->out->checkbox('handle', _('Handle sessions'),
                              (bool) $this->value('handle', 'sessions'),
                              // TRANS: Checkbox title on the sessions administration panel.
                              // TRANS: Indicates if StatusNet should handle session administration.
                              _('Handle sessions ourselves.'));
        $this->unli();

        $this->li();
        // TRANS: Checkbox label on the sessions administration panel.
        // TRANS: Indicates if StatusNet should write session debugging output.
        $this->out->checkbox('debug', _('Session debugging'),
                              (bool) $this->value('debug', 'sessions'),
                              // TRANS: Checkbox title on the sessions administration panel.
                              _('Enable debugging output for sessions.'));
        $this->unli();

        $this->out->elementEnd('ul');

        $this->out->elementEnd('fieldset');
    }

    /**
     * Action elements
     *
     * @return void
     */
    function formActions()
    {
        $this->out->submit('submit',
                           // TRANS: Submit button text on the sessions administration panel.
                           _m('BUTTON','Save'),
                           'submit',
                           null,
                           // TRANS: Title for submit button on the sessions administration panel.
                           _('Save session settings'));
    }
}
