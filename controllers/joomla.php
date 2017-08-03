<?php

/**
 * Version controller.
 *
 * @category   Apps
 * @package    Joomla
 * @subpackage Controller
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link    http://www.clearfoundation.com/docs/developer/apps/joomla/
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

class Joomla extends ClearOS_Controller
{
    /**
     * Joomla default controller.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('joomla');
        $this->load->library('joomla/Joomla');
        $projects = $this->joomla->get_project_list();
        $versions = $this->joomla->get_versions();
        $data['projects'] = $projects;
        $data['versions'] = $versions;
        $data['base_path'] = 'https://'.$_SERVER['SERVER_ADDR'].'/joomla/';

        // Load views
        //-----------
        $this->page->view_form('joomla', $data, lang('joomla_app_name'));
    }
	/**
     * Add a new Project
     * 
     * @return load view
     */ 
	function addproject()
	{
		// Load dependencies
        //------------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		$version_all = $this->joomla->get_versions();
		$versions = array();
		foreach ($version_all as $key => $value) 
		{
			if($value['clearos_path'])
				$versions[$value['file_name']] = $value['version'];
		}
		if($_POST)
		{
			// Handle Form 
        	//------------------

			$use_exisiting_database = $this->input->post('use_exisiting_database');
			$this->form_validation->set_policy('folder_name', 'joomla/Joomla', 'validate_folder_name', TRUE);
			if($use_exisiting_database == "Yes")
				$this->form_validation->set_policy('database_name', 'joomla/Joomla', 'validate_existing_database', TRUE);
			else
				$this->form_validation->set_policy('database_name', 'joomla/Joomla', 'validate_new_database', TRUE);
			$this->form_validation->set_policy('database_user_name', 'joomla/Joomla', 'validate_database_username', TRUE);
			$this->form_validation->set_policy('database_user_password', 'joomla/Joomla', 'validate_database_password', TRUE);
			$this->form_validation->set_policy('root_username', 'joomla/Joomla', 'validate_root_username', TRUE);
			$this->form_validation->set_policy('root_password', 'joomla/Joomla', 'validate_root_password', TRUE);
			$this->form_validation->set_policy('joomla_version', 'joomla/Joomla', 'validate_joomla_version', TRUE);
			$form_ok = $this->form_validation->run();
			if($form_ok)
			{
				$folder_name = $this->input->post('folder_name');
				$database_name = $this->input->post('database_name');
				$database_username = $this->input->post('database_user_name');
				$database_user_password = $this->input->post('database_user_password');
				$root_username = $this->input->post('root_username');
				$root_password = $this->input->post('root_password');
				$joomla_version = $this->input->post('joomla_version');
				try {
					$this->joomla->add_project($folder_name, $database_name, $database_username, $database_user_password, $root_username, $root_password, $use_exisiting_database, $joomla_version);
					//$this->joomla->create_project_folder($folder_name);   
					//$this->joomla->put_joomla($folder_name);
					$this->page->set_message(lang('joomla_project_add_success'), 'info');
					redirect('/joomla');
				} catch (Exception $e) {
					$this->page->view_exception($e);
				}
			}
		}
		$data['versions'] = $versions;
		$data['default_version'] = 'latest.zip';
		$this->page->view_form('add_project', $data, lang('joomla_app_name'));
	}
	/**
     * Delete Project
     *
     * @param string $folder_name Folder Name 
     * @return redirect to index after delete
     */ 
	function delete($folder_name)
	{
		// Load dependencies
        //------------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		if ($_POST) {
			$database_name = '';
			$folder_name = $this->input->post('folder_name');
			$delete_database = $this->input->post('delete_database');

			if ($folder_name)
				$database_name = $this->joomla->get_database_name($folder_name);
			$_POST['database_name'] = $database_name;
			$_POST['folder_name'] = $folder_name;
			$this->form_validation->set_policy('folder_name', 'joomla/Joomla', 'validate_folder_name_exists', TRUE);
			if ($delete_database && $database_name) {
				$this->form_validation->set_policy('database_name', 'joomla/Joomla', 'validate_existing_database', TRUE);
				$this->form_validation->set_policy('root_username', 'joomla/Joomla', 'validate_root_username', TRUE);
				$this->form_validation->set_policy('root_password', 'joomla/Joomla', 'validate_root_password', TRUE);
			}
			$form_ok = $this->form_validation->run();

			//echo $database_name; die('fg');
			if ($form_ok) {
				$folder_name = $this->input->post('folder_name');
				$database_name = $this->input->post('database_name');
				$root_username = $this->input->post('root_username');
				$root_password = $this->input->post('root_password');

				try {
					$this->joomla->delete_folder($folder_name);
					if ($delete_database && $database_name) {
						//$this->joomla->backup_database($database_name, $root_username, $root_password); /// due to some temp error I commented it
						$this->joomla->delete_database($database_name, $root_username, $root_password);
					}
					$this->page->set_message(lang('joomla_project_delete_success'), 'info');
					redirect('/joomla');
				} catch (Exception $e) {
					$this->page->view_exception($e);
				}
			}
			else {
				$this->page->view_exception(validation_errors());
				
			}
		}
	}
	/**
     * Fix Folder Permissions
     *
     * @param string $folder_name Folder Name 
     * @return redirect to index after fixes
     */ 
	function fixit($folder_name)
	{
		// Load dependencies
        //------------------

		$this->lang->load('joomla');
		$this->load->library('joomla/Joomla');

		$this->joomla->set_folder_permissions($folder_name,'0755');

		$this->page->set_message(lang('joomla_project_delete_success'), 'info');
		redirect('/joomla');
	
	}
}
