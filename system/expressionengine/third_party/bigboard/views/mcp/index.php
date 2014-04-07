<?php
$data = form_open('C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=bigboard' . AMP . 'method=config_handler');

// Define table template
$this->table->set_template($cp_table_template);

$data .= '<h2>' . lang('bigboard_api_key_heading') . '</h2>';
$data .= '<p>' . lang('bigboard_api_key_desc') . '</p>';

$this->table->set_heading(array(lang('bigboard_api_key_heading')));
$this->table->add_row(
    array(
        'data'  => form_input('api_key-1', $api_key),
        'style' => 'width: 100%',
    )
);

$data .= $this->table->generate();

// Channels
if ($channels->num_rows())
{

    ////////////////////////////////////////////////////////////////////////////////
    // ENTRY CREATED
    ////////////////////////////////////////////////////////////////////////////////

    $data .= '<h2>' . lang('bigboard_channel_entry_created_heading') . '</h2>';
    $data .= '<p>' . lang('bigboard_channel_entry_created_desc') . '</p>';

    $this->table->set_heading(array(lang('bigboard_selected'), lang('bigboard_channels')));
    foreach ($channels->result() as $channel)
    {
        $selected = (in_array($channel->channel_id, $entry_created_channels)) ? TRUE : FALSE;
        $this->table->add_row(
            array(
                'data'  => form_checkbox('entry_created-' . $channel->channel_id, $channel->channel_id, $selected),
                'style' => 'width: 5%',
            ),
            array(
                'data'  => $channel->channel_title,
                'style' => 'width: 95%',
            )
        );
    }

    $data .= $this->table->generate();

    ////////////////////////////////////////////////////////////////////////////////
    // ENTRY UPDATED
    ////////////////////////////////////////////////////////////////////////////////

    $data .= '<h2>' . lang('bigboard_channel_entry_updated_heading') . '</h2>';
    $data .= '<p>' . lang('bigboard_channel_entry_updated_desc') . '</p>';

    $this->table->set_heading(array(lang('bigboard_selected'), lang('bigboard_channels')));
    foreach ($channels->result() as $channel)
    {
        $selected = (in_array($channel->channel_id, $entry_updated_channels)) ? TRUE : FALSE;

        $this->table->add_row(
            array(
                'data'  => form_checkbox('entry_updated-' . $channel->channel_id, $channel->channel_id, $selected),
                'style' => 'width: 5%',
            ),
            array(
                'data'  => $channel->channel_title,
                'style' => 'width: 95%',
            )
        );
    }

    $data .= $this->table->generate();

    ////////////////////////////////////////////////////////////////////////////////
    // ENTRY COMMENTED
    ////////////////////////////////////////////////////////////////////////////////

    $data .= '<h2>' . lang('bigboard_channel_entry_comment_heading') . '</h2>';
    $data .= '<p>' . lang('bigboard_channel_entry_comment_desc') . '</p>';

    $this->table->set_heading(array(lang('bigboard_selected'), lang('bigboard_channels')));
    foreach ($channels->result() as $channel)
    {
        $selected = (in_array($channel->channel_id, $entry_commented_channels)) ? TRUE : FALSE;

        $this->table->add_row(
            array(
                'data'  => form_checkbox('entry_commented-' . $channel->channel_id, $channel->channel_id, $selected),
                'style' => 'width: 5%',
            ),
            array(
                'data'  => $channel->channel_title,
                'style' => 'width: 95%',
            )
        );
    }

    $data .= $this->table->generate();

    ////////////////////////////////////////////////////////////////////////////////
    // CHANNEL TEMPLATE ASSIGNMENT
    ////////////////////////////////////////////////////////////////////////////////

    $this->table->set_heading(array(lang('bigboard_selected'), lang('bigboard_channels')));
    $tmpl = array(
        'None' => array(0 => 'Select Template'),
    );

    foreach ($templates->result() as $template)
    {
        $tmpl[$template->group_name][$template->template_id] = $template->template_name;
    }

    $this->table->set_heading(array(lang('bigboard_channels'), lang('bigboard_templates'), lang('bigboard_pages_heading')));
    foreach ($channels->result() as $channel)
    {
        $selected = (array_key_exists($channel->channel_id, $channel_templates)) ? $channel_templates[$channel->channel_id] : 0;
        $pages_selected = (in_array($channel->channel_id, $pages_channels)) ? TRUE : FALSE;

        $this->table->add_row(
            array(
                'data'  => $channel->channel_title,
                'style' => 'width: 33%',
            ),
            array(
                'data'  => form_dropdown('templates-' . $channel->channel_id, $tmpl, $selected),
                'style' => 'width: 34%',
            ),
            array(
                'data'  => form_checkbox('pages-' . $channel->channel_id, $channel->channel_id, $pages_selected),
                'style' => 'width: 33%',
            )
        );
    }


    $data .= $this->table->generate();

}
else
{
    $data .= '<p>' . lang('bigboard_no_channels_warning') . '</p>';
}

$data .= '<div class="tableFooter"><div class="tableSubmit">' . form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')) . '</div></div>';
$data .= form_close();
echo $data;


/* End of file index.php */
/* Location: ./system/expressionengine/third_party/bigboard/views/mcp/index.php */