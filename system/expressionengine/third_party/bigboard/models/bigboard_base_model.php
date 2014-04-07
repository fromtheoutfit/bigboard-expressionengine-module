<?php
/*
 * BigBoard Base Model
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

class Bigboard_base_model
{
    private $site_id = 1;

    public function __construct()
    {
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');
        $this->EE->db->cache_off();
    }

    /**
     * Gets all the config variables from the database for the current site
     *
     * @access public
     * @return array
     */

    public function get_config()
    {
        $data = array(
            'api_key'                  => '',
            'entry_created_channels'   => array(),
            'entry_updated_channels'   => array(),
            'entry_commented_channels' => array(),
            'channel_templates'        => array(),
            'pages_channels'           => array(),
        );

        $this->EE->db->where('site_id', $this->site_id);
        $q = $this->EE->db->get('bigboard_config');

        if ($q->num_rows() > 0)
        {
            foreach ($q->result() as $config)
            {
                switch ($config->type)
                {
                    case 'api_key':
                        $data['api_key'] = $config->v;
                        break;

                    case 'entry_created':
                        array_push($data['entry_created_channels'], $config->v);
                        break;

                    case 'entry_updated':
                        array_push($data['entry_updated_channels'], $config->v);
                        break;

                    case 'entry_commented':
                        array_push($data['entry_commented_channels'], $config->v);
                        break;

                    case 'templates':
                        $data['channel_templates'][$config->k] = $config->v;
                        break;

                    case 'pages':
                        array_push($data['pages_channels'], $config->v);
                        break;

                }
            }
        }

        $q->free_result();

        return $data;
    }

    /**
     * Returns a template group and name by template id
     *
     * @access public
     * @param int $template_id
     * @return object
     */

    public function get_template($template_id)
    {
        $this->EE->db->select('tg.group_name, t.template_name');
        $this->EE->db->from('templates as t');
        $this->EE->db->join('template_groups as tg', 'tg.group_id = t.group_id', 'left outer');
        $this->EE->db->where('t.site_id', $this->site_id);
        $this->EE->db->where('t.template_id', $template_id);
        return $this->EE->db->get();
    }
}

/* End of file bigboard_base_model.php */
/* Location: ./system/expressionengine/third_party/bigboard/models/bigboard_base_model.php */