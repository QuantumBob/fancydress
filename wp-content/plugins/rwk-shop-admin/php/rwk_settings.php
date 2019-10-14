<?php

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */
function rwk_settings_init()
{
    // register a new setting for "rwk" page
    register_setting('rwk',
                     'rwk_options');

    // register a new section in the "rwk" page
    add_settings_section(
            'rwk_section_developers',
            __('The Matrix has you.',
               'rwk'),
               'rwk_section_developers_cb',
               'rwk'
    );

    // register a new field in the "rwk_section_developers" section, inside the "rwk" page
    add_settings_field(
            'rwk_field_pill', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Pill',
               'rwk'),
               'rwk_field_pill_cb',
               'rwk',
               'rwk_section_developers',
               [
                'label_for'       => 'rwk_field_pill',
                'class'           => 'rwk_row',
                'rwk_custom_data' => 'custom',
            ]
    );
}
add_action('admin_init',
           'rwk_settings_init');

/**
 * custom option and settings:
 * callback functions
 */
// developers section cb
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function rwk_section_developers_cb($args)
{
    ?>
    <p id="<?php echo esc_attr($args['id']); ?>"><?php
        esc_html_e('Follow the white rabbit.',
                   'rwk');
        ?></p>
    <?php
}

// pill field cb
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function rwk_field_pill_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option('rwk_options');
    // output the field
    ?>
    <select id="<?php echo esc_attr($args['label_for']); ?>"
            data-custom="<?php echo esc_attr($args['rwk_custom_data']); ?>"
            name="rwk_options[<?php echo esc_attr($args['label_for']); ?>]"
            >
        <option value="red" <?php
        echo isset($options[$args['label_for']]) ? ( selected($options[$args['label_for']],
                                                              'red',
                                                              false) ) : ( '' );
        ?>>
                    <?php
                    esc_html_e('red pill',
                               'rwk');
                    ?>
        </option>
        <option value="blue" <?php
        echo isset($options[$args['label_for']]) ? ( selected($options[$args['label_for']],
                                                              'blue',
                                                              false) ) : ( '' );
        ?>>
                    <?php
                    esc_html_e('blue pill',
                               'rwk');
                    ?>
        </option>
    </select>
    <p class="description">
        <?php
        esc_html_e('You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.',
                   'rwk');
        ?>
    </p>
    <p class="description">
        <?php
        esc_html_e('You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.',
                   'rwk');
        ?>
    </p>
    <?php
}

function rwk_options_page()
{
    // add top level menu page
    add_options_page(
            'Rwk Shop Admin Options',
            'Rwk Shop Admin',
            'manage_options',
            'rwk',
            'rwk_options_page_html'
    );
}
add_action('admin_menu',
           'rwk_options_page');

function rwk_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // add error/update messages
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('rwk_messages',
                           'rwk_message',
                           __('Settings Saved',
                              'rwk'),
                              'updated');
    }

    // show error/update messages
    settings_errors('rwk_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "rwk"
            settings_fields('rwk');
            // output setting sections and their fields
            // (sections are registered for "rwk", each field is registered to a specific section)
            do_settings_sections('rwk');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}
