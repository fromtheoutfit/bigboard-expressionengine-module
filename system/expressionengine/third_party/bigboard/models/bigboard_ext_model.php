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

class Bigboard_ext_model extends Bigboard_base_model
{
    private $site_id = 1;

    public function __construct()
    {
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');
        $this->EE->db->cache_off();
    }

    /**
     * Get information about an expressionengine member
     *
     * @access public
     * @param int $member_id
     * @return object
     */

    public function get_member($member_id)
    {
        $this->EE->db->select('username, email');
        $this->EE->db->where('member_id', $member_id);
        return $this->EE->db->get('members');
    }

    /**
     * Returns the title of an entry
     *
     * @access public
     * @param int $entry_id
     * @return string
     */

    public function get_channel_details($entry_id)
    {
        $this->EE->db->select('title, url_title');
        $this->EE->db->where('entry_id', $entry_id);
        return $this->EE->db->get('channel_titles');
    }

    /**
     * Returns the URL for a given entry
     *
     * @access public
     * @param string $url_title
     * @param int $entry_id
     * @param int $template_id
     * @param boolean $use_pages
     * @return string
     */

    public function get_url($url_title = '', $entry_id = 0, $template_id = 0, $use_pages = FALSE)
    {
        $url = $this->EE->config->item('site_url');
        $url = rtrim($url, '/');
        $process_template_data = TRUE;

        // Get an array of Pages
        $pages = $this->EE->config->item('site_pages');
        $pages_exist = (isset($pages[$this->site_id]['uris']) && sizeof($pages[$this->site_id]['uris']) > 0) ? TRUE : FALSE;

        // Check to see if we should use pages information for this channel
        if ($pages_exist && $use_pages && isset($pages[$this->site_id]['uris'][$entry_id]))
        {
            $url .= $pages[$this->site_id]['uris'][$entry_id];
            $process_template_data = FALSE;
        }


        if ($template_id > 0 && $process_template_data)
        {
            $template = $this->get_template($template_id);

            if ($template->num_rows() == 1)
            {
                $template_name = $template->row()->template_name;
                $template_group = $template->row()->group_name;

                $url .= '/' . $template_group;

                if ($template_name !== 'index')
                {
                    $url .= '/' . $template_name;
                }

                $url .= '/' . $url_title;
            }

        }
        return $url;
    }
}

/* End of file bigboard_ext_model.php */
/* Location: ./system/expressionengine/third_party/bigboard/models/bigboard_ext_model.php */