<?php

$l['xt_proffields'] = 'Additional Settings For Profile Fields';

$l['xt_proffields_proffields'] = 'Profile Fields';
$l['xt_proffields_error_js'] = 'The value is not valid';
$l['xt_proffields_no_req'] = 'Additional Informations &ndash; Not Required';
$l['xt_proffields_err_editable'] = 'You don\'t have permission to edit this profile field.';
$l['xt_proffields_err_nopf'] = 'There is no profile field found.';

$l['xt_proffields_reg'] = 'Display On Registration Page';
$l['xt_proffields_reg_desc'] = 'Should this field to be displayed on registration? We don\'t need to set this setting to <em>Yes</em> if this field is a required field. Will be applied if this field is editable.';
$l['xt_proffields_html'] = 'Allow HTML';
$l['xt_proffields_html_desc'] = 'Allow HTML for this field (will be applied for textbox and textarea only)?';
$l['xt_proffields_mycode'] = 'Allow MyCode';
$l['xt_proffields_mycode_desc'] = 'Allow MyCode for this field (will be applied for textbox and textarea only)?';
$l['xt_proffields_imgcode'] = 'Allow [img] Code';
$l['xt_proffields_imgcode_desc'] = 'Allow [img] code for this field (requires MyCode to be turned on)?';
$l['xt_proffields_videocode'] = 'Allow [video] Code';
$l['xt_proffields_videocode_desc'] = 'Allow [video] code for this field (requires MyCode to be turned on)?';
$l['xt_proffields_smilies'] = 'Allow Smilies';
$l['xt_proffields_smilies_desc'] = 'Allow smilies for this field (will be applied for textbox and textarea only)?';
$l['xt_proffields_badwords'] = 'Filter Badwords';
$l['xt_proffields_badwords_desc'] = 'Filter badwords for this field (will be applied for textbox and textarea only)?';
$l['xt_proffields_regex'] = 'Regular Expression';
$l['xt_proffields_regex_desc'] = 'Use regular expression for this field (will be applied for textbox, select box and radio buttons only).';
$l['xt_proffields_fml'] = 'Formatting Map List';
$l['xt_proffields_fml_desc'] = 'Leave it blank if we want to use the default output.';
$l['xt_proffields_cinp'] = 'Custom Input';
$l['xt_proffields_cinp_desc'] = 'We can place the input field anywhere in our edit profile page template (usercp_profile) and/or member_register template. To do this, at least put <span style="font-family:courier">{INPUT}</span> element in this setting, then put <span style="font-family: courier">{$GLOBALS[\'xtpf_inp\'][\'fidX\']}</span> variable anywhere in the edit profile template (usercp_profile) and/or member_register template, where X is the profile field id. Please remember that when we put something to this setting, then the input fields won\'t be displayed in the default Custom Profile Fields inputs block. Leave it blank if we want to use the default input.';
$l['xt_proffields_brv'] = 'Blank Replacement Value';
$l['xt_proffields_brv_desc'] = 'Will be used as value if there is no value supplied for this field. We can use conditional structures in this setting.';
$l['xt_proffields_df'] = 'Display Format';
$l['xt_proffields_df_desc'] = 'Use <span style="font-family: courier">{VALUE}</span> for representating the value of this field. We can use conditional structures in this setting. We need to put <span style="font-family: courier">{$xtpf[\'fidX\']}</span> or <span style="font-family: courier">{$GLOBALS[\'xtpf\'][\'fidX\']}</span> manually in our template for displaying it, where X is this field\'s ID.';
$l['xt_proffields_viewable'] = 'Viewable By Usergroup';
$l['xt_proffields_viewable_desc'] = 'Do not select any usergroup if this field is viewable for all uergroups. Will be applied for custom blocks only.';
$l['xt_proffields_editable'] = 'Editable By Usergroup';
$l['xt_proffields_editable_desc'] = 'Do not select any usergroup if this field is editable by all uergroups. Will be applied for editable and non required fields only.';