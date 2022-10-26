<?php
/*
Plugin Name: Benson WordFilter Plugin
Description: Replaces a string of words
Version: 1.0
Author: Kogi
*/

if (! defined( 'ABSPATH')) exit; //Exit if accessed directly

class OurWordFilterPlugin{

    function __construct(){
        add_action('admin_init',array($this,'ourSettings'));
        add_action('admin_menu',array($this,'openMenu'));
        if(get_option('plugin-words-to-filter')) add_filter('the_content',array($this, 'filterLogic'));
    }
    
    function ourSettings() {
        add_settings_section('replacement-text-section',null,null,'word-filter-options');
        register_setting('replacementFields','replacementText');
        add_settings_field('replacement-text','filtered text',array($this,'replacementFieldHTML'),'word-filter-options','replacement-text-section');
    }

    function replacementFieldHTML() { ?>
    <input type="text" name="replacementText" value="<?php echo esc_attr(get_option('replacementText','***')); ?>">
    <p class="description">Leave blank to simply remove filtered words</p>

  <?php  }

  function filterLogic($content) {
     $badWords = explode(',', get_option('plugin-words-to-filter'));
     $badWordsTrimmed = array_map('trim',$badWords);
    return str_ireplace($badWordsTrimmed,esc_html(get_option('replacementText','****')),$content);
  }

  function openMenu(){
      // This method takes 7 arguments
    $mainPageHook = add_menu_page('Words to Filter','Word Filter','manage_options','ourwordfilter',array($this,'wordFilterPage'),'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+Cg==',90);
     add_submenu_page('ourwordfilter','Words to Filter','Words List','manage_options','ourwordfilter',array($this, 'wordFilterPage'));
     add_submenu_page('ourwordfilter','Word filter options','Options','manage_options','word-filter-options',array($this, 'optionSubPage'));
     add_action("load-{$mainPageHook}",array($this,'mainPageAssets'));
    }

    function mainPageAssets() {
        wp_enqueue_style('filterAdminCss',plugin_dir_url(__FILE__) . 'styles.css');
    }

    function handleForm() {
       if(wp_verify_nonce($_POST['ourNonce'],'saveFilteredWords') AND current_user_can('manage_options')){
        update_option('plugin_words_to_filter',sanitize_text_field($_POST['plugin-words-to-filter'])); ?>
        <div class="updated">
            <p>Your filtered words were saved</p>
        </div>
    <?php   } else{ ?>
         <div class="error">
            <p>Sorry you do not have permission to perform that action.</p>
         </div>
     <?php  } ?>
   <?php }
  function wordFilterPage() { ?>
    <div class="wrap">
        <h1>Word Filter Page</h1>
        <?php if($_POST["justSubmitted"] == "true") $this->handleForm();?>
        <form action="" method="post">
            <input type="hidden" name="justSubmitted" value="true">
            <?php wp_nonce_field('saveFilteredWords','ourNonce'); ?>
            <label for="plugin-words-to-filter">
                <p>Enter words to filter from your content seperated by <strong>Commas</strong></p>
            <div class="word-filter__flex-container">
        <textarea name="plugin-words-to-filter" id="plugin-words-to-filter" placeholder="New,Old,Mad,Dog,....." cols="30" rows="10"><?php echo  esc_textarea(get_option('plugin-words-to-filter')); ?></textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
         </form>    
    </div>
   <?php }

  function optionSubPage() { ?>
  <div class="row">
  <h1>Word Filter Options</h1>
  <form action="options.php" method="POST">
    <?php
    settings_errors();
     settings_fields('replacementFields');
     do_settings_sections('word-filter-options');
    submit_button(); ?>
  </form>
  </div>
   <?php }


}



$ourWordFilterPlugin = new OurWordFilterPlugin();