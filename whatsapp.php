<?php

if(isset($_POST['phone'])) {


    global $wpdb;
    $table_name=$wpdb->prefix.'suresh';

    $db_temp_array = array();
    $db_temp_array ['number']=$_POST['phone'] ;
    //json_encode (temporary array);
    $db_array = json_encode($db_temp_array);
  if(isset($_POST['save'])){
    $wpdb->insert($table_name,
                    array(
                        'settings'=>$db_array
                    ),
                    array(
                        '%s'
                    )
                    
);
}
}

/*
Plugin Name: whatsapp
Plugin URI: https://fb.com/
Description: Just another contact form plugin. Simple but flexible.
Author: Sureshraja
Author URI: https://fb.com/

Version: 0.1
*/

function whatsapp_menu_item()
{
  add_submenu_page("options-general.php", "Social Share", "whatsapp Share", "manage_options", "social-share", "whatsapp_page"); 
}

add_action("admin_menu", "whatsapp_menu_item");

function whatsapp_page()
{
   ?>
      
         <h1>Chat Options</h1>
 
         <form method="post" action="options.php"> 
            <?php
               settings_fields("whatsapp_config_section");
 
               do_settings_sections("social-share"); //do_settings_sections("page name");Prints out all settings sections  added to a particular settings page
                
               submit_button(); 
            ?>
         </form>
      
   <?php        // wp default
}

function whatsapp_settings()
{
    add_settings_section("whatsapp_config_section", "", null, "social-share");// (settings_fields ,do_settings_sections)
 
    add_settings_field("social-share-whatsapp", "Do you want to display whatsapp share button?", "whatsapp_whatsapp_checkbox", "social-share", "whatsapp_config_section");
    add_settings_field("social-share-whats", "<label for='your_name'>Whatsapp Number</label>
    ", "whatsapp_Number", "social-share", "whatsapp_config_section");
     
    register_setting("whatsapp_config_section", "social-share-whatsapp");
    register_setting("whatsapp_config_section", "social-share-whats");
   
}
 
function whatsapp_whatsapp_checkbox()
{  
   ?>
        <input type="checkbox" name="social-share-whatsapp" value="1" 
        <?php 
        checked(1, get_option('social-share-whatsapp'), true); 
        
        ?> /> Check for Yes
   <?php
}

function whatsapp_Number()
{
    
    ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/css/intlTelInput.css" rel="stylesheet" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"></script>
    <input id="phone" value="<?=get_option('social-share-whats')?>" name="social-share-whats" type="tel">
<span id="valid-msg" class="hide">Valid</span>
<span id="error-msg" class="hide">Invalid number</span>
   
<script>
var telInput = $("#phone"),
  errorMsg = $("#error-msg"),
  validMsg = $("#valid-msg");

// initialise plugin
telInput.intlTelInput({

  allowExtensions: true,
  formatOnDisplay: true,
  autoFormat: true,
  autoHideDialCode: true,
  autoPlaceholder: true,
  defaultCountry: "auto",
  ipinfoToken: "yolo",

  nationalMode: false,
  numberType: "MOBILE",
  //onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
  preferredCountries: ['sa', 'ae', 'qa','om','bh','kw','ma'],
  preventInvalidNumbers: true,
  separateDialCode: true,
  initialCountry: "auto",
  geoIpLookup: function(callback) {
  $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
    var countryCode = (resp && resp.country) ? resp.country : "";
    callback(countryCode);
  });
},
   utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
});

var reset = function() {
  telInput.removeClass("error");
  errorMsg.addClass("hide");
  validMsg.addClass("hide");
};

// on blur: validate
telInput.blur(function() {
  reset();
  if ($.trim(telInput.val())) {
    if (telInput.intlTelInput("isValidNumber")) {
      validMsg.removeClass("hide");
    } else {
      telInput.addClass("error");
      errorMsg.removeClass("hide");
    }
  }
});

// on keyup / change flag: reset
telInput.on("keyup change", reset);
//telephone
</script>

    <?php
    
        
}
 
add_action("admin_init", "whatsapp_settings"); //admin_init-Fires as an admin screen or script is being initialized.

function add_whatsapp_icons($content)
{
    

    global $post; //wp default Retrieves post data given a post ID or post object.

    $url = get_permalink($post->ID); //Retrieves the full permalink for the current post or post ID.
    $url = esc_url($url);//Checks and cleans a URL.
    $ss = 2424234;
     
    if(get_option("social-share-whatsapp") == 1)
    {
        $ph = get_option("social-share-whats");
        echo "<script>alert('$ph')</script>";   
        $html = "<div class='icon'><a target='_blank' href='https://wa.me/$ph?text=Iam%20Sureshrajan%20thanks%20to%20contact%20me'" . $url . "'><i class='fab fa-whatsapp my-icon'></i>
        </a></div>";
    }
    
    return $content = $content . $html;
}

add_filter("the_content", "add_whatsapp_icons");

function whatsapp_style() 
{
    wp_register_style("social-share-style-file", plugin_dir_url(__FILE__) . "style.css"); 
    //path set // Magic PHP constant that means  plugin_dir_url(__FILE__) the "current file"
    
    wp_enqueue_style("social-share-style-file");
}

add_action("wp_enqueue_scripts", "whatsapp_style"); //Hooks a function on to a specific action.


//
function whatsapp_script() 
{
    wp_register_script("social-share-script-file", plugin_dir_url(__FILE__) . "number.js"); 
    //path set // Magic PHP constant that means  plugin_dir_url(__FILE__) the "current file"
    
    wp_enqueue_script("social-share-script-file");
}

add_action("wp_enqueue_scripts", "whatsapp_script"); //Hooks a function on to a specific action.



?>

