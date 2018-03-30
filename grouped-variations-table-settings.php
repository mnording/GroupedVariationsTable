<?php
/**
 * Created by PhpStorm.
 * User: Mattias
 * Date: 2018-03-28
 * Time: 20:07
 */
function myplugin_register_settings() {
    add_option( 'myplugin_option_name', 'This is my option value.');
    register_setting( 'myplugin_options_group', 'myplugin_option_name', 'myplugin_callback' );
}
add_action( 'admin_init', 'myplugin_register_settings' );

function myplugin_register_options_page() {
    add_options_page('Grouped Variations Table - Settings', 'Grouped Variations Table', 'manage_options', 'grouped-variations-table', 'myplugin_options_page');
}
add_action('admin_menu', 'myplugin_register_options_page');
add_action( 'admin_notices', 'my_error_notice' );

function my_error_notice()
{
    if(count(wc_get_attribute_taxonomies()) === 0)
    { ?>
        <div class="notice notice-warning is-dismissable">
            <p>You have no attributes configured for your shop!</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Förkasta detta meddelande.</span></button>
        </div>
    <?php }
}

function myplugin_options_page()
{
    ?>
    <div>
        <h2>Grouped Variations Table</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'myplugin_options_group' ); ?>
            <h3>Select Main Attribute for grouping</h3>
            <table style="width:50%">
                <tr valign="top">
                    <th scope="row"><label for="myplugin_option_name">Main Attribute for sorting:</label></th>
                    <td><select id="myplugin_option_name"  name="myplugin_option_name">
                            <?php foreach(wc_get_attribute_taxonomies() as $attr)
                                {
                                    if(get_option('myplugin_option_name') == "attribute_pa_".$attr->attribute_name)
                                    {
                                        echo "<option selected value='attribute_pa_".$attr->attribute_name."'>".$attr->attribute_label."</option>";
                                    }
                                    else{
                                        echo "<option value='attribute_pa_".$attr->attribute_name."'>".$attr->attribute_label."</option>";
                                    }

                                } ?>
                        </select>
                       </td>
                </tr>
            </table>
            <?php  submit_button(); ?>
        </form>
    </div>
    <?php
} ?>