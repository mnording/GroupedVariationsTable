<?php
/*
Plugin Name:  Grouped Variations Table
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  Allowing you to group variations in sleak tables on the product page
Version:      1.1.0
Author:       mnording
Author URI:   https://mnording.com/
License:      MIT
License URI:  https://opensource.org/licenses/MIT
Text Domain:  grouped-variations-table
Domain Path:  /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require 'grouped-variations-table-settings.php';
/**
 * Check if WooCommerce is active
 **/
class GroupedVariationsTable
{
    public function __construct()
    {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action( 'admin_init', 'groupedvartable_register_settings' );
            add_action('woocommerce_before_single_product', array($this,'CheckIfPluginShouldLoad'), 10);
            add_action('wp_enqueue_scripts', array($this,'groupedvartable_adding_styles'));

            add_action( 'plugins_loaded', array($this,'groupedvartable_load_plugin_textdomain') );
        }
    }
    function groupedvartable_load_plugin_textdomain() {
        load_plugin_textdomain( 'grouped-variations-table', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }

    function CheckIfPluginShouldLoad()
    {
        global $woocommerce, $product, $post;
        if ($product->is_type('variable')) {

            // Product is a variable Product, then this might be ok to load
            $this->attrMasterSorter = get_option('groupedvartable_option_mainsorter'); // Get the master grouper to see if this variation uses it
            $loadPlugin = false;
            $this->available_product_variations =  $product->get_available_variations();

            //Go through the products variations, in order to see if it uses the one for grouping
            foreach($this->available_product_variations as $prod)
            {
                foreach($prod["attributes"] as $key => $attr)
                {

                    if($key === $this->attrMasterSorter)
                    {
                        // we found it!
                        $loadPlugin = true;
                    }
                }
            }
            if($loadPlugin) // Basicly, if its a variable prod and if it uses the attribute used for master grouping
            {
                add_action('woocommerce_after_single_product_summary', array($this,'groupedvartable_renderTable'), 1);
                add_filter( 'woocommerce_locate_template', array($this,'groupedvartable_reordertemplateloading'), 1, 3 );
            }
        }
    }





    function groupedvartable_adding_styles() {

        wp_register_style('grouped-variations-table', plugins_url('css/main.css', __FILE__));
        if ( function_exists( 'is_woocommerce' ) ) {
            if (  is_woocommerce() &&  is_product() ) {
                wp_enqueue_style('grouped-variations-table');
            }
        }

    }
    function groupedvartable_reordertemplateloading( $template, $template_name, $template_path ) {
        global $woocommerce;
        $_template = $template;
        if ( ! $template_path )
            $template_path = $woocommerce->template_url;

        $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if( ! $template && file_exists( $plugin_path . $template_name ) )
            $template = $plugin_path . $template_name;

        if ( ! $template )
            $template = $_template;

        return $template;
    }

    function groupedvartable_renderTable()
    {
        global $woocommerce, $product, $post;
// test if product is variable


        if ($product->is_type( 'variable' ))
        {

            $terms = get_terms(str_replace("attribute_","",$this->attrMasterSorter));
            $sorting = array();

            foreach($terms as $term)
            {
                $sorting[] = array(
                    "attribute" => $term->slug,
                    "name" => $term->name);
            }

            $tablearray = array();
            foreach($sorting as $attr)
            {

                $tablearray[$attr["attribute"]] = array();
                foreach ($this->available_product_variations as $value) {


                    if($value["attributes"][$this->attrMasterSorter] === $attr["attribute"])
                    {
                        $tablearray[$attr["attribute"]][] = array("name" => $attr["name"], "data" => $value);
                    }
                }
            }
            $this->CreateOutput($tablearray);
        }

    }


    function CreateOutput($tablearray)
    {

        global $product;
        echo "<div class='grouped-variation-table-container'>";
        foreach($tablearray as $grouping=>$tabledata)
        {
if(isset($tabledata[0])){
            echo "<table class='grouped-variation-table ".get_option('groupedvartable_option_mainwidth')."'>";
            echo "<caption>".$tabledata[0]["name"]."</caption>";
            echo "<thead>";
            foreach($this->GetTableHeaders($grouping) as $key){
                echo "<th>";
                echo $key;
                echo "</th>";
            }
            echo "<th>";
            echo _e("Price","grouped-variations-table");
            echo "</th>";
            echo "<th>";
            echo "</th>";
            echo "</thead>";
            echo "<tbody>";

            foreach($tabledata as $data)
            {
                echo "<tr>";
                foreach($this->GetAttributesWithoutMainGrouped($data["data"]["attributes"],$grouping) as $attr)
                {
                    echo "<td>";
                    echo $attr;
                    echo "</td>";
                }
                echo "<td>";
                echo $data["data"]["price_html"];
                echo "</td>";
                echo "<td>";
                echo "<a href='?add-to-cart=".$product->get_id()."&variation_id=".$data["data"]["variation_id"]."&".http_build_query($data["data"]["attributes"])."'>".__("Add to cart","grouped-variations-table")."</a>";
                echo "</td>";
                echo "</tr>";


            }
            echo "</tbody>";
            echo  "</table>";
        }

        }
        echo "</div>";
    }
    function GetTableHeaders($exclude)
    {
        global $product;
        $headers = array();
        foreach($product->get_variation_attributes() as $key => $attr){

            if(in_array($exclude,$attr))
            {
                continue;
            }
            $terms = get_taxonomies(array("name"=>$key),"objects");
            $headers[] = $terms[$key]->labels->singular_name;
        }
        return  $headers;
    }
    function GetAttributesWithoutMainGrouped($attributes,$exclude)
    {   $attributesClean = array();
        foreach($attributes as $attr)
        {
            if($attr ===  $exclude)
            {
                continue;
            }
            $attributesClean[] =  $attr;
        }
        return  $attributesClean;

    }

}



$GLOBALS['GroupedVariationsTable'] = new GroupedVariationsTable();