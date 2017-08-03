<?php

/**
 * Backup controller.
 *
 * @category   Apps
 * @package    Joomla
 * @subpackage Controller
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link    http://www.clearfoundation.com/docs/developer/apps/wordpress/
 */


///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Joomla controller.
 *
 * @category   Apps
 * @package    Joomla
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link    http://www.clearfoundation.com/docs/developer/apps/joomla/
 */

class Backup extends ClearOS_Controller
{
    /**
     * Joomla Backup controller.
     *
     * @return view
     */
    function index()
    {
		// Load libraries
    	//---------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		$data['backups'] = $this->joomla->get_backup_list();
		$this->page->view_form('backups', $data, lang('joomla_available_backup'));
	}
	/**
     * Download Backup file
     *
     * @param string $file_name File Name
     * @return Start dorce download 
     */ 
    function download($file_name)
	{
		// Load libraries
        //---------------

		$this->lang->load('joomla');

		$this->load->library('joomla/Joomla');
		$this->joomla->download_backup($file_name);
	}
	/**
     * Delete joomla version
     *
     * @param @string $file_name File name
     *
     * @return @rediret load backup index page
     */
	function deletewe($file_name)
	{
		// Load libraries
        //---------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		$this->joomla->delete_backup($file_name);
		$this->page->set_message(lang('joomla_backup_delete_success'), 'info');
		redirect('/joomla/backup');
	}
	/**
     * Delete Backup view.
     *
     * @param string $file_name file Nane
     *
     * @return view
     */
    function delete($file_name)
    {
        // Load libraries
        //---------------
        $this->lang->load('joomla');
		$this->load->library('joomla/Joomla');
        
        // Show confirm
        //-------------
        $confirm_uri = '/app/joomla/backup/destroy/' . $file_name;
        $cancel_uri = '/app/joomla/backup';
        $items = array($file_name);
        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }
    /**
     * Destroy joomla version
     *
     * @param @string $file_name File name
     *
     * @return @rediret load backup index page
     */
	function destroy($file_name)
	{
		// Load libraries
        //---------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		$this->joomla->delete_backup($file_name);
		$this->page->set_message(lang('joomla_backup_delete_success'), 'info');
		redirect('/joomla/backup');
	}
}
