<?php
/*
 * BigBoard Mod
 *
 * @package     BigBoard
 * @version     1.0.0
 * @author      The Outfit, Inc | Michael Witwicki
 * @link        http://bigboard.us
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

// require
require_once PATH_THIRD . 'bigboard/config' . EXT;

class Bigboard
{
    private $version = BIGBOARD_VERSION;

    public function __construct()
    {
        // ee super object
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');

        // models
        $this->EE->load->model('bigboard_mod_model', 'bb');

        // db cache
        $this->EE->db->cache_off();
    }
}

/* End of file mod.bigboard.php */
/* Location: ./system/expressionengine/third_party/bigboard/mod.bigboard.php */