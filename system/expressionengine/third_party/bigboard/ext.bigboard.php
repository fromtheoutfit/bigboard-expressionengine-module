<?php
/*
 __    __                                   __       ___     __
/\ \__/\ \                                 /\ \__  /'___\ __/\ \__
\ \ ,_\ \ \___      __         ___   __  __\ \ ,_\/\ \__//\_\ \ ,_\
 \ \ \/\ \  _ `\  /'__`\      / __`\/\ \/\ \\ \ \/\ \ ,__\/\ \ \ \/
  \ \ \_\ \ \ \ \/\  __/     /\ \L\ \ \ \_\ \\ \ \_\ \ \_/\ \ \ \ \_
   \ \__\\ \_\ \_\ \____\    \ \____/\ \____/ \ \__\\ \_\  \ \_\ \__\
    \/__/ \/_/\/_/\/____/     \/___/  \/___/   \/__/ \/_/   \/_/\/__/

Description:		BigBoard Extension for ExpressionEngine
Developer:			The Outfit, Inc.
Website:			fromtheoutfit.com
Contact:			hello@fromtheoutfit.com  / 617.459.4578
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

// config
require_once PATH_THIRD . 'bigboard/config' . EXT;

class Bigboard_ext
{

    public $name = "BigBoard Extension";
    public $description = "Send ExpressionEngine events to BigBoard.us";
    public $settings = array();
    public $settings_exist = 'n';
    public $docs_url = 'http://bigboard.us';
    public $version = BIGBOARD_VERSION;
    public $site_id = 1;
    private $config = array();

    public function __construct($settings = '')
    {
        // ee super object
        $this->EE      =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');

        // settings
        $this->settings = $settings;

        // models
        $this->EE->load->add_package_path(PATH_THIRD . 'bigboard/');
        $this->EE->load->model('bigboard_ext_model', 'bb');

        // lang
        $this->EE->lang->loadfile('bigboard');

        // load configs
        $this->config = $this->EE->bb->get_config();
    }

    /**
     * activate the extension
     *
     * @access public
     * @return void
     */

    public function activate_extension()
    {
        $hooks = array(
            'entry_submission_end' => 'entry_submission_end',
            'insert_comment_end'   => 'insert_comment_end',
        );

        foreach ($hooks as $k => $v)
        {
            $data = array(
                'class'    => __CLASS__,
                'method'   => $v,
                'hook'     => $v,
                'settings' => serialize($this->settings),
                'priority' => 10,
                'version'  => $this->version,
                'enabled'  => 'y'
            );

            $this->EE->db->insert('extensions', $data);
        }
    }

    /**
     * Update the extension
     *
     * @access public
     * @param string $current
     * @return bool
     */

    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->update(
            'extensions',
            array('version' => $this->version)
        );
    }

    /**
     * Disable the extension
     *
     * @access public
     * @return void
     */

    public function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    /**
     * Extension settings
     *
     * @access public
     * @return array
     */

    public function settings()
    {
        $settings = array();

        return $settings;
    }

    /**
     * Handles the entry_submission_end hook
     *
     * @access public
     * @param $id
     * @param $meta
     * @param $data
     * @return bool
     */

    public function entry_submission_end($id, $meta, $data)
    {
        $entry_type = $data['entry_id'] > 0 ? 'updated' : 'new';

        if ($this->entry_submission_config_check($meta['channel_id'], $data['entry_id']))
        {
            // API
            $this->EE->load->library('api');
            $this->EE->api->instantiate('channel_structure');
            $channel  = $this->EE->api_channel_structure->get_channel_info((int)$meta['channel_id']);
            $member   = $this->EE->bb->get_member($meta['author_id']);
            $template = (array_key_exists((int)$meta['channel_id'], $this->config['channel_templates'])) ? $this->config['channel_templates'][(int)$meta['channel_id']] : 0;
            $use_pages = (in_array((int)$meta['channel_id'], $this->config['pages_channels'])) ? TRUE : FALSE;
            $url      = $this->EE->bb->get_url($this->EE->input->post('url_title'), $id, $template, $use_pages);

            if ($member->num_rows() > 0 && $channel->num_rows() > 0)
            {
                $m  = $member->row();
                $ch = $channel->row();
                $this->bigboard($m->email, $ch->channel_title . ': ' . $meta['title'], $this->EE->lang->line('bigboard_entry_' . $entry_type), $url);
            }

            $member->free_result();
        }

        return TRUE;
    }

    /**
     * Handles the insert_comment_end hook
     *
     * @access public
     * @param $data
     * @param $comment_moderate
     * @param $comment_id
     * @return bool
     */

    public function insert_comment_end($data, $comment_moderate, $comment_id)
    {
        if ($this->entry_comment_config_check($data['channel_id']))
        {
            // API
            $this->EE->load->library('api');
            $this->EE->api->instantiate('channel_structure');
            $channel  = $this->EE->api_channel_structure->get_channel_info((int)$data['channel_id']);
            $entry    = $this->EE->bb->get_channel_details($data['entry_id']);
            $template = (array_key_exists((int)$data['channel_id'], $this->config['channel_templates'])) ? $this->config['channel_templates'][(int)$data['channel_id']] : 0;
            $use_pages = (in_array((int)$data['channel_id'], $this->config['pages_channels'])) ? TRUE : FALSE;

            if ($channel->num_rows() > 0 && $entry->num_rows() > 0)
            {
                $ch  = $channel->row();
                $e   = $entry->row();
                $url = $this->EE->bb->get_url($e->url_title, $data['entry_id'], $template, $use_pages);
                $this->bigboard($data['email'], $ch->channel_title . ': ' . $e->title, lang('bigboard_entry_commented'), $url);
            }

            $channel->free_result();
            $entry->free_result();
        }
        return TRUE;
    }


    /**
     * Post to BigBoard
     *
     * @access private
     * @param string $email
     * @param string $summary
     * @param string $label
     * @param string $url
     * @return mixed
     */

    private function bigboard($email, $summary, $label, $url)
    {
        $data = '';

        if (isset($this->config['api_key']))
        {

            // get it on the board!
            $p['events'][0]['email']   = $email;
            $p['events'][0]['summary'] = $summary;
            $p['events'][0]['label']   = $label;
            $p['events'][0]['url']     = $url;
            $p['events'][0]['time']    = time();

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Accept: application/json';
            $headers[] = 'X-BigBoard-Token: ' . $this->config['api_key'];

            $ch = curl_init();

            $options = array(
                CURLOPT_POST           => 1,
                CURLOPT_URL            => BIGBOARD_API_ENDPOINT,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_POSTFIELDS     => json_encode($p),
                CURLOPT_USERAGENT      => 'BigBoard (bigboard@fromtheoutfit.com)',
                CURLOPT_FOLLOWLOCATION => TRUE
            );

            curl_setopt_array($ch, $options);
            $data = json_decode(curl_exec($ch));

        }

        return $data;
    }

    /**
     * Determines if an entry submission should be processed
     *
     * @access private
     * @param int $channel_id
     * @param int $entry_id
     * @return boolean
     */

    private function entry_submission_config_check($channel_id, $entry_id)
    {
        $data = FALSE;

        if ($entry_id == 0 && in_array($channel_id, $this->config['entry_created_channels']))
        {
            $data = TRUE;
        }
        elseif ($entry_id > 0 && in_array($channel_id, $this->config['entry_updated_channels']))
        {
            $data = TRUE;
        }

        return $data;
    }

    /**
     * Determines if an entry comment should be processed
     *
     * @access public
     * @param int $channel_id
     * @return bool
     */

    private function entry_comment_config_check($channel_id)
    {
        $data = FALSE;

        if (in_array($channel_id, $this->config['entry_commented_channels']))
        {
            $data = TRUE;
        }

        return $data;
    }
}

/* End of file ext.bigboard.php */
/* Location: ./system/expressionengine/third_party/bigboard/ext.bigboard.php */