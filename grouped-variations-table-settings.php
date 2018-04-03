<?php
/**
 * Created by PhpStorm.
 * User: Mattias
 * Date: 2018-03-28
 * Time: 20:07
 */
function groupedvartable_register_settings() {
    add_option( 'groupedvartable_option_mainsorter', 'This is my option value.');
    add_option( 'groupedvartable_option_mainwidth', 'This is my option value.');
    register_setting( 'groupedvartable_options_group', 'groupedvartable_option_mainwidth', 'myplugin_callback' );
    register_setting( 'groupedvartable_options_group', 'groupedvartable_option_mainsorter', 'myplugin_callback' );
}
add_action( 'admin_init', 'groupedvartable_register_settings' );

function groupedvartable_register_options_page() {
    add_options_page('Grouped Variations Table - Settings', 'Grouped Variations Table', 'manage_options', 'grouped-variations-table', 'myplugin_options_page');
}
add_action('admin_menu', 'groupedvartable_register_options_page');
add_action( 'admin_notices', 'my_error_notice' );


function my_error_notice()
{
    if(count(wc_get_attribute_taxonomies()) === 0)
    { ?>
        <div class="notice notice-warning is-dismissable">
            <p><?php _e("You have no attributes configured for your shop!","groupedvartable")?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Close this message.","groupedvartable")?></span></button>
        </div>
    <?php }
}

function myplugin_options_page()
{
    ?>
    <div>
        <h2>Grouped Variations Table</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'groupedvartable_options_group' ); ?>
            <h3><?php _e("Select Main Attribute for grouping","groupedvartable") ?></h3>
            <table style="width:50%">
                <tr valign="top">
                    <th scope="row"><label for="myplugin_option_name"><?php _e("Main Attribute for sorting:","groupedvartable")?></label></th>
                    <td><select id="groupedvartable_option_mainsorter"  name="groupedvartable_option_mainsorter">
                            <?php foreach(wc_get_attribute_taxonomies() as $attr)
                                {
                                    if(get_option('groupedvartable_option_mainsorter') == "attribute_pa_".$attr->attribute_name)
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
                <tr>
                    <th scope="row"><label for="myplugin_option_columns">Grouping width</label></th>
                    <td>
                        <select id="groupedvartable_option_mainwidth"  name="groupedvartable_option_mainwidth">
                            <?php $selectedvalue = get_option('groupedvartable_option_mainwidth'); var_dump($selectedvalue) ?>
                            <option <?php echo $selectedvalue=="full"?"selected":""?>  value="full"><?php _e("Full width","groupedvartable");?></option>
                            <option <?php echo $selectedvalue=="half"?"selected":""?> value="half"><?php _e("Half width","groupedvartable");?></option>
                            <option <?php echo $selectedvalue=="third"?"selected":""?> value="third"><?php _e("1 / 3 Width","groupedvartable");?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php  submit_button(); ?>
        </form>
    </div>
    <?php
} ?>