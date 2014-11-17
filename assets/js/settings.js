/**
 * Created by Bobby on 11/2/14.
 */
jQuery(document).ready(function ($) {

    var dir = jQuery('[name="agsg_install_url"]').val(); // dir url of install from hidden meta
    var page = dir + 'class-agsgPlugin.php';
    var htmlTagATTList = []; // holds our html attributes
    var shortcodeTagATTList = []; // holds our shortcode attributes
    var conditionList = []; // holds our shortcode conditions
    var tinyMCEids = []; // holds our shortcode conditions tinyMCEs
    var serializedMatchData = [];

    // prevent attribute_matching_form submit
    jQuery("#attribute_matching_form").submit(function (event) {
        // setup some local variables
        var form = jQuery(this);
        // let's select and cache all the fields
        var inputs = jQuery(form).find("input, select, button, textarea");
        // serialize the data in the form
        serializedMatchData = jQuery(form).serialize();
        alert('Attributes Mapped!');
        // prevent default posting of form
        event.preventDefault();
    });

    /**
     * Begin listen for the link to load our attributes for mapping
     */
    jQuery('#map_att_description a').click(function (e) {
        var html_att_options = '';
        var htmlTagATTs = [];
        var att_options = '';
        var shortcodeTagATTs = [];
        var selects = '';
        var error = false;
        var html = '<h2>Instructions</h2>';
        html += '<p>Please select your matches then click the "x" in the upper right hand screen.<br/>' +
            '<span class="error"><span class="dashicons dashicons-welcome-write-blog"></span>Important Note: These values will not be set unless you press the button below!</span></p>';
        var form = jQuery('#attribute_matching_form');
        var longest = 0;

        // filter arrays to take out the 0's
        for (var i = 0; i < htmlTagATTList.length; i++) {
            // if current index value is not == 0 add to the list
            if (htmlTagATTList[i] !== 0) {
                htmlTagATTs.push(htmlTagATTList[i]);
            }
        }// filter arrays to take out the 0's
        for (var i = 0; i < shortcodeTagATTList.length; i++) {
            // if current index value is not == 0 add to the list
            if (shortcodeTagATTList[i] !== 0) {
                shortcodeTagATTs.push(shortcodeTagATTList[i]);
            }
        }
        if (htmlTagATTs.length >= shortcodeTagATTs.length) {
            longest = htmlTagATTs.length;
        } else {
            longest = shortcodeTagATTs.length;
        }
        html_att_options += '<option value="select">Select HTML TAG Attribute</option>';
        att_options += '<option value="select">Select Shortcode Attribute</option>';
        // build select options
        for (var j = 0; j < longest; j++) {
            var html_att_opt = jQuery('#' + htmlTagATTs[j]);
            var att_opt = jQuery('#' + shortcodeTagATTs[j]);
            var html_att_opt_val = jQuery(html_att_opt).val();
            var att_opt_val = jQuery(att_opt).val();
            // any are blank
            if (att_opt_val === '' || html_att_opt_val === '') { // if empty create an error
                error = 'You have blank HTML TAG attribute names or shortcode attribute names.  Cannot performing mapping unless all attribute names are filled in.  Please close this container and look at the attribute sections, any name fields that are blank will be called out in red.';
                if (html_att_opt_val == '') {
                    jQuery(html_att_opt).parent().find('.error').remove().parent().removeClass('form-invalid'); // lets not double up the errors
                    jQuery(html_att_opt).parent().prepend('<span class="error">Must have data to map.</span>').parent().addClass('form-invalid');
                }
                if (att_opt_val === '') {
                    jQuery(att_opt).parent().find('.error').remove().parent().removeClass('form-invalid'); // lets not double up the errors
                    jQuery(att_opt).parent().prepend('<span class="error">Must have data to map.</span>').parent().addClass('form-invalid');
                }
            } else { // neither are blank
                if (html_att_opt_val) { // show if defined
                    jQuery(html_att_opt).parent().find('.error').remove().parent().removeClass('form-invalid');
                    html_att_options += '<option id="match_att_name_opt_' + j + '" value="' + html_att_opt_val + '">' + jQuery(html_att_opt).parent().prev().text() + ' (' + html_att_opt_val + ')</option>';
                }
                if (att_opt_val) { // show if defined
                    jQuery(att_opt).parent().find('.error').remove().parent().removeClass('form-invalid');
                    att_options += '<option id="match_att_name_opt_' + j + '" value="' + att_opt_val + '">' + jQuery(att_opt).parent().prev().text() + ' (' + att_opt_val + ')</option>';
                }
            }
        }
        if (!error) {
            // build selects
            for (var i = 0; i < longest; i++) {
                selects += '<select id="match_html_tag_att_name_' + i + '" name="match_html_tag_att_name[]">' + html_att_options + '</select> = ';
                selects += '<select id="match_att_name_' + i + '" name="match_att_name[]">' + att_options + '</select>';
            }
            html += selects;
            html += '<br/><p style="text-align: center;"><input name="submit_attribute_matching_form" type="submit" class="button-primary" value="Set Mapping" /></p>';
        } else {
            html = error;
        }
        jQuery(form).html(html);
    });
    /**
     * End listen for the link to load our attributes for mapping
     */
    /**
     * Begin conditions sections
     */
        // listen for radio options of the has_conditions question @todo - Make sure this errors out if there are no shortcode attrubutes
    jQuery('[name="agsg_has_conditions"]').change(function (e) {
        var val = jQuery(this).val();
        if (val == 'Yes') {
            conditionList.push('shortcode_condition_type');
            var html = '<tr class="type_template"><th scope="row">Select IF</th><td><select id="shortcode_condition_type" name="agsg_shortcode_condition_type[]"><option value="select">Select IF</option><option value="if">IF</option></select> ' +
                '<label for="agsg_shortcode_condition_type[]"><span class="description"><br/>' +
                '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF" to do something when an attribute(s) evaluation is "TRUE".<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this condition and the data associated with it."></span><br/>' +
                '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Important Note:</span> TinyMCE will not load TWICE for a condition. What this means is once you remove a condition, if you add it again, you will no longer have the editor so you must create conditions until you have passed the id number of the condition you deleted, then delete the conditions without TinyMCE unless you want to use HTML markup.<br/>' +
                '</span><br/>' +
                '</td></tr>';
            jQuery(this).parent().parent().parent().after(html);
            jQuery('[for="agsg_shortcode_condition_type[]"] .dashicons-dismiss').click(function (e) {
                if (window.confirm("You will lose this condition and its' data. Continue?")) {
                    // check what condition was just set so the proper amount can be removed.
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().remove();

                    // cycle through list of condition ids
                    for (var i = 0; i < conditionList.length; i++) {
                        // check current id is the same as the -this-
                        if (conditionList[i] === jQuery(this).parent().parent().prev().attr('id')) {
                            conditionList[i] = 0; // if so set this index to 0 so it won't be used
                        }
                    }
                }
            });
            jQuery('#shortcode_condition_type').change(function (e) {
                var val = jQuery(this).find('option:selected').val();
                var html = '';
                if (jQuery(this).attr('id') == 'shortcode_condition_type') {
                    var condition_id_num = jQuery(this).attr('id').replace('shortcode_condition_type', '');
                    condition_id_num = 1;
                } else {
                    var condition_id_num = jQuery(this).attr('id').replace('shortcode_condition_type_', '');
                }
                var loadTiny = false;
                if (val == 'select') {
                    jQuery(this).parent().parent().parent().find('.for_condition_number_' + condition_id_num).remove();
                }
                else if (val == 'if') {
                    loadTiny = true;
                    // attribute to evaluate
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Evaluate Attribute</th><td><select id="shortcode_condition_' + condition_id_num + '_attribute" class="shortcode_condition_attribute" name="agsg_shortcode_condition_attribute[]">';
                    html += getShortcodeAttributeOptionsList();
                    html += '</select><label for="shortcode_condition_' + condition_id_num + '_attribute"><span class="description">This is the shortcode attribute you want to check the value of.</span></label>' +
                        '<div id="reset_for_attribute_condition_number_' + condition_id_num + '" class="reset_condition_container"><span class="reset_condition">Reset these options</span><span class="dashicons dashicons-randomize"></span></div></td></tr>';

                    // operator type == / <= / >= / < / >
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Operator</th><td><select id="shortcode_condition_' + condition_id_num + '_operator" class="shortcode_condition_operator" name="agsg_shortcode_condition_operator[]">';
                    html += '<option value="==">equal to value</option>' +
                        '<option value="!=">not equal to value</option>' +
                        '<option value="<=">less than or equal to value</option>' +
                        '<option value="<">less than value</option>' +
                        '<option value=">=">greater than or equal to value</option>' +
                        '<option value=">">greater than value</option>' +
                        '</select><label for="shortcode_condition_' + condition_id_num + '_operator"><span class="description">This is the operator to use in evaluating the shortcode attribute you want to check against the value you input in the text field below.</span></label>';
                    // value to check against
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Value</th><td><input id="shortcode_condition_' + condition_id_num + '_value"" class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_value[]">' +
                        '<label for="shortcode_condition_' + condition_id_num + '_value"><span class="description">This is the value the value to check the shortcode attribute against using the operator above.<br/>' +
                        '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Important Note:</span>If you leave this blank it will be considered an empty string.</span></label>' +
                        '</td></tr>';
                    // action to do if true
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">If the evaluation of the attribute selected and the value entered using the operator selected results to true, display the content entered here.</th><td><textarea name="agsg_shortcode_condition_tinyMCE[]" cols="50" rows="5" id="shortcode_condition_' + condition_id_num + '_tinyMCE">Enter content you want displayed here.  Remember that you can reference any attribute named above using the shortcode attribute reference syntax.  They can be inserted anywhere you can place text in the TinyMCE editor.  Play around with it and don\'t hestistate to report a bug if you can find one.</textarea><br><label for="description"><span class="description">This is where you can add some conditional content to appear above the normally rendered content if this is an enclosing shortcode or decide what content is displayed if this is a self closing shortcode depending on an attribute value.</span></label></td></tr>';
                    // push this id into the array
                    tinyMCEids.push('shortcode_condition_' + condition_id_num + '_tinyMCE');

                }
                jQuery(this).parent().parent().after(html);
                if (loadTiny) loadTinyMCE('#shortcode_condition_' + condition_id_num + '_tinyMCE', tinyMCEids); // load if needed
                // set reset button
                jQuery('#reset_for_attribute_condition_number_' + condition_id_num).click(function (e) {
                    jQuery('#shortcode_condition_' + condition_id_num + '_attribute').html(getShortcodeAttributeOptionsList('reset'));
                });
            });
            jQuery('#add_shortcode_condition').show();
        } else {
            if (window.confirm("You will lose all conditions. Continue?")) {
                jQuery('[name="agsg_shortcode_condition_type[]"]').parent().parent().remove();
                jQuery('.shortcode_condition_attribute').parent().parent().remove();
                jQuery('#add_shortcode_condition').hide();
            }
            else { // canceled
                jQuery('#has_conditions_Yes').attr('checked', 'checked');
            }
        }
    });
    // listen for add button
    var shortcode_condition_type_count = jQuery('[name="agsg_shortcode_condition_type[]"]').length;
    jQuery('#add_shortcode_condition').click(function (e) {
        // count the types
        shortcode_condition_type_count++;
        var count = shortcode_condition_type_count + 1;

        var type_clone = jQuery('.type_template').clone(true, true).removeClass('type_template').removeClass('form-invalid');
        jQuery("th", type_clone).text('Select an IF Statement Type ' + count);
        jQuery("select", type_clone).attr('id', jQuery("select", type_clone).attr('id') + '_' + count);
        jQuery("label", type_clone).attr('for', jQuery("label", type_clone).attr('for') + '_' + count);
        jQuery(".error", type_clone).remove();
        jQuery(".dashicons-dismiss", type_clone).show();

        conditionList.push('shortcode_condition_type_' + count);

        count--;
        if (!count == 1) {
            jQuery('#shortcode_condition_type' + '_' + count).parent().parent().parent().append(type_clone);
        } else {
            jQuery('#shortcode_condition_type').parent().parent().parent().append(type_clone);
        }
    });
    /**
     * End conditions section
     */
    /**
     * Begin html tag section
     */
        // listen for radio options of the has_html_tag_atts question
    jQuery('[name="agsg_has_html_tag_atts"]').change(function (e) {
        var val = jQuery(this).val();
        if (val == 'Yes') {
            htmlTagATTList.push('html_tag_att_name');
            var html = '<tr class="html_tag_template_name"><th scope="row">HTML TAG Attribute 1 Name</th><td><input type="text" value="" placeholder="" name="agsg_html_tag_att_name[]" id="html_tag_att_name">' +
                '<label for="html_tag_att_name"><span class="description">This is the name for the HTML TAG attribute.<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this attribute and the static value associated with it."></span></span></label>' +
                '</td></tr>';
            html += '<tr class="html_tag_template_value"><th scope="row">HTML TAG Attribute 1 Set Value</th><td><input type="text" value="" placeholder="" name="agsg_html_tag_default[]" id="html_tag_default">' +
                '<label for="html_tag_default"><span class="description">The SET value for the attribute.<br/>  ' +
                '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> If you want to match this HTML TAG attribute name with a shortcode attribute, leave this blank and put the default value in the shortcode attributes "Default Value" field. Then map them using the link that becomes visible when you\'ve answered both questions </span></label>' +
                '</td></tr>';
            jQuery(this).parent().parent().parent().after(html);
            jQuery('[for="html_tag_att_name"] .dashicons-dismiss').click(function (e) {
//                if(jQuery('#has_atts_Yes').is(':checked')){
                if (window.confirm("You will lose this HTML TAG attribute and must reset attribute mapping if set already. Continue?")) {
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().remove();
                    if (serializedMatchData.length) serializedMatchData = 0;
                    // cycle through list of tag ids
                    for (var i = 0; i < htmlTagATTList.length; i++) {
                        // check current id is the same as the -this-
                        if (htmlTagATTList[i] === jQuery(this).parent().parent().prev().attr('id')) {
                            htmlTagATTList[i] = 0; // if so set this index to 0 so it won't be recognized in matching area
                        }
                    }
                }
//                }
            });
            jQuery('#add_html_tag_att').show();
            if (jQuery('.template_name').length) {
                jQuery('#map_attributes').show();
            }
        } else {
            if (window.confirm("You will lose all attributes. Continue?")) {
                if (serializedMatchData.length) serializedMatchData = 0;
                jQuery('[name="agsg_html_tag_att_name[]"]').parent().parent().remove();
                jQuery('[name="agsg_html_tag_default[]"]').parent().parent().remove();
                jQuery('#add_html_tag_att').hide();
                jQuery('#map_attributes').hide();
            }
            else {
                jQuery('#has_html_tag_atts_Yes').attr('checked', 'checked');
            }
        }
    });
    // listen for add attribute button
    var html_tag_att_count = jQuery('[name="agsg_html_tag_att_name[]"]').length;
    jQuery('#add_html_tag_att').click(function (e) {
        // count the names ( defaults will be the same)
        html_tag_att_count++;
        var count = html_tag_att_count + 1;

        var name_clone = jQuery('.html_tag_template_name').clone(true, true).removeClass('html_tag_template_name').removeClass('form-invalid');
        jQuery("th", name_clone).text('HTML TAG Attribute ' + count + ' Name');
        jQuery("input", name_clone).attr('id', jQuery("input", name_clone).attr('id') + '_' + count);
        jQuery("label", name_clone).attr('for', jQuery("label", name_clone).attr('for') + '_' + count);
        jQuery(".error", name_clone).remove();

        htmlTagATTList.push('html_tag_att_name_' + count);

        jQuery(".dashicons-dismiss", name_clone).show();
        var value_clone = jQuery('.html_tag_template_value').clone(true, true).removeClass('html_tag_template_value');
        jQuery("th", value_clone).text('HTML TAG Attribute ' + count + ' Set Value');
        jQuery("input", value_clone).attr('id', jQuery("input", value_clone).attr('id') + '_' + count);
        jQuery("label", value_clone).attr('for', jQuery("label", value_clone).attr('for') + '_' + count);

        count--;
        if (!count == 1) {
            jQuery('#html_tag_default' + '_' + count).parent().parent().after(value_clone).after(name_clone);
        } else {
            jQuery('#html_tag_default').parent().parent().after(value_clone).after(name_clone);
        }
    });
    /**
     * End html tag section
     */
    /**
     * Begin atts section
     */
        // listen for radio options of the has_atts question
    jQuery('[name="agsg_has_atts"]').change(function (e) {
        var val = jQuery(this).val();
        if (val == 'Yes') {
            shortcodeTagATTList.push('att_name');
            var html = '<tr class="template_name"><th scope="row">Attribute 1 Name</th><td><input type="text" value="" placeholder="" name="agsg_att_name[]" id="att_name">' +
                '<label for="att_name"><span class="description">This is the name for the attribute <span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this attribute and the default vaule associated with it."></span></span></label>' +
                '</td></tr>';
            html += '<tr class="template_value"><th scope="row">Attribute 1 Default Value</th><td><input type="text" value="" placeholder="" name="agsg_default[]" id="default">' +
                '<label for="default"><span class="description">The default value for the attribute.</span></label>' +
                '</td></tr>';
            jQuery(this).parent().parent().parent().after(html);
            // listen for shortcode attributes to keep input valid
            jQuery('[name="agsg_att_name[]"]').change(function (e) {
                var val = jQuery(this).val();
                val = val.trim().replace(' ', '_', 'gi');
                jQuery(this).val(val);
            });
            jQuery('[for="att_name"] .dashicons-dismiss').click(function (e) {
                if (window.confirm("You will lose this attribute, you must reset attribute mapping if set already, and if you were using this for a condition you need to delete is as well.  Continue?")) {
                    if (serializedMatchData.length) serializedMatchData = 0;
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().remove();
                    // cycle through list of shortcode ids
                    for (var i = 0; i < shortcodeTagATTList.length; i++) {
                        // check current id is the same as the -this-
                        if (shortcodeTagATTList[i] === jQuery(this).parent().parent().prev().attr('id')) {
                            shortcodeTagATTList[i] = 0; // if so set this index to 0 so it won't be recognized in matching area
                        }
                    }
                }
            });
            jQuery('#add_shortcode_att').show();
            if (jQuery('.html_tag_template_name').length) {
                jQuery('#map_attributes').show();
            }
        } else {
            if (window.confirm("You will lose all attributes. Continue?")) {
                if (serializedMatchData.length) {
                    serializedMatchData = 0;
                }
                jQuery('[name="agsg_att_name[]"]').parent().parent().remove();
                jQuery('[name="agsg_default[]"]').parent().parent().remove();
                jQuery('#add_shortcode_att').hide();
                jQuery('#map_attributes').hide();
            }
            else {
                jQuery('#has_atts_Yes').attr('checked', 'checked');
            }
        }
    });
    // listen for add attribute button
    var att_name_count = jQuery('[name="agsg_att_name[]"]').length;
    jQuery('#add_shortcode_att').click(function (e) {
        // count the names ( defaults will be the same)
        att_name_count++;
        var count = att_name_count + 1;

        var name_clone = jQuery('.template_name').clone(true, true).removeClass('template_name').removeClass('form-invalid');
        jQuery("th", name_clone).text('Attribute ' + count + ' Name');
        jQuery("input", name_clone).attr('id', jQuery("input", name_clone).attr('id') + '_' + count);
        jQuery("label", name_clone).attr('for', jQuery("label", name_clone).attr('for') + '_' + count);
        jQuery(".error", name_clone).remove();

        shortcodeTagATTList.push('att_name_' + count);

        jQuery(".dashicons-dismiss", name_clone).show();
        var value_clone = jQuery('.template_value').clone(true, true).removeClass('template_value');
        jQuery("th", value_clone).text('Attribute ' + count + ' Value');
        jQuery("input", value_clone).attr('id', jQuery("input", value_clone).attr('id') + '_' + count);
        jQuery("label", value_clone).attr('for', jQuery("label", value_clone).attr('for') + '_' + count);

        count--;
        if (!count == 1) {
            jQuery('#default' + '_' + count).parent().parent().after(value_clone).after(name_clone);
        } else {
            jQuery('#default').parent().parent().after(value_clone).after(name_clone);
        }
    });
    /**
     * End atts section
     */
    /**
     * Begin field .change modifications to force valid input
     */
        // listen for shortcode tag to keep input valid
    jQuery('[name="agsg_shortcode_tag_name"]').change(function (e) {
        var val = jQuery(this).val();
        val = val.replace(/[\[\]]/g, '', 'gi').trim().replace(' ', '_', 'gi');
        jQuery(this).val(val);
    });
    // listen for html tag to keep input valid
    jQuery('[name="agsg_html_tag_name"]').change(function (e) {
        var val = jQuery(this).val();
        val = val.replace(/[<>]/g, '', 'gi').trim();
        jQuery(this).val(val);
    });
    // listen for inline styles to keep input valid - ie strip off white space from sides
    jQuery('[name="agsg_inline_styles"]').change(function (e) {
        var val = jQuery(this).val();
        val = val.trim();
        jQuery(this).val(val);
    });
    /**
     * End field .change modifications to force valid input
     */
    /**
     * Begin Shortcode Generator Forms Post Code
     */
    /**
     * Begin eagsg page form
     */
//   bind tinyMCE to forms serialized data
    var request = '';
    jQuery("#agsg_eagsg_form").submit(function (event) {
        // prevent default posting of form
        event.preventDefault();
        // abort any pending request
        if (request) {
            request.abort();
        }
        // save all tinyMCE fields before serializing form data IF there is one.
        if (typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
        // setup some local variables
        var form = jQuery(this);
        // let's select and cache all the fields
        var inputs = jQuery(form).find("input, select, button, textarea");
        // serialize the data in the form
        var serializedData = jQuery(form).serialize();

        // get values for validation
        var shortcode_tag_name = jQuery('[name="agsg_shortcode_tag_name"]');
        var html_tag_name = jQuery('[name="agsg_html_tag_name"]');

        var has_html_tag_atts_Yes = jQuery('#has_html_tag_atts_Yes').is(':checked');
        var has_conditions_Yes = jQuery('#has_conditions_Yes').is(':checked');
        var has_atts_No = jQuery('#has_atts_No').is(':checked');
        var inline_styles = jQuery('#inline_styles');
        var preview = jQuery('#preview_Yes');
        var regenerate = jQuery('#regenerate_Yes');

        var has_atts = jQuery('[name="agsg_has_atts"]'); // not used for validation check but to set the error msg
        var err = false;

        /**
         * Begin Validation Area
         */
        if (serializedMatchData === 0) {
            alert('After attributes have been mapped and you delete one you must re-map them to ensure nothing goes wrong during generating.');
            err = true;
        } else {
            err = false;
        }
        // are there any html atts
        if (has_html_tag_atts_Yes) {
            // cycle through them and ensure there are no blanks
            jQuery('[name="agsg_html_tag_att_name[]"]').each(function (e) {
                // if blank
                if (jQuery(this).val() === '') {
                    jQuery(this).parent().parent().addClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                    jQuery(this).parent().prepend('<span class="error">Must not be empty.  Please fill in or remove.</span>');
                    err = true;
                }
                else { // if filled
                    // check to see if 'id' / 'class' / 'style' defined as html att
                    if (jQuery(this).val() === 'id' || jQuery(this).val() === 'class' || jQuery(this).val() === 'style') {
                        jQuery(this).parent().parent().addClass('form-invalid');
                        jQuery(this).parent().find('.error').remove();
                        jQuery(this).parent().prepend('<span class="error">Must not have id, class, or style html attributes defined as they are automatically defined.  Please remove.  You just need to create them as shortcode attributes below to use them.</span>');
                        err = true;
                    }
                    else { // passed validation - ensure there no error messages
                        jQuery(this).parent().parent().removeClass('form-invalid');
                        jQuery(this).parent().find('.error').remove();
                    }
                }
            });
        }
        // check inline styles are properly formatted
        if (inline_styles.val() !== '') {
            var inline_matches = inline_styles.val().match(/([a-z-]+: [0-9a-z#(). -]+;)/g);
            if (Array.isArray(inline_matches) === false) {
                inline_styles.parent().parent().addClass('form-invalid');
                inline_styles.parent().find('.error').remove();
                inline_styles.parent().prepend('<span class="error">Must properly format inline styles. Please cut out your text in input using "CTRL+x" and see placeholder for example, then paster your text back in and reformat.</span>');
                err = true;
            } else { // passed
                inline_styles.parent().parent().removeClass('form-invalid');
                inline_styles.parent().find('.error').remove();
            }
        }
        // check for blank attribute names if Yes is checked
        if (!has_atts_No) {
            jQuery('[name="agsg_att_name[]"]').each(function (e) {
                var val = jQuery(this).val();
                if (jQuery(this).val() === '') {
                    jQuery(this).parent().parent().addClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                    jQuery(this).parent().prepend('<span class="error">Must not be empty.  Please fill in or remove.</span>');
                    err = true;
                } else { // great its not blank... check for numbers at the beginning of the attribute - (numbers at beginning screw up variable generation)
                    var att_name_matches = val.match(/(^[0-9]+)/);
                    if (Array.isArray(att_name_matches) === true) {
                        jQuery(this).parent().parent().addClass('form-invalid');
                        jQuery(this).parent().find('.error').remove();
                        jQuery(this).parent().prepend('<span class="error">Must not have number at beginning of shortcode.  Please remove.  Place a letter before the number, so for example, if setting up a fighter stats table, you could this. Instead of an attribute named "1_wp" where "1" means "fighter 1" and "wp" means "win percentage" you could do "f1_wp".</span>');
                    } else {
                        jQuery(this).parent().parent().removeClass('form-invalid');
                        jQuery(this).parent().find('.error').remove();
                    }
                }
            });
        }
        // check that if and evaluate are selected if trying to use conditions
        if (has_conditions_Yes) {
            // evaluate
            jQuery('[name="agsg_shortcode_condition_attribute[]"]').each(function (e) {
                if (jQuery(this).find('option:selected').val() === 'select') {
                    jQuery(this).parent().parent().addClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                    jQuery(this).parent().prepend('<span class="error">Must select an attribute.</span>');
                    err = true;
                } else {
                    jQuery(this).parent().parent().removeClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                }
            });
            // if
            jQuery('[name="agsg_shortcode_condition_type[]"]').each(function (e) {
                if (jQuery(this).find('option:selected').val() === 'select') {
                    jQuery(this).parent().parent().addClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                    jQuery(this).parent().prepend('<span class="error">Must select IF.</span>');
                    err = true;
                } else {
                    jQuery(this).parent().parent().removeClass('form-invalid');
                    jQuery(this).parent().find('.error').remove();
                }
            });
        }
        // check shortcode tag
        if (shortcode_tag_name.val() === '') {
            jQuery(shortcode_tag_name).parent().parent().addClass('form-invalid');
            jQuery(shortcode_tag_name).parent().find('.error').remove();
            jQuery(shortcode_tag_name).parent().prepend('<span class="error">Must have a tag.</span>');
            err = true;
        } else {
            jQuery(shortcode_tag_name).parent().parent().removeClass('form-invalid').find('.error').remove();
        }
        // check html tag
        if (html_tag_name.val() === '') {
            jQuery(html_tag_name).parent().parent().addClass('form-invalid');
            jQuery(html_tag_name).parent().find('.error').remove();
            jQuery(html_tag_name).parent().prepend('<span class="error">Must have a tag name.</span>');
            err = true;
        } else {
            jQuery(html_tag_name).parent().parent().removeClass('form-invalid').find('.error').remove();
        }
        // if conditions are present then make sure we have attributes present
        if (has_conditions_Yes && has_atts_No) {
            jQuery(has_atts).parent().parent().addClass('form-invalid');
            jQuery(has_atts).parent().find('.error').remove();
            jQuery('#has_atts_Yes').parent().prepend('<span class="error">Must have attributes to use the conditionals.</span>');
            err = true;
        } else {
            jQuery(has_atts).parent().parent().removeClass('form-invalid').find('.error').remove();
        }
        // if there are a preview and regenerate flag error out
        if (preview.is(':checked') && regenerate.is(':checked')) {
            jQuery(preview).parent().parent().find('.error').remove();
            preview.parent().prepend('<span class="error">Must NOT have both flags set.</span>');
            jQuery(regenerate).parent().parent().find('.error').remove();
            regenerate.parent().prepend('<span class="error">Must NOT have both flags set.</span>');
            jQuery(preview).parent().parent().parent().addClass('form-invalid');
            jQuery(regenerate).parent().parent().parent().addClass('form-invalid');
            err = true;
        } else {
            jQuery(preview).parent().parent().find('.error').remove();
            jQuery(preview).parent().parent().parent().removeClass('form-invalid');
            jQuery(regenerate).parent().parent().find('.error').remove();
            jQuery(regenerate).parent().parent().parent().removeClass('form-invalid');
        }
        /**
         * End Validation Area
         */


        // no errors
        if (!err) {
            // set type of shortcode if you wish to extend it to do more.
            var type = 'default';

            // let's disable the inputs for the duration of the ajax request
            jQuery(inputs).prop("disabled", true);
            // fire off the request
            request = jQuery.ajax({
                url: page,
                type: "post",
                data: { type: type, form_info: serializedData, matched_attributes: serializedMatchData }
            });
            // callback handler that will be called on success
            request.done(function (html, response, textStatus, jqXHR) {
                // output shortcode information
                jQuery('#agsg_shortcode_preview').html(html);
            });
            // callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // log the error to the console
                console.error(
                    "The following error occured: " +
                        textStatus, errorThrown
                );
            });
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                jQuery(inputs).prop("disabled", false);
            });
        }
    });
    /**
     * End eagsg page form
     */
    /**
     * End Shortcode Generator Forms Post Code
     */
});
/**
 * Build a list of shortcode attribute options in HTML
 * @returns {string}
 */
