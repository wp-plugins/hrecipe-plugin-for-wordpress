<?php 
/*
Plugin Name: hRecipe Support for Editor
Plugin URI: http://tinobox.com/wordpress/hrecipe
Description: Allows the correct microformat content to be easily added for recipes.
Version: 0.1
Author: Dave Doolin
Author URI: http://tinobox.com/wordpress/
*/ 

/*  Copyright 2009 Dave Doolin

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

  /* hRecipe is derived from Andrew Scott's hReview plugin. */


// Find the full URL to the plugin directory and store it
global $hrecipe_plugin_url;
$hrecipe_plugin_url = dirname(get_settings('siteurl') . '/wp-content/plugins/' . preg_replace('/^.*wp-content\/plugins\//', '', str_replace('\\', '/', __FILE__)));

// Set up defaults for options
add_option('hrecipe_rating_text', 'My rating: ');
add_option('hrecipe_stars_text', 'stars');

// Set up hooks for the plugin
add_action('admin_footer', 'hrecipe_plugin_footer');
add_action('wp_head', 'hrecipe_plugin_head');
add_action('marker_css', 'hrecipe_plugin_css');
add_action('init', 'hrecipe_plugin_init');
add_action('admin_menu', 'hrecipe_plugin_menu');

function hrecipe_plugin_init() {
  if (get_user_option('rich_editing') == 'true') {
    // Include hooks for TinyMCE plugin
    add_filter('mce_external_plugins', 'hrecipe_plugin_mce_external_plugins');
    add_filter('mce_buttons_3', 'hrecipe_plugin_mce_buttons');
  }
} // End hrecipe_plugin_init()

function hrecipe_plugin_menu() {
  if (function_exists('add_options_page')) {
    add_options_page('hRecipe Options', 'hRecipe', 8, __FILE__,
      'hrecipe_plugin_options_page');
  }
} // End hrecipe_plugin_menu()

function hrecipe_plugin_options_page() {
?><div class="wrap">
<h2>hRecipe Support for Editor</h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table class="form-table">
<tr valign="top">
<th scope="row">Rating text</th>
<td><input type="text" name="hrecipe_rating_text" value="<?php echo get_option('hrecipe_rating_text'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Stars text</th>
<td><input type="text" name="hrecipe_stars_text" value="<?php echo get_option('hrecipe_stars_text'); ?>" /></td>
</tr>
</table>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hrecipe_rating_text,hrecipe_stars_text" />
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p></form></div>
<?php
} // End hrecipe_plugin_options_page()

function hrecipe_plugin_mce_external_plugins($plugins) {
  global $hrecipe_plugin_url;
  $plugins['hrecipe_plugin'] = $hrecipe_plugin_url . '/tinymceplugin/editor_plugin.js';
  return $plugins;
} // End hrecipe_plugin_mce_external_plugins()

function hrecipe_plugin_mce_buttons($buttons) {
  array_push($buttons, 'hrecipe_button');
  return $buttons;
} // End hrecipe_plugin_mce_buttons()

