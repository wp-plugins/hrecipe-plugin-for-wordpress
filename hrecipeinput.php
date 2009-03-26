<?php
/* This is the form entry page that is used by hrecipe.php */
require_once('../../../wp-load.php'); // Ugly directory stuff
require_once('../../../wp-admin/admin.php'); // Ugly directory stuff
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
?>
<script type="text/javascript">//<!CDATA[
function clearForm()
{
document.getElementById('item-name').value = '';
document.getElementById('item-url').value = '';
document.getElementById('item-summary').value = '';
document.getElementById('item-ingredients').value = '';
document.getElementById('item-description').value = '';
document.getElementById('item-rating').value = '';
}
function getSelectValue(fieldId)
{
  var selectItem = document.getElementById(fieldId);
  var selectValue = selectItem.value;
  if ("" != selectValue)
  {
    return selectValue;
  }
  // avoid bug in old browsers where they never give any value directly
  var selectIdx = selectItem.selectedIndex;
  selectValue = selectItem.options[selectIdx].value;
  if ("" != selectValue)
  {
    return selectValue;
  }
  // and cope with IE
  selectValue = (selectItem.options[selectIdx]).text;
  return selectValue;
}
function submitForm()
{
var itemName = document.getElementById('item-name').value;
if ("" == itemName) {
alert("You need to provide a name for the recipe.");
return false;
}
var itemURL = document.getElementById('item-url').value;
var itemSummary = document.getElementById('item-summary').value;
var itemIngredients = document.getElementById('item-ingredients').value;
var itemDescription = document.getElementById('item-description').value;
//var itemRating = document.getElementById('item-rating').value;
var itemRating = getSelectValue('item-rating');
 window.parent.edInsertHRecipeDone(itemName, itemURL, itemSummary, itemIngredients, itemDescription, itemRating);
}
function abortForm()
{
window.parent.edInsertHRecipeAbort();
}
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>
<div class="wrap">
<h2>New Recipe</h2>
<form name="recipeForm">
<p>
<table class="form-table">
<tr valign="top">
<th scope="row">Name of item being recipeed:</th>
<td><input type="text" id="item-name" size="45" /></td>
</tr>
<tr valign="top">
<th scope="row">Reference URL:</th>
<td><input type="text" id="item-url" size="45" /></td>
</tr>

<tr valign="top">
<th scope="row">Summary:</th>
<td><input type="text" id="item-summary" size="45" /></td>
</tr>

<tr valign="top">
<th scope="row">Ingredients:</th>
<td><textarea id="item-ingredients" rows="10" cols="45"></textarea></td>
</tr>


<tr valign="top">
<th scope="row">Description:<br />(May use HTML)</th>
<td><textarea id="item-description" rows="10" cols="45"></textarea></td>
</tr>
<tr valign="top">
<th scope="row">Rating:<br />(number of stars)</th>
<td><select id="item-rating"><option></option>
<option>0.5</option>
<option>1.0</option>
<option>1.5</option>
<option>2.0</option>
<option>2.5</option>
<option>3.0</option>
<option>3.5</option>
<option>4.0</option>
<option>4.5</option>
<option>5.0</option>
</select></td>
</tr>
</table>
<p class="submit">
<input type="button" value="Insert" onclick="javascript:submitForm()" />
<input type="button" value="Back" onclick="javascript:abortForm()" />
</p>
</form></div>
</body>
</html>