function getShortcodeAttributeOptionsList(reset) {
    if (reset === 'reset') {
        var html = '<option value="select" selected="selected">Reset - Select Attribute</option>';
    } else {
        var html = '<option value="select" selected="selected">Select Attribute</option>';
    }
    // build a list of options from the attributes
    jQuery('[name="agsg_att_name[]"]').each(function (e) {
        var oVal = jQuery(this).val();
        html += '<option value="' + oVal + '">' + oVal + '</option>';
    });
    return html;
}
/**
 * Loads TinyMCE's
 * @param selector - the current id of new textarea
 * @param existing_ids - textareas that have already been initialized
 */
function loadTinyMCE(selector, existing_ids) {
    if (typeof tinymce == 'undefined') {
        jQuery.getScript('//tinymce.cachefly.net/4/tinymce.min.js', function () {
            window.tinymce.dom.Event.domLoaded = true;
            tinymce.init({
                selector: "textarea" + selector,
                plugins: ["advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste emoticons paste textcolor colorpicker textpattern"],
                toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                toolbar2: "preview media | forecolor backcolor emoticons insertfile",
                menubar: "tools table format view insert edit",
                schema: "html5",
                entity_encoding: "raw",
                extended_valid_elements: "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],$elements",
                valid_children: "+body[style]"
            });
        });
    }
    else {
        // shutdown all current instances
        for (var i = 0; i < existing_ids.length; i++) {
            var id = '#' + existing_ids[i];
            tinymce.execCommand('mceRemoveControl', true, id);
        }
        // initlaize new tini MCE
        window.tinymce.dom.Event.domLoaded = true;
        tinymce.init({
            selector: "textarea" + selector,
            plugins: ["advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste emoticons paste textcolor colorpicker textpattern"],
            toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            toolbar2: "preview media | forecolor backcolor emoticons insertfile",
            menubar: "tools table format view insert edit",
            schema: "html5",
            entity_encoding: "raw",
            extended_valid_elements: "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],$elements",
            valid_children: "+body[style]"
        });
        // restart all current instances
        for (var i = 0; i < existing_ids.length; i++) {
            var id = '#' + existing_ids[i];
            tinymce.execCommand('mceAddControl', true, id);
        }
    }
}