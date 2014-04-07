<?php
/*
 * BigBoard MCP
 *
 * @package     BigBoard
 * @version     1.0.0
 * @author      The Outfit, Inc | Michael Witwicki
 * @link        http://bigboard.us
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bigboard_mcp
{

    private $site_id = 1;
    private $module_base = '';

    public function __construct()
    {
        // ee super object
        $this->EE          =& get_instance();
        $this->site_id     = $this->EE->config->item('site_id');
        $this->module_base = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=bigboard' . AMP;

        // models
        $this->EE->load->model('bigboard_mcp_model', 'bb');

        // db cache
        $this->EE->db->cache_off();
    }

    /**
     * MCP Landing Page
     *
     * @access public
     * @return    string
     */

    public function index()
    {
        // page title
        $this->_cp_page_title($this->EE->lang->line('bigboard_mcp_index'));

        // models
        $this->EE->load->model('template_model');

        // libraries
        $this->EE->load->library('table');
        $this->EE->load->library('api');
        $this->EE->api->instantiate('channel_structure');

        // variables
        $channels  = $this->EE->api_channel_structure->get_channels((int)$this->site_id);
        $templates = $this->EE->template_model->get_templates($this->EE->config->item('site_id'));

        $config = $this->EE->bb->get_config();

        $vars = array(
            'channels'                 => $channels,
            'templates'                => $templates,
            'channel_templates'        => $config['channel_templates'],
            'api_key'                  => $config['api_key'],
            'entry_created_channels'   => $config['entry_created_channels'],
            'entry_updated_channels'   => $config['entry_updated_channels'],
            'entry_commented_channels' => $config['entry_commented_channels'],
            'pages_channels'           => $config['pages_channels'],
        );

        return $this->EE->load->view('/mcp/index', $vars, TRUE);
    }

    /**
     * Main form handler
     *
     * @access public
     * @return void
     */

    public function config_handler()
    {
        $data = array();
        foreach ($_POST as $k => $v)
        {
            if ($k !== 'submit')
            {
                $kboom = explode('-', $k);
                $type  = $kboom[0];

                switch ($type)
                {
                    case 'templates':
                        $key     = $kboom[1];
                        $process = ($v > 0) ? TRUE : FALSE;
                        break;
                    default:
                        $key     = $type;
                        $process = TRUE;
                }

                if ($process)
                {
                    array_push($data, array(
                        'site_id' => $this->site_id,
                        'type'    => $type,
                        'k'       => $key,
                        'v'       => $v,
                    ));
                }

            }
        }

        $this->EE->bb->drop_config();
        $this->EE->bb->set_config($data);

        $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('bigboard_config_saved'));
        $this->EE->functions->redirect($this->module_base . AMP . 'method=index');

    }

    /**
     * Set page title
     *
     * @access private
     * @param string $page
     * @return void
     */

    private function _cp_page_title($page)
    {
        if (version_compare(APP_VER, '2.6.0', '<'))
        {
            $this->EE->cp->set_variable('cp_page_title', $page);
        }
        else
        {
            $this->EE->view->cp_page_title = $page;
        }
    }
}
/* End of file mcp.bigboard.php */
/* Location: ./system/expressionengine/third_party/bigboard/mcp.bigboard.php */
