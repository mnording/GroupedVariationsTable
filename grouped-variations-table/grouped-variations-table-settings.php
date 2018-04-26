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
    add_option( 'groupedvartable_option_buttonsenabled', 'This is my option value.');
    register_setting( 'groupedvartable_options_group', 'groupedvartable_option_buttonsenabled', 'groupedvartable_callback' );
    register_setting( 'groupedvartable_options_group', 'groupedvartable_option_mainwidth', 'groupedvartable_callback' );
    register_setting( 'groupedvartable_options_group', 'groupedvartable_option_mainsorter', 'groupedvartable_callback' );
}
add_action( 'admin_init', 'groupedvartable_register_settings' );

function groupedvartable_register_options_page() {
    add_options_page('Grouped Variations Table - Settings', 'Grouped Variations Table', 'manage_options', 'grouped-variations-table', 'groupedvartable_options_page');
}
add_action('admin_menu', 'groupedvartable_register_options_page');
add_action( 'admin_notices', 'groupedvartable_error_notice' );


function groupedvartable_error_notice()
{
    if(count(wc_get_attribute_taxonomies()) === 0)
    { ?>
        <div class="notice notice-warning is-dismissable">
            <p><?php _e("You have no attributes configured for your shop!","grouped-variations-table")?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Close this message.","grouped-variations-table")?></span></button>
        </div>
    <?php }
}

function groupedvartable_options_page()
{
    ?>
    <div>
        <h2>Grouped Variations Table</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'groupedvartable_options_group' ); ?>
            <h3><?php _e("Select Main Attribute for grouping","grouped-variations-table") ?></h3>
            <table style="width:50%">
                <tr valign="top">
                    <th scope="row"><label for="myplugin_option_name"><?php _e("Main Attribute for sorting:","grouped-variations-table")?></label></th>
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
                            <option <?php echo $selectedvalue=="full"?"selected":""?>  value="full"><?php _e("Full width","grouped-variations-table");?></option>
                            <option <?php echo $selectedvalue=="half"?"selected":""?> value="half"><?php _e("Half width","grouped-variations-table");?></option>
                            <option <?php echo $selectedvalue=="third"?"selected":""?> value="third"><?php _e("1 / 3 Width","grouped-variations-table");?></option>
                        </select>
                    </td>
                </tr>

            </table>
            <?php  submit_button(); ?>
        </form>
    </div>
    <?php
} ?>