function hrecipe_plugin_footer() {
  global $hrecipe_plugin_url;
?>
<script type="text/javascript">//<![CDATA[

  var hrecipe_from_gui;

  function edInsertHRecipe() {
    tb_show("Add an hRecipe", "<?php echo $hrecipe_plugin_url; ?>/hrecipeinput.php?TB_iframe=true");
    hrecipe_from_gui = true; /** Called from TinyMCE **/
  } // End edInsertHRecipe()


  function edInsertHRecipeCode() {
    tb_show("Add an hRecipe", "<?php echo $hrecipe_plugin_url; ?>/hrecipeinput.php?TB_iframe=true");
    hrecipe_from_gui = false; /** Called from Quicktags **/
  } // End edInsertHRecipe()

  if (hrecipe_qttoolbar = document.getElementById("ed_toolbar")){
    newbutton = document.createElement("input");
    newbutton.type = "button";
    newbutton.id = "ed_hrecipe";
    newbutton.className = "ed_button";
    newbutton.value = "hRecipe";
    newbutton.onclick = edInsertHRecipeCode;
    hrecipe_qttoolbar.appendChild(newbutton);
  }

  function edInsertHRecipeAbort() {
    tb_remove();
  } // End edInsertHRecipeAbort()


  function edInsertHRecipeStars(itemRating) {
    var markup = '';
    if ( itemRating ) {
      var i, stars, itemRatingValue = parseFloat(itemRating);
      markup = '<p class="myrating"><?php echo get_option('hrecipe_rating_text');?>' +
        '<span class="rating">' + itemRating + '</span> <?php echo get_option('hrecipe_stars_text');?><br />';
      stars = 0;
      for ( i = 1; i <= itemRatingValue; i++ ) {
        stars++;
        markup = markup + '<img class="hrecipe_image" width="20" height="20" src="<?php echo $hrecipe_plugin_url;
?>/starfull.gif" alt="*" />';
      } // End for
      i = parseInt(itemRatingValue);
      if ( itemRatingValue - i > 0.1 ) {
        stars++;
        markup = markup + '<img class="hrecipe_image" width="20" height="20" src="<?php echo $hrecipe_plugin_url;
?>/starhalf.gif" alt="1/2" />';
      } // End if
      for ( i = stars; i < 5; i++ ) {
        markup = markup + '<img class="hrecipe_image" width="20" height="20" src="<?php echo $hrecipe_plugin_url;
?>/starempty.gif" alt="" />';
      } // End for
      markup = markup + '</p>';
    } // End if
    return markup;
  } // End edInsertHRecipeStars()


  function edInsertFormattedIngredients(itemIngredients) {
    var imarkup = '';
    var lines = '';
    lines = itemIngredients.split("\*");
    imarkup = '<p>Ingredients: <span class="ingredients">';
    imarkup += '<ol class="ingredients">';
    for(var i=0; i<lines.length; i++) {
      if (lines[i] == '') continue;
      imarkup += '<li class="ingredient">' + lines[i] + '</li>';
    }
    imarkup += '</ol>';
    imarkup += '</span></p>';
    return imarkup;
  }

  function format_instructions(itemDescription) {
    var imarkup = '';
    var lines = '';
    lines = itemDescription.split("\*");
    imarkup = '<p>Instructions: <span class="instructions">';
    imarkup += '<ol class="instructions">';
    for(var i=0; i<lines.length; i++) {
      if (lines[i] == '') continue;
      imarkup += '<li>' + lines[i] + '</li>';
    }
    imarkup += '</ol>';
    imarkup += '</span></p>';
    return imarkup;
  }


  function edInsertHRecipeDone(itemName, itemURL, itemSummary, itemIngredients, itemDescription, itemRating) {
    tb_remove();
    var HRecipeOutput = '<div class="hrecipe"><h5 class="item">Recipe: <span class="fn">' +
      ( itemURL ? '<a class="url" href="' + itemURL + '">' : '' ) +
      itemName +
      ( itemURL ? '</a>' : '') +
      '</span></h5>' +
      ( itemSummary ? '<p>Summary: <span class="summary">' + itemSummary + 
        '</span></p>' : '' ) +
      //( itemIngredients ? '<p>Ingredients: <span class="ingredients">' +
      //	itemIngredients + '</span></p>' : '') +
      ( itemIngredients ? edInsertFormattedIngredients(itemIngredients) : '' ) +
      ( itemDescription ? format_instructions(itemDescription) : '' ) +
      //( itemDescription ? 'Directions: <blockquote class="instructions">' +
      //  itemDescription + '</blockquote>' : '' ) +
      ( itemRating ? edInsertHRecipeStars(itemRating) : '' ) +
      '</div>';
    if (hrecipe_from_gui)
    {
      tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, HRecipeOutput);
      tinyMCE.execCommand('mceCleanup');
    } else
    {
      edInsertContent(edCanvas, HRecipeOutput);
    }
  } // End edInsertHRecipeDone()
//]]></script>
<?php
} // End hrecipe_plugin_footer()


function hrecipe_plugin_head() {
  global $hrecipe_plugin_url;
  echo '<link rel="stylesheet" type="text/css" media="screen" href="' .
    $hrecipe_plugin_url . '/hrecipe.css" />';
} // End hrecipe_plugin_head()


function hrecipe_plugin_css() {
  global $hrecipe_plugin_url;
  echo '@import url( ' . $hrecipe_plugin_url . '/hrecipe-editor.css );';
} // End hrecipe_plugin_css()

?>
