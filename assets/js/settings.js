/**
 * Created by Bobby on 11/2/14.
 */
jQuery(document).ready(function ($) {

    /***** Colour picker *****/

    $('.colorpicker').hide();
    $('.colorpicker').each(function () {
        $(this).farbtastic($(this).closest('.color-picker').find('.color'));
    });

    $('.color').click(function () {
        $(this).closest('.color-picker').find('.colorpicker').fadeIn();
    });

    $(document).mousedown(function () {
        $('.colorpicker').each(function () {
            var display = $(this).css('display');
            if (display == 'block')
                $(this).fadeOut();
        });
    });


    /***** Uploading images *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function (button, preview_media) {
        var button_id = button.attr('id');
        var field_id = button_id.replace('_button', '');
        var preview_id = button_id.replace('_button', '_preview');

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery(this).data('uploader_title'),
            button: {
                text: jQuery(this).data('uploader_button_text'),
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            attachment = file_frame.state().get('selection').first().toJSON();
            jQuery("#" + field_id).val(attachment.id);
            if (preview_media) {
                jQuery("#" + preview_id).attr('src', attachment.sizes.thumbnail.url);
            }
        });

        // Finally, open the modal
        file_frame.open();
    }

    jQuery('.image_upload_button').click(function () {
        jQuery.fn.uploadMediaFile(jQuery(this), true);
    });

    jQuery('.image_delete_button').click(function () {
        jQuery(this).closest('td').find('.image_data_field').val('');
        jQuery('.image_preview').remove();
        return false;
    });


    /***** Navigation for settings page *****/

        // Make sure each heading has a unique ID.
    jQuery('ul#settings-sections.subsubsub').find('a').each(function (i) {
        var id_value = jQuery(this).attr('href').replace('#', '');
        jQuery('h3:contains("' + jQuery(this).text() + '")').attr('id', id_value).addClass('section-heading');
    });

    // Create nav links for settings page
    jQuery('#plugin_settings .subsubsub a.tab').click(function (e) {
        // Move the "current" CSS class.
        jQuery(this).parents('.subsubsub').find('.current').removeClass('current');
        jQuery(this).addClass('current');

        // If "All" is clicked, show all.
        if (jQuery(this).hasClass('all')) {
            jQuery('#plugin_settings h3, #plugin_settings form p, #plugin_settings table.form-table, p.submit').show();

            return false;
        }

        // If the link is a tab, show only the specified tab.
        var toShow = jQuery(this).attr('href');

        // Remove the first occurance of # from the selected string (will be added manually below).
        toShow = toShow.replace('#', '', toShow);

        jQuery('#plugin_settings h3, #plugin_settings form > p:not(".submit"), #plugin_settings table').hide();
        jQuery('h3#' + toShow).show().nextUntil('h3.section-heading', 'p, table, table p').show();

        return false;
    });

    /***** Custom settings for pages ****/
    var htmlTagATTList = []; // holds our html attributes
    var shortcodeTagATTList = []; // holds our shortcode attributes
    var conditionList = []; // holds our shortcode conditions
    var serializedMatchData;

        // prevent attribute_matching_form submit
    jQuery("#attribute_matching_form").submit(function (event) {
        // setup some local variables
        var form = jQuery(this);
        // let's select and cache all the fields
        var inputs = jQuery(form).find("input, select, button, textarea");
        // serialize the data in the form
        serializedMatchData = jQuery(form).serialize();
        alert(serializedMatchData);
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
        console.log(htmlTagATTList);
        console.log(htmlTagATTs);
        console.log(shortcodeTagATTList);
        console.log(shortcodeTagATTs);
        console.log(longest);
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
            var html = '<tr class="type_template"><th scope="row">Select an IF Statement Type</th><td><select id="shortcode_condition_type" name="agsg_shortcode_condition_type[]"><option value="select">Select IF statement type</option><option value="if">IF</option><option value="if-else">IF-ELSE</option><option value="if-elseif">IF-ELSEIF</option><option value="if-elseif-else">IF-ELSEIF-ELSE</option></select> ' +
                '<label for="shortcode_condition_type"><span class="description"><br/>' +
                '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF" to do something when an attribute(s) evaluation is "TRUE".<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this condition and the data associated with it."></span><br/>' +
                '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF-ELSE" to do something when an attribute(s) evaluation is "TRUE" but something "else" if it evaluates to "FALSE".<br/>' +
                '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF-ELSEIF" to do something when one attribute(s) evaluation is "TRUE" or to do something when another attribute(s) evaluation is "TRUE"<br/>' +
                '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF-ELSEIF-ELSE" to do something when one attribute(s) evaluation is "TRUE" and if not, to do something when another attribute(s) evaluation is "TRUE", or to do something "else" if both attribute(s) evalutations is "FALSE".</span></label>' +
                '</td></tr>';
            jQuery(this).parent().parent().parent().after(html);
            jQuery('[for="shortcode_condition_type"] .dashicons-dismiss').click(function (e) {
                if (window.confirm("You will lose this condition and its' data. Continue?")) {
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
                if (val == 'select') {
                    jQuery(this).parent().parent().parent().find('.for_condition_number_' + condition_id_num).remove();
                }
                else if (val == 'if') {
                    // attribute to evaluate
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Evaluate Attribute</th><td><select class="shortcode_condition_attribute" name="agsg_shortcode_condition_' + condition_id_num + '_attribute">';
                    html += getShortcodeAttributeOptionsList();
                    html += '</select><label for="shortcode_condition_attribute"><span class="description">This is the shortcode attribute you want to check the value of.</span></label>' +
                        '<div id="reset_for_attribute_condition_number_' + condition_id_num + '" class="reset_condition_container"><span class="reset_condition">Reset these options</span><span class="dashicons dashicons-randomize"></span></div></td></tr>';

                    // operator type == / <= / >= / < / >
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Operator</th><td><select class="shortcode_condition_operator" name="agsg_shortcode_condition_' + condition_id_num + '_operator">';
                    html += '<option value="==">equal to</option>' +
                        '<option value="!=">equal to</option>' +
                        '<option value="<=">less than or equal to</option>' +
                        '<option value="<">less than</option>' +
                        '<option value=">=">greater than or equal to</option>' +
                        '<option value=">">greater than</option>' +
                        '</select><label for="shortcode_condition_operator"><span class="description">This is the operator to use in evaluating the shortcode attribute you want to check against the value you input in the text field below.</span></label>';
                    // value to check against
                    html += '<tr class="for_condition_number_' + condition_id_num + '"><th scope="row">Value</th><td><input class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_' + condition_id_num + '_value">' +
                        '<label for="agsg_shortcode_condition_' + condition_id_num + '_value"><span class="description">This is the value the value to check the shortcode attribute against using the operator above.</span></label>' +
                        '</td></tr>';
                    // action to do if true
                }
                else if (val == 'if-else') {
                    // attribute to evaluate
                    // operator type == / <= / >= / < / >
                    // value to check against
                    // action to do if true
                    // action to do if false
                }
                else if (val == 'if-elseif') {
                    // 1st attribute to evaluate
                    // operator type == / <= / >= / < / >
                    // value to check against
                    // action to do if true

                    // 2nd attribute to evaluate
                    // operator type == / <= / >= / < / >
                    // value to check against
                    // action to do if true
                }
                else if (val == 'if-elseif-else') {
                    // 1st attribute to evaluate
                    // operator type == / <= / >= / < / >
                    // value to check against
                    // action to do if true

                    // 2nd attribute to evaluate
                    // operator type == / <= / >= / < / >
                    // value to check against
                    // action to do if true

                    // action to do if both are false
                }
                jQuery(this).parent().parent().after(html);
                jQuery('#reset_for_attribute_condition_number_' + condition_id_num).click(function (e) {
                    jQuery('[name="agsg_shortcode_condition_' + condition_id_num + '_attribute"]').html(getShortcodeAttributeOptionsList('reset'));
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
                if (window.confirm("You will lose this HTML TAG attribute and must reset attribute mapping.. Continue?")) {
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().remove();
                    serializedMatchData = 0;
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
                serializedMatchData = 0;
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
            jQuery('[for="att_name"] .dashicons-dismiss').click(function (e) {
//                if(jQuery('#has_atts_Yes').is(':checked')){
                if (window.confirm("You will lose this attribute, you must reset attribute mapping, and if you were using this for a condition you need to delete is as well.  Continue?")) {
                    serializedMatchData = 0;
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
//                }
            });
            jQuery('#add_shortcode_att').show();
            if (jQuery('.html_tag_template_name').length) {
                jQuery('#map_attributes').show();
            }
        } else {
            if (window.confirm("You will lose all attributes. Continue?")) {
                serializedMatchData = 0;
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
    /**
     * End field .change modifications to force valid input
     */
    /**
     * Begin Shortcode Generator Forms Post Code
     */
    var dir = jQuery('[name="agsg_install_url"]').val(); // dir url of install from hidden meta
    var page = dir + 'class-agsgPlugin.php';
    /**
     * Begin eagsg page form
     */
    var request = '';
    jQuery("#agsg_eagsg_form").submit(function (event) {
        // abort any pending request
        if (request) {
            request.abort();
        }
        // setup some local variables
        var form = jQuery(this);
        // let's select and cache all the fields
        var inputs = jQuery(form).find("input, select, button, textarea");
        // serialize the data in the form
        var serializedData = jQuery(form).serialize();

        // get values for validation and to see if what type of shortcode this is
        var shortcode_tag_name = jQuery('[name="agsg_shortcode_tag_name"]');
        var html_tag_name = jQuery('[name="agsg_html_tag_name"]');
        var has_atts = jQuery('[name="agsg_has_atts"]');
        var err = false;

        if (serializedMatchData === 0) {
            alert('After attributes have been mapped and you delete one you must re-map them to ensure nothing goes wrong during generating.');
            err = true;
        }

        if (shortcode_tag_name.val() === '') {
            jQuery(shortcode_tag_name).parent().parent().addClass('form-invalid');
            jQuery(shortcode_tag_name).parent().find('.error').remove();
            jQuery(shortcode_tag_name).parent().prepend('<span class="error">Must have a tag.</span>');
            err = true;
        } else {
            jQuery(shortcode_tag_name).parent().removeClass('form-invalid').find('.error').remove();
        }

        if (html_tag_name.val() === '') {
            jQuery(html_tag_name).parent().parent().addClass('form-invalid');
            jQuery(html_tag_name).parent().find('.error').remove();
            jQuery(html_tag_name).parent().prepend('<span class="error">Must have a tag name.</span>');
            err = true;
        } else {
            jQuery(html_tag_name).parent().removeClass('form-invalid').find('.error').remove();
        }

        // no errors
        if (!err) {
            // set type of shortcode
            var type = 'enclosed';
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
                // log a message to the console
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

            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                jQuery(inputs).prop("disabled", false);
            });
        }
        // prevent default posting of form
        event.preventDefault();
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