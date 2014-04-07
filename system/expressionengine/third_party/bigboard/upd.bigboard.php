<?php
/*
 * BigBoard Update
 *
 * @package     BigBoard
 * @version     1.0.0
 * @author      The Outfit, Inc | Michael Witwicki
 * @link        http://bigboard.us
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

// require
require_once PATH_THIRD . 'bigboard/config' . EXT;

class Bigboard_upd
{
    public $version = BIGBOARD_VERSION;

    public function __construct()
    {
        // Make a local reference to the ExpressionEngine super object
        $this->EE =& get_instance();
    }

    /**
     * Installer
     *
     * @access public
     * @return bool
     */

    public function install()
    {
        $this->EE->load->dbforge();

        // module
        $data = array(
            'module_name'        => BIGBOARD_CLASS,
            'module_version'     => $this->version,
            'has_cp_backend'     => 'y',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);

        // Custom Tables
        $fields = array(
            'id'      => array('type'           => 'int',
                               'constraint'     => '10',
                               'unsigned'       => TRUE,
                               'auto_increment' => TRUE),
            'site_id' => array('type' => 'int', 'constraint' => '10'),
            'type'    => array('type' => 'varchar', 'constraint' => '50'),
            'k'       => array('type' => 'varchar', 'constraint' => '50'),
            'v'       => array('type' => 'varchar', 'constraint' => '255'),
        );

        $this->EE->dbforge->add_field($fields);
        $this->EE->dbforge->add_key('id', TRUE);
        $this->EE->dbforge->create_table('bigboard_config', TRUE);
        unset($fields);

        return TRUE;
    }

    /**
     * Uninstaller
     *
     * @access public
     * @return bool
     */

    public function uninstall()
    {
        $this->EE->load->dbforge();

        $this->EE->db->select('module_id');
        $query = $this->EE->db->get_where('modules', array('module_name' => BIGBOARD_CLASS));

        $this->EE->db->where('module_id', $query->row('module_id'));
        $this->EE->db->delete('module_member_groups');

        $this->EE->db->where('module_name', BIGBOARD_CLASS);
        $this->EE->db->delete('modules');

        $this->EE->db->where('class', BIGBOARD_CLASS);
        $this->EE->db->delete('actions');

        $this->EE->dbforge->drop_table('bigboard_config');

        return TRUE;

    }

    /**
     * Updater
     *
     * @param string $current
     * @return bool
     */

    public function update($current = '')
    {
        $this->EE->load->dbforge();
        return TRUE;
    }
}
/* End of file upd.bigboard.php */
/* Location: ./system/expressionengine/third_party/bigboard/upd.bigboard.php */