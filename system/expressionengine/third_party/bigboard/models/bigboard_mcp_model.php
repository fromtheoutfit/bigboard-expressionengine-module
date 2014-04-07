<?php
/*
 * BigBoard CP Model
 *
 * @package     BigBoard
 * @version     1.0.0
 * @author      The Outfit, Inc | Michael Witwicki
 * @link        http://bigboard.us
 */

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// require
require_once PATH_THIRD . 'bigboard/config' . EXT;
require_once PATH_THIRD . 'bigboard/models/bigboard_base_model' . EXT;

class Bigboard_mcp_model extends Bigboard_base_model
{
    private $site_id = 1;

    public function __construct()
    {
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');
        $this->EE->db->cache_off();
    }

    /**
     * Sets the config variables in the database
     *
     * @access public
     * @param array $data
     * @return void
     */
    public function set_config($data = array())
    {
        // Insert the new configs
        $this->EE->db->insert_batch('bigboard_config', $data);
    }

    /**
     * Deletes existing configs
     *
     * @access public
     * @return void
     */

    public function drop_config()
    {
        // Delete any existing configs for this site
        $this->EE->db->where('site_id', $this->site_id);
        $this->EE->db->delete('bigboard_config');
    }
}

/* End of file bigboard_cp.php */
/* Location: ./system/expressionengine/third_party/bigboard/models/bigboard_mcp_model.php */