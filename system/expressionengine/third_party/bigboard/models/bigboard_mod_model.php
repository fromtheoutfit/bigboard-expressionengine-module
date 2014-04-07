<?php
/*
 * BigBoard Mod Model
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

class Bigboard_mod_model extends Bigboard_base_model
{
    private $site_id = 1;

    public function __construct()
    {
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');
        $this->EE->db->cache_off();
    }
}

/* End of file bigboard_model.php */
/* Location: ./system/expressionengine/third_party/bigboard/models/bigboard_mod_model.php */