<?php

/**
 * Joomla Add project View.
 *
 * @category   Apps
 * @package    Joomla
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link    http://www.clearfoundation.com/docs/developer/apps/joomla/
 */

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('joomla');

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('joomla/addproject');
echo form_header(lang('joomla_add_project'));
echo field_input('folder_name', '', lang('joomla_folder_name'));
echo field_dropdown('use_exisiting_database', array('Yes' => lang('joomla_select_yes'), 'No' => lang('joomla_select_no')), 'No', lang('joomla_use_existing_database'));
echo field_input('database_name', '', lang('joomla_database_name'));
echo field_input('database_user_name', 'testuser', lang('joomla_database_username'));
echo field_password('database_user_password', '', lang('joomla_database_password'));
echo field_input('root_username', 'root', lang('joomla_mysql_root_username'));
echo field_password('root_password', '', lang('joomla_mysql_root_password'));
echo field_dropdown('joomla_version', $versions, $default_version, lang('joomla_joomla_version'));
echo field_button_set(
    array(
    	anchor_cancel('/app/joomla'),
    	form_submit_add('submit', 'high')
    )
);
echo form_footer();
echo form_close();

?>