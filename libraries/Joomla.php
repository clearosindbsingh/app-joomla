<?php

/**
 * Joomla Libraray class.
 *
 * @category   apps
 * @package    Joomla
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2005-2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/joomla/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\joomla;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('joomla');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Factories
//----------

use \clearos\apps\groups\Group_Manager_Factory as Group_Manager;

clearos_load_library('groups/Group_Manager_Factory');

// Classes
//--------

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Shell as Shell;
use \clearos\apps\base\File_Types as File_Types;
use \clearos\apps\base\Folder as Folder;
use \clearos\apps\base\Tuning as Tuning;
use \clearos\apps\network\Role as Role;

clearos_load_library('base/Daemon');
clearos_load_library('base/File');
clearos_load_library('base/Shell');
clearos_load_library('base/File_Types');
clearos_load_library('base/Folder');
clearos_load_library('base/Tuning');
clearos_load_library('network/Role');

// Exceptions
//-----------

use \Exception as Exception;
use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\File_No_Match_Exception as File_No_Match_Exception;
use \clearos\apps\base\File_Not_Found_Exception as File_Not_Found_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/File_No_Match_Exception');
clearos_load_library('base/File_Not_Found_Exception');
clearos_load_library('base/Validation_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Joomla class.
 *
 * @category   apps
 * @package    Joomla
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2005-2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/joomla/
 */

class Joomla extends Daemon
{
    ///////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////

    const PATH_WEBROOT = '/var/www/html';
    const PATH_JOOMLA = '/var/www/html/joomla';
    const PATH_VERSIONS = '/var/clearos/joomla/versions/';
    const PATH_BACKUP = '/var/clearos/joomla/backup/';
    const COMMAND_MYSQLADMIN = '/usr/bin/mysqladmin';
    const COMMAND_MYSQL = '/usr/bin/mysql';
    const COMMAND_WGET = '/bin/wget';
    const COMMAND_ZIP = '/bin/zip';
    const COMMAND_UNZIP = '/bin/unzip';
    const COMMAND_MV = '/bin/mv';
    const CONFIG_SAMPLE_FILE_NAME = 'configuration.php';
    const CONFIG_MAIN_FILE_NAME = 'configuration.php';

    ///////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////

    var $locales;

    ///////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////

    /**
     * DansGuardian constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('joomla');

    }
    /**
     * Get Project path
     *
     * @param @string $folder_name Folder Name
     *
     * @return @string path of folder
     */
    function get_project_path($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        return self::PATH_JOOMLA.'/'.$folder_name.'/';
    }
    /**
     * Get joomla version
     *
     * @return @array Array of available versions
     */
    function get_versions()
    {
        $versions = array(
            array(
                'version' => '3.7.4',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-7-4/Joomla_3-7.4-Stable-Full_Package.zip',
                'deletable' => FALSE,
                'size' => '',
            ),
            array(
                'version' => '3.7.3',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-7-3/Joomla_3.7.3-Stable-Full_Package.zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.7.2',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-7-2/Joomla_3-7.2-Stable-Full_Package.zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.7.1',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-7-1/joomla_3-7-1-stable-full_package-zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.7.0',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-7-0/joomla_3-7-0-stable-full_package-zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.6.5',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-6-5/joomla_3-6-5-stable-full_package-zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.6.4',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-6-4/joomla_3-6-4-stable-full_package-zip',
                'deletable' => TRUE,
                'size' => '',
            ),
            array(
                'version' => '3.6.3',
                'download_url' => 'https://downloads.joomla.org/cms/joomla3/3-6-3/joomla_3-6-3-stable-full_package-zip',
                'deletable' => TRUE,
                'size' => '',
            ),
        );
        foreach ($versions as $key => $value) {
            $versions[$key]['file_name'] = basename($versions[$key]['download_url']);
            $versions[$key]['clearos_path'] = $this->get_joomla_version_downloaded_path(basename($versions[$key]['download_url']));
        }
        return $versions;
    }
    /**
     * Get local system download joomla version path
     * so system can copy from this path to new folder path 
     * 
     * @param @string $version_name zipped version name 
     *
     * @return @string $zip_folder if downloaded & available | FALSE if zip file is not available or not downloaded
     */
    function get_joomla_version_downloaded_path($version_name)
    {
        $zip_folder = self::PATH_VERSIONS.$version_name;
        $folder = new Folder($zip_folder, TRUE);
        if ($folder->exists())
            return $zip_folder;
        return FALSE;

    }

    /**
     * Add a new project.
     *
     * @param string $folder_name Folder Name            
     * @param string $database_name Database name 
     * @param string $database_username Database user 
     * @param string $database_user_password Database user password 
     * @param string $root_username Root username for root permissions 
     * @param string $root_password Root password 
     * @param string $use_exisiting_database Yes / No if you want to use existing database
     * @param string $joomla_version_file selected joomla version zip file name
     *
     * @return void
     */

    public function add_project(
        $folder_name, $database_name, $database_username, $database_user_password,
        $root_username, $root_password, $use_exisiting_database = "No", $joomla_version_file = 'latest.zip'
        ) 
    {
        clearos_profile(__METHOD__, __LINE__);


        $options['validate_exit_code'] = FALSE;
        $shell = new Shell();

        if ($use_exisiting_database == "No")
            $command = "mysql -u $root_username -p$root_password -e \"create database $database_name; GRANT ALL PRIVILEGES ON $database_name.* TO $database_username@localhost IDENTIFIED BY '$database_user_password'\"";
        else
            $command = "mysql -u $root_username -p$root_password -e \"GRANT ALL PRIVILEGES ON $database_name.* TO $database_username@localhost IDENTIFIED BY '$database_user_password'\"";

        try {
            $retval = $shell->execute(
                self::COMMAND_MYSQL, $command, FALSE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Engine_Exception($e->get_message());
        }
        $output = $shell->get_output();
        $output_message = strtolower($output[0]);
        if (strpos($output_message, 'error') !== FALSE)
            throw new Exception($output_message);

        $this->create_project_folder($folder_name);
        $this->put_joomla($folder_name, $joomla_version_file);
        //$this->copy_sample_config_file($folder_name);
        //$this->set_database_name($folder_name, $database_name);
        //$this->set_database_user($folder_name, $database_username);
        //$this->set_database_password($folder_name, $database_user_password);
        //$this->delete_installation_dir($folder_name);
        return $output;
    }
    /**
     * Copy Config File from sample file 
     *
     * @param string $folder_name Folder Name
     *
     * @return void
     */
    function copy_sample_config_file($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        $dirname = dirname(__FILE__);
        $path = explode('/', $dirname);
        array_pop($path);
        $path[] = 'htdocs';
        $htdocs = implode('/', $path).'/';
        $sample_file =  $htdocs.self::CONFIG_SAMPLE_FILE_NAME;
       
        $folder_path = $this->get_project_path($folder_name);
        $main_file = $folder_path.self::CONFIG_MAIN_FILE_NAME;
        $sample_file_obj    = new File($sample_file, TRUE);
        $main_file_obj      = new File($main_file, TRUE);

        if (!$main_file_obj->exists())
            $sample_file_obj->copy_to($main_file);
    }
    /**
     * Config database name in config file
     *
     * @param string $folder_name Folder Name
     * @param string $database_name Database Name
     *
     * @return @void
     */
    function set_database_name($folder_name, $database_name)
    {
        $folder_path = $this->get_project_path($folder_name);
        $main_file = $folder_path.self::CONFIG_MAIN_FILE_NAME;

        $file = new File($main_file, TRUE);

        $replace = '    public $db = '."'$database_name'".';'.PHP_EOL;
        $file->replace_lines('/db =/', $replace, 1);
    }
    /**
     * Change database user in config file
     *
     * @param string $folder_name Folder Name
     * @param string $database_username Database User
     *
     * @return @void
     */
    function set_database_user($folder_name, $database_username)
    {
        $folder_path = $this->get_project_path($folder_name);
        $main_file = $folder_path.self::CONFIG_MAIN_FILE_NAME;
        
        $file = new File($main_file, TRUE);

        $replace = '    public $user = '."'$database_username'".';'.PHP_EOL;
        $file->replace_lines('/user =/', $replace, 1);
    }
    /**
     * Change database password in config file
     *
     * @param string $folder_name Folder Name
     * @param string $database_user_password Database Password
     *
     * @return @void
     */
    function set_database_password($folder_name, $database_user_password)
    {
        $folder_path = $this->get_project_path($folder_name);
        $main_file = $folder_path.self::CONFIG_MAIN_FILE_NAME;
        
        $file = new File($main_file, TRUE);

        $replace = '    public $password = '."'$database_user_password'".';'.PHP_EOL;
        $file->replace_lines('/password =/', $replace, 1);
    }
    /**
     * Validate Folder Name.
     *
     * @param string $folder_name Folder Name
     *
     * @return string error message if Folder name is invalid
     */
    public function validate_folder_name($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/^([a-z0-9_\-\.\$]+)$/', $folder_name))
            return lang('joomla_folder_name_invalid');
        else if($folder_name == 'joomla')
            return lang('joomla_folder_name_choose_other');
        else if($this->check_folder_exists($folder_name))
            return lang('joomla_folder_already_exists');
    }
    /**
     * Validate Folder name must be exists.
     *
     * @param string $folder_name Folder name
     *
     * @return string error message if Folder name is not exists
     */
    public function validate_folder_name_exists($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/^([a-z0-9_\-\.\$]+)$/', $folder_name))
            return lang('joomla_folder_name_invalid');
    }
    /**
     * Validate if database is new.
     *
     * @param string $database_name Database Name
     *
     * @return string error message if Database name is exists
     */
    public function validate_new_database($database_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $root_username = $_POST['root_username'];
        $root_password = $_POST['root_password'];
        $command = "mysql -u $root_username -p$root_password -e \"SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name'\"";
        $shell = new Shell();
        try {
            $retval = $shell->execute(
                self::COMMAND_MYSQL, $command, FALSE, $options
            );
        } catch (Engine_Exception $e) {
            return $e->get_message();
        }
        $output = $shell->get_output();
        $output_message = strtolower($output);
        if (strpos($output_message, 'error') !== FALSE)
            return lang('joomla_unable_connect_via_root_user');
        else if($output)
            return lang('joomla_database_already_exits');
    }
    /**
     * Validate if database is exisitng.
     *
     * @param string $database_name Database Name
     *
     * @return string error message if database name is not exists
     */
    public function validate_existing_database($database_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $root_username = $_POST['root_username'];
        $root_password = $_POST['root_password'];
        $command = "mysql -u $root_username -p$root_password -e \"SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name'\"";
        $shell = new Shell();
        try {
            $retval = $shell->execute(
                self::COMMAND_MYSQL, $command, FALSE, $options
            );
        } catch (Engine_Exception $e) {
            return $e->get_message();
        }
        $output = $shell->get_output();
        $output_message = strtolower($output);
        if (strpos($output_message, 'error') !== FALSE)
            return lang('joomla_unable_connect_via_root_user');
        else if(!$output)
            return lang('joomla_database_not_exits');
    }
    /**
     * Validate database username.
     *
     * @param string $username Username
     *
     * @return string error message if exists
     */
    public function validate_database_username($username)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/^([a-z0-9_\-\.\$]+)$/', $username))
            return lang('joomla_username_invalid');
    }
    /**
     * Validate database password.
     *
     * @param string $password Password
     *
     * @return string error message if exists
     */
    public function validate_database_password($password)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/.*\S.*/', $password))
            return lang('joomla_password_invalid');
    }
    /**
     * Validate root username.
     *
     * @param string $username Username
     *
     * @return string error message if exists
     */
    public function validate_root_username($username)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/^([a-z0-9_\-\.\$]+)$/', $username))
            return lang('joomla_username_invalid');
    }
    /**
     * Validate database root password.
     *
     * @param string $password Password
     *
     * @return string error message if exists
     */
    public function validate_root_password($password)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/.*\S.*/', $password))
            return lang('joomla_password_invalid');
    }
    /**
     * Validate joomla version.
     *
     * @param string $joomla_version version file name 
     *
     * @return string error message if exists
     */
    public function validate_joomla_version($joomla_version)
    {
        clearos_profile(__METHOD__, __LINE__);
        if (! preg_match('/.*\S.*/', $joomla_version))
            return lang('joomla_password_invalid');
    }
    /**
     * Check Folder Exists.
     *
     * @param string $folder_name Folder name
     *
     * @return TRUE if exists, FALSE if not exists 
     */
    function check_folder_exists($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $wpfolder = new Folder(self::PATH_JOOMLA, TRUE);
        $project_path = self::PATH_JOOMLA.'/'.$folder_name;
        if (!$wpfolder->exists()) {
            $wpfolder->create('root', 'root', 0777);
            return FALSE;
        }
        $project_folder = new Folder($project_path, TRUE);
        if ($project_folder->exists()) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Create Project Folder.
     *
     * @param string $folder_name Folder Name
     *
     * @return void
     */
    function create_project_folder($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        if ($this->check_folder_exists($folder_name)) {
            return FALSE;
        }
        $new_folder = new Folder(self::PATH_JOOMLA.'/'.$folder_name, TRUE);
        $new_folder->create('root', 'root', 0777);

    }
    /**
     * Download and setup joomla folder.
     *
     * @param string $folder_name Folder name
     * @param string $version_name Version name
     *
     * @return void
     */
    function put_joomla($folder_name, $version_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $PATH_JOOMLA = self::PATH_JOOMLA;

        $zip_file = $this->get_joomla_version_downloaded_path($version_name);

        $shell = new Shell();
        $options['validate_exit_code'] = FALSE;

        $command = "$zip_file -d ".$this->get_project_path($folder_name);
       
        try {
            $retval = $shell->execute(
                self::COMMAND_UNZIP, $command, TRUE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Exception($e);
        }
        $output = $shell->get_output();

        $folder = new Folder($this->get_project_path($folder_name));
        $folder->chmod(777);

        $folder = new Folder($this->get_project_path($folder_name).'installation');
        $folder->chmod(777);

        $folder = new Folder($this->get_project_path($folder_name).'cache');
        $folder->chmod(777);
        
        return $output;
    }
    /**
     * Download joomla version from official website.
     *
     * @param string $version_file_name Zip file name
     *
     * @return TRUE if download completed, FALSE if folder exists, ERROR if something goes wrong
    **/
    function download_version($version_file_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $options['validate_exit_code'] = FALSE;
        
        $path_versions = self::PATH_VERSIONS;
        $path_file = $path_versions.$version_file_name;
        $file = new File($path_file, TRUE);

        if($file->exists())
           return FALSE;
        $versions = $this->get_versions();
        $download_url = '';

        foreach ($versions as $key => $value) {

            if ($value['file_name'] == $version_file_name) {
                $download_url = $value['download_url'];
                break;
            }
        }

        $shell = new Shell();
        $command = "$download_url -P $path_versions";

        try {
            $retval = $shell->execute(
                self::COMMAND_WGET, $command, TRUE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Exception($e);
        }
        $output = $shell->get_output();
        return TRUE;
    }
    /**
     * Delete downloaded joomla version.
     *
     * @param string $version_file_name Zip file name
     *
     * @return TRUE if delete completed, FALSE if file not exists, ERROR if something goes wrong 
     */
    function delete_version($version_file_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        
        $path_versions = self::PATH_VERSIONS;
        $path_file = $path_versions.$version_file_name;

        $file = new File($path_file, TRUE);
        if (!$file->exists())
           return FALSE;
        $file->delete();
            return TRUE;
    }
    /**
     * List of project.
     *
     * @return array $list of all projects under joomla
     */
    function get_project_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = array();
        $folder = new Folder(self::PATH_JOOMLA);
        if ($folder->exists()) {
            $list = $folder->get_listing(TRUE, FALSE);
            foreach ($list as $key => $value) {
                $folder = new Folder($this->get_project_path($value['name']));
                $list[$key]['permissions'] = $folder->get_permissions();
                $list[$key]['database'] = $this->get_database_name($value['name']);
            }
        }
        return $list;
    }
    /**
     * Delete project folder.
     *
     * @param string $folder_name Folder Name
     *
     * @return void
     */
    function delete_folder($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $this->get_database_name($folder_name);
        $this->do_backup_folder($folder_name);
        $folder = new Folder($this->get_project_path($folder_name));
        $folder->delete(TRUE);
    }
    /**
     * Delete project folder.
     *
     * @param string $folder_name Folder Name
     * @param string $permissions Permission
     *
     * @return void
     */
    function set_folder_permissions($folder_name,$permissions = '0755')
    {
        clearos_profile(__METHOD__, __LINE__);
       
        $folder = new Folder($this->get_project_path($folder_name));
        $folder->chmod($permissions);
    }
    /**
     * Delete installation folder.
     *
     * @param string $folder_name Folder Name
     *
     * @return void
     */
    function delete_installation_dir($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $folder = new Folder($this->get_project_path($folder_name).'installation');
        $folder->delete(TRUE);
    }
    /**
     * Create backup of given project folder.
     *
     * @param string $folder_name Folder Name
     *
     * @return void
     */
    function do_backup_folder($folder_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        
        $folder = new Folder(self::PATH_BACKUP);
        if (!$folder->exists())
            $folder->create('root', 'root', 0777);

        $folder_path = $this->get_project_path($folder_name);

        $zip_path = self::PATH_JOOMLA.'/'.$folder_name.'__'.date('Y-m-d-H-i-s').'.zip';
        $command = "-r $zip_path $folder_path";
        
        $options['validate_exit_code'] = FALSE;
        $shell = new Shell();
        try {
            $retval = $shell->execute(
                self::COMMAND_ZIP, $command, TRUE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Exception($e);
        }
        $output = $shell->get_output();
        $file = new File($zip_path);
        if ($file->exists() && !$file->is_directory()) {
            $file->move_to(self::PATH_BACKUP);
        }
    }
    /**
     * Get database name from config file.
     *
     * @param string $folder_name Project folder name
     *
     * @return string $database_name Database Name
     */
    function get_database_name($folder_name)
    {
        $folder_path = $this->get_project_path($folder_name);
        $main_file = $folder_path.self::CONFIG_MAIN_FILE_NAME;
        
        $file = new File($main_file, TRUE);
        if(!$file->exists())
            return FALSE;
        $line = $file->lookup_line("/db =/");
        preg_match_all('/".*?"|\'.*?\'/', $line, $matches);
        $database_name = trim($matches[0][0], "'");
        return $database_name;
    }
    /**
     * Delete MYSQL database.
     *
     * @param string $database_name Database Name
     * @param string $root_username Root Username
     * @param string $root_password Root Password
     *
     * @return Exception is somethings goes wrong with MYSQL 
    */
    function delete_database($database_name, $root_username, $root_password)
    {
        $command = "mysql -u $root_username -p$root_password -e \"DROP DATABASE $database_name\"";
        $shell = new Shell();
        try {
            $retval = $shell->execute(
                self::COMMAND_MYSQL, $command, FALSE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Exception($e->get_message());
        }
        $output = $shell->get_output();
        $output_message = strtolower($output);

        if (strpos($output_message, 'error') !== FALSE)
            throw new Exception(lang('joomla_unable_connect_via_root_user'));
    }
    /**
     * Backup MYSQL database.
     *
     * @param string $database_name Database Name
     * @param string $root_username Root Username
     * @param string $root_password Root Password
     *
     * @return Exception is somethings goes wrong with MYSQL 
    */
    function backup_database($database_name, $root_username, $root_password)
    {
        $sql_file_path = self::PATH_BACKUP.$database_name.'__'.date('Y-m-d-H-i-s').'.sql';
        $command = "mysql -u $root_username -p$root_password -e \"mysqldump $database_name > $sql_file_path\"";
        //echo $command; die;
        $shell = new Shell();
        try {
            $retval = $shell->execute(
                self::COMMAND_MYSQL, $command, FALSE, $options
            );
        } catch (Engine_Exception $e) {
            throw new Exception($e->get_message());
        }
        $output = $shell->get_output();
        $output_message = strtolower($output);
        if (strpos($output_message, 'error') !== FALSE)
            throw new Exception(lang('joomla_unable_connect_via_root_user'));
        
    }
    /**
     * List of avalable Project & SQL backups.
     *
     * @return list of all backups under joomla including database
    */
    function get_backup_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = array();
        $folder = new Folder(self::PATH_BACKUP);
        if ($folder->exists()) {
            $list = $folder->get_listing(TRUE, TRUE);
        }
        return $list;
    }
    /**
     * Start force download of backup
     *
     * @param string $file_name Backup file name
     * @return void
    */
    function download_backup($file_name)
    {
        clearos_profile(__METHOD__, __LINE__);
        // Make file full path
        $file_path = self::PATH_BACKUP.$file_name;

        // Check file exists
        if (file_exists($file_path)) {
            // Getting file extension.
            $extension = explode('.', $file_name);
            $extension = $extension[count($extension)-1]; 
            // For Gecko browsers
            header('Content-Transfer-Encoding: binary');  
            // Supports for download resume
            header('Accept-Ranges: bytes');  
            // Calculate File size
            header('Content-Length: ' . filesize($file_path));  
            header('Content-Encoding: none');
            // Change the mime type if the file is not PDF
            header('Content-Type: application/'.$extension);  
            // Make the browser display the Save As dialog
            header('Content-Disposition: attachment; filename=' . $file_name);  
            readfile($file_path); 
            exit;
        }
        else
            throw new File_Not_Found_Exception(lang('joomla_file_not_found'));
    }
    /**
     * Delete backup from system
     *
     * @param string $file_name Backup file name
     * @return TRUE if deletion successful, Exception if something wrong in deletion
    **/
    function delete_backup($file_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        $file_path = self::PATH_BACKUP.$file_name;
        $file = new File($file_path);

        if (!$file->is_directory())
            $file->delete(TRUE);
        else
            throw new File_Not_Found_Exception(lang('joomla_file_not_found'));
        return TRUE;
    }
}