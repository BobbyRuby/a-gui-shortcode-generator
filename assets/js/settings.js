/**
 * Created by Bobby on 11/2/14.
 */

// IIFE - Immediately Invoked Function Expression
(function (yourcode) {

    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function ($, window, document) {
    // The $ is now locally scoped
    // Listen for the jQuery ready event on the document
    $(function () {
        console.log('The DOM is ready');
        // The DOM is ready!
        var dir = jQuery('[name="agsg_install_url"]').val(); // dir url of install from hidden meta
        var page = dir + 'a-gui-shortcode-generator.php';
        var htmlTagATTList = []; // holds our html attributes
        var shortcodeTagATTList = []; // holds our shortcode attributes
        var conditionList = []; // holds our shortcode conditions
        var scriptsList = []; // holds our shortcode scripts
        var stylesList = []; // holds our shortcode styles
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
                var html = '<tr class="type_template"><th scope="row">Select Condition Type</th><td><select id="shortcode_condition_type" name="agsg_shortcode_condition_type[]"><option value="select">Select Condition Type</option><option value="if">IF</option><option value="if-else">IF-Else</option></select> ' +
                    '<label for="agsg_shortcode_condition_type[]"><span class="description"><br/>' +
                    '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Use "IF" to do something when an attribute(s) evaluation is "TRUE".<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this condition and the data associated with it."></span><br/>' +
                    '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Important Note:</span> TinyMCE will not load TWICE for a condition. What this means is once you remove a condition, if you add it again, you will no longer have the editor so you must create conditions until you have passed the id number of the condition you deleted, then delete the conditions without TinyMCE unless you want to use HTML markup.<br/>' +
                    '</span><br/>' +
                    '</td></tr>';
                jQuery(this).parent().parent().parent().after(html);
                jQuery('[for="agsg_shortcode_condition_type[]"] .dashicons-dismiss').click(function (e) {
                    if (window.confirm("You will lose this condition and its' data. Continue?")) {
                        var condition_type = jQuery(this).parent().parent().prev().find('option:selected').val();
                        // check what condition was just set so the proper amount can be removed.
                        if (condition_type == 'if') {
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                        }
                        else if (condition_type == 'if-else') {
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                            jQuery(this).parent().parent().parent().parent().next().remove();
                        }
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
                    var id = jQuery(this).attr('id');
                    if (id == 'shortcode_condition_type') {
                        var condition_id_num = id.replace('shortcode_condition_type', '');
                        condition_id_num = 1;
                    } else {
                        var condition_id_num = id.replace('shortcode_condition_type_', '');
                    }
                    var condition_id = 'shortcode_condition_' + condition_id_num;
                    if (val == 'select') {
                        jQuery(this).parent().parent().parent().find('.for_condition_number_' + condition_id_num).remove();
                    }
                    else if (val == 'if') {
                        jQuery(this).attr("disabled", "disabled");
                        // to evaluate 1
                        html += '<tr class="' + condition_id + '"><th scope="row">Evaluate 1</th><td><select id="' + condition_id + '_eval_1" class="shortcode_condition_attribute" name="agsg_shortcode_condition_eval_1[]">';
                        html += getShortcodeAttributeOptionsList();
                        html += getGlobalVarOptionsList();
                        html += '</select><label for="' + condition_id + '_eval_1"><span class="description">This is the first shortcode attribute or variable you want to check the value of, it is required.</span></label>' +
                            '<div id="reset_for_' + condition_id + '_eval_1" class="reset_condition_container"><span class="reset_condition">Reset these options</span><span class="dashicons dashicons-randomize"></span></div></td></tr>';
                        // to evaluate 2
                        html += '<tr class="' + condition_id + '"><th scope="row">Evaluate 2</th><td><select id="' + condition_id + '_eval_2" class="shortcode_condition_attribute" name="agsg_shortcode_condition_eval_2[]">';
                        html += getShortcodeAttributeOptionsList();
                        html += getGlobalVarOptionsList();
                        html += '</select><label for="' + condition_id + '_eval_2"><span class="description">This is the second shortcode attribute or variable you want to check the value of, it is optional.</span></label>' +
                            '<div id="reset_for_' + condition_id + '_eval_2" class="reset_condition_container"><span class="reset_condition">Reset these options</span><span class="dashicons dashicons-randomize"></span></div></td></tr>';

                        // operator type 1 == / <= / >= / < / >
                        html += '<tr class="' + condition_id + '"><th scope="row">Operator 1</th><td><select id="' + condition_id + '_operator_1" class="shortcode_condition_operator" name="agsg_shortcode_condition_operator_1[]">';
                        html += '<option value="==">equal to value</option>' +
                            '<option value="!=">not equal to value</option>' +
                            '<option value="<=">less than or equal to value</option>' +
                            '<option value="<">less than value</option>' +
                            '<option value=">=">greater than or equal to value</option>' +
                            '<option value=">">greater than value</option>' +
                            '</select><label for="' + condition_id + '_operator_1"><span class="description">This is the first operator to use in evaluating the shortcode attribute or variable you want to check against value 1, it is required.</span></label>';

                        // operator type 2 == / <= / >= / < / >
                        html += '<tr class="' + condition_id + '"><th scope="row">Operator 2</th><td><select id="' + condition_id + '_operator_2" class="shortcode_condition_operator" name="agsg_shortcode_condition_operator_2[]">';
                        html += '<option value="==">equal to value</option>' +
                            '<option value="!=">not equal to value</option>' +
                            '<option value="<=">less than or equal to value</option>' +
                            '<option value="<">less than value</option>' +
                            '<option value=">=">greater than or equal to value</option>' +
                            '<option value=">">greater than value</option>' +
                            '</select><label for="' + condition_id + '_operator_2"><span class="description">This is the first operator to use in evaluating the shortcode attribute or variable you want to check against value 2, it is optional</span><br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span>This field will be ignored if "Evaluate 2" has no selection.</span></label>';

                        // value 1 to check against
                        html += '<tr class="' + condition_id + '"><th scope="row">Value 1</th><td><input id="' + condition_id + '_value_1" class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_value_1[]">' +
                            '<label for="' + condition_id + '_value_1"><span class="description">This is the value to check the shortcode attribute or variable against using the operator 1 above, it is required only when "Evaluate 2" field has been selected.<br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span>If you leave this blank it will be considered an empty string.</span></label>' +
                            '</td></tr>';

                        // value 2 to check against
                        html += '<tr class="' + condition_id + '"><th scope="row">Value 2</th><td><input id="' + condition_id + '_value_2" class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_value_2[]">' +
                            '<label for="' + condition_id + '_value_2"><span class="description">This is the value to check the shortcode attribute or variable against using the operator 2 above.<br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Important Note:</span> This field will be ignored if "Evaluate 2" has no selection.</span><br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> If you leave this blank and you have selected something for the "Evaluate 2" field, it will be considered an empty string.</span></label>' +
                            '</td></tr>';

                        // Relation between expressions above
                        html += '<tr class="' + condition_id + '"><th scope="row">Relation between expressions 1 and 2.</th><td><input id="' + condition_id + '_value_2" class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_value_2[]">' +
                            '<label for="' + condition_id + '_value_2"><span class="description">This is where you tell the generator how it needs to treat the expressions created by the fields above to deal with them inside the conditional created.</span><br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> This field will be ignored if "Evaluate 2" has no selection.</span><br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> Expression 1 is made up of the "Evaluate 1", "Operator 1", and "Value 1"</span><br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> Expression 2 is made up of the "Evaluate 2", "Operator 2", and "Value 2"</span></label>' +
                            '</td></tr>';

                        // action to do if true
                        html += '<tr class="' + condition_id + '"><th scope="row">' +
                            'If the evaluation of the attribute selected and the value entered using the operator selected results to true, display the content entered here.</th>' +
                            '<td><textarea name="agsg_shortcode_condition_tinyMCE_true[]" cols="50" rows="5" id="' + condition_id + '_tinyMCE_true">' +
                            'Enter content you want displayed here.  Remember that you can reference any attribute named above using the shortcode attribute reference syntax.<br>' +
                            'They can be inserted anywhere you can place text in the TinyMCE editor.  Play around with it and don\'t hestistate to report a bug if you can find one. DO NOT USE DOUBLE QUOTES FOR ANY HTML attribute values.' +
                            '</textarea><br><label for="' + condition_id + '_tinyMCE_true"><span class="' + condition_id + '_tinyMCE">This is where you add some conditional content.<br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It appears below the normally rendered content if this is an enclosing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It is displayed if this is a self closing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You must NOT use single quotations in any HTML attribute values.</span></label></span></label></td></tr>';
                        // push this id into the array
                        tinyMCEids.push('#' + condition_id + '_tinyMCE_true');
                        loadTinyMCE('#' + condition_id + '_tinyMCE_true', tinyMCEids);
                    } else if (val == 'if-else') {
                        jQuery(this).attr("disabled", "disabled");
                        condition_id += '_if_else';
                        // attribute to evaluate
                        html += '<tr class="' + condition_id + '"><th scope="row">Evaluate Attribute</th><td><select id="' + condition_id + '_eval_1" class="shortcode_condition_attribute" name="agsg_shortcode_condition_eval_1[]">';
                        html += getShortcodeAttributeOptionsList();
                        html += '</select><label for="' + condition_id + '_eval_1"><span class="description">This is the shortcode attribute you want to check the value of.</span></label>' +
                            '<div id="reset_for_attribute_condition_number_' + condition_id_num + '" class="reset_condition_container"><span class="reset_condition">Reset these options</span><span class="dashicons dashicons-randomize"></span></div></td></tr>';

                        // operator type == / <= / >= / < / >
                        html += '<tr class="' + condition_id + '"><th scope="row">Operator</th><td><select id="' + condition_id + '_operator" class="shortcode_condition_operator" name="agsg_shortcode_condition_operator[]">';
                        html += '<option value="==">equal to value</option>' +
                            '<option value="!=">not equal to value</option>' +
                            '<option value="<=">less than or equal to value</option>' +
                            '<option value="<">less than value</option>' +
                            '<option value=">=">greater than or equal to value</option>' +
                            '<option value=">">greater than value</option>' +
                            '</select><label for="' + condition_id + '_operator"><span class="description">This is the operator to use in evaluating the shortcode attribute you want to check against the value you input in the text field below.</span></label>';
                        // value to check against
                        html += '<tr class="' + condition_id + '"><th scope="row">Value</th><td><input id="' + condition_id + '_value" class="shortcode_condition_value" type="text" value="" placeholder="" name="agsg_shortcode_condition_value[]">' +
                            '<label for="' + condition_id + '_value"><span class="description">This is the value the value to check the shortcode attribute against using the operator above.<br/>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span>If you leave this blank it will be considered an empty string.</span></label>' +
                            '</td></tr>';
                        // content to do if true
                        html += '<tr class="' + condition_id + '"><th scope="row">' +
                            'If the evaluation results to TRUE, display the content entered here.</th>' +
                            '<td><textarea name="agsg_shortcode_condition_tinyMCE_true[]" cols="50" rows="5" id="' + condition_id + '_tinyMCE_true">' +
                            'Enter content you want displayed here.  Remember that you can reference any attribute named above using the shortcode attribute reference syntax.<br>' +
                            'They can be inserted anywhere you can place text in the TinyMCE editor.  Play around with it and don\'t hestistate to report a bug if you can find one. DO NOT USE DOUBLE QUOTES FOR ANY HTML attribute values.' +
                            '</textarea><br><label for="' + condition_id + '_tinyMCE_true"><span class="' + condition_id + '_tinyMCE">This is where you add some conditional content.<br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It appears below the normally rendered content if this is an enclosing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It is displayed if this is a self closing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You must NOT use single quotations in any HTML attribute values.</span></label></span>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">This content is displayed if the evaluation is TRUE.</span></label></span></label></td></tr>';
                        // push this id into the array
                        tinyMCEids.push(condition_id + '_tinyMCE_true');

                        // content to do if false
                        html += '<tr class="' + condition_id + '"><th scope="row">' +
                            'If the evaluation results to FALSE, display the content entered here.</th>' +
                            '<td><textarea name="agsg_shortcode_condition_tinyMCE_else[]" cols="50" rows="5" id="' + condition_id + '_tinyMCE_else">' +
                            'Enter content you want displayed here.  Remember that you can reference any attribute named above using the shortcode attribute reference syntax.<br>' +
                            'They can be inserted anywhere you can place text in the TinyMCE editor.  Play around with it and don\'t hestistate to report a bug if you can find one. DO NOT USE DOUBLE QUOTES FOR ANY HTML attribute values.' +
                            '</textarea><br><label for="' + condition_id + '_tinyMCE_false"><span class="' + condition_id + '_tinyMCE">This is where you add some conditional content.<br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It appears below the normally rendered content if this is an enclosing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> It is displayed if this is a self closing shortcode.</span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You must NOT use single quotations in any HTML attribute values.</span></label></span><br>' +
                            '<span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">This content is displayed if the evaluation is FALSE.</span></label></span></label></td></tr>';
                        // push this id into the array
                        tinyMCEids.push(condition_id + '_tinyMCE_else');

                        loadTinyMCE('#' + condition_id + '_tinyMCE_true', tinyMCEids);
                        setTimeout(1000,
                            loadTinyMCE('#' + condition_id + '_tinyMCE_else', tinyMCEids));
                    }
                    jQuery(this).parent().parent().after(html);

                    // set reset button 1
                    jQuery('#reset_for_' + condition_id + '_eval_1').click(function (e) {
                        jQuery('#' + condition_id + '_eval_1').html(
                            getShortcodeAttributeOptionsList('reset') + getGlobalVarOptionsList());
                    });
                    // set reset button 2
                    jQuery('#reset_for_' + condition_id + '_eval_2').click(function (e) {
                        jQuery('#' + condition_id + '_eval_2').html(
                            getShortcodeAttributeOptionsList('reset') + getGlobalVarOptionsList());
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
         * Begin html tag atts section
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
         * End html tag atts section
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
                    '<label for="att_name"><span class="description">This is the name for the attribute <span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this attribute and the default value associated with it."></span></span></label>' +
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
         * Begin scripts section
         */
            // listen for radio options of the has_scripts question
        jQuery('[name="agsg_has_scripts"]').change(function (e) {
            var val = jQuery(this).val();
            if (val == 'Yes') {
                scriptsList.push('script_handle');

                var html = '<tr class="template_script_handle"><th scope="row">Script 1 Handle</th><td><input type="text" value="" placeholder="" name="agsg_script_handle[]" id="script_handle">' +
                    '<label for="script_handle"><span class="description">This is the name for the script.<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this script and the data associated with it."></span></span></label>' +
                    '</td></tr>';

                html += '<tr class="template_script_src"><th scope="row">Script 1 Source</th><td><input type="text" value="" placeholder="" name="agsg_script_src[]" id="script_src">' +
                    '<label for="script_src"><span class="description">The URL on the server where the file resides.</span></label>' +
                    '</td></tr>';

                html += '<tr class="template_script_deps"><th scope="row">Script 1 Dependents</th><td><input type="text" value="" placeholder="" name="agsg_script_deps[]" id="script_deps">' +
                    '<label for="script_deps"><span class="description">The handles for the scripts this script depends on comma seperated.</span></label>' +
                    '</td></tr>';

                html += '<tr class="template_script_ver"><th scope="row">Script 1 Version</th><td><input type="text" value="" placeholder="" name="agsg_script_ver[]" id="script_ver">' +
                    '<label for="script_ver"><span class="description">The version of this script.</span></label>' +
                    '</td></tr>';

                jQuery(this).parent().parent().parent().after(html);
                // listen for shortcode scripts to keep input valid
                jQuery('[name="agsg_script_handle[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '-', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode scripts to keep input valid
                jQuery('[name="agsg_script_src[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '/', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode scripts to keep input valid
                jQuery('[name="agsg_script_deps[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', ',', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode scripts to keep input valid
                jQuery('[name="agsg_script_ver[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '.', 'gi');
                    jQuery(this).val(val);
                });

                jQuery('[for="script_handle"] .dashicons-dismiss').click(function (e) {
                    if (window.confirm("You will lose this script. Continue?")) {
                        jQuery(this).parent().parent().parent().parent().next().next().next().remove();
                        jQuery(this).parent().parent().parent().parent().next().next().remove();
                        jQuery(this).parent().parent().parent().parent().next().remove();
                        jQuery(this).parent().parent().parent().parent().remove();
                        // cycle through list of shortcode ids
                        for (var i = 0; i < scriptsList.length; i++) {
                            // check current id is the same as the -this-
                            if (scriptsList[i] === jQuery(this).parent().parent().prev().attr('id')) {
                                scriptsList[i] = 0; // if so set this index to 0 so it won't be recognized in matching area
                            }
                        }
                    }
                });
                jQuery('#add_shortcode_script').show();
            } else {
                if (window.confirm("You will lose all scripts. Continue?")) {
                    jQuery('[name="agsg_script_handle[]"]').parent().parent().remove();
                    jQuery('[name="agsg_script_src[]"]').parent().parent().remove();
                    jQuery('[name="agsg_script_deps[]"]').parent().parent().remove();
                    jQuery('[name="agsg_script_ver[]"]').parent().parent().remove();
                    jQuery('#add_shortcode_script').hide();
                }
                else {
                    jQuery('#has_scripts_Yes').attr('checked', 'checked');
                }
            }
        });
        // listen for add script button
        var script_name_count = jQuery('[name="agsg_script_handle[]"]').length;
        jQuery('#add_shortcode_script').click(function (e) {
            // count the names ( defaults will be the same)
            script_name_count++;
            var count = script_name_count + 1;

            var handle_clone = jQuery('.template_script_handle').clone(true, true).removeClass('template_script_handle').removeClass('form-invalid');
            jQuery("th", handle_clone).text('Script ' + count + ' Handle');
            jQuery("input", handle_clone).attr('id', jQuery("input", handle_clone).attr('id') + '_' + count);
            jQuery("label", handle_clone).attr('for', jQuery("label", handle_clone).attr('for') + '_' + count);
            jQuery(".error", handle_clone).remove();

            var src_clone = jQuery('.template_script_src').clone(true, true).removeClass('template_script_src').removeClass('form-invalid');
            jQuery("th", src_clone).text('Script ' + count + ' Source');
            jQuery("input", src_clone).attr('id', jQuery("input", src_clone).attr('id') + '_' + count);
            jQuery("label", src_clone).attr('for', jQuery("label", src_clone).attr('for') + '_' + count);
            jQuery(".error", src_clone).remove();

            var deps_clone = jQuery('.template_script_deps').clone(true, true).removeClass('template_script_deps').removeClass('form-invalid');
            jQuery("th", deps_clone).text('Script ' + count + ' Dependents');
            jQuery("input", deps_clone).attr('id', jQuery("input", deps_clone).attr('id') + '_' + count);
            jQuery("label", deps_clone).attr('for', jQuery("label", deps_clone).attr('for') + '_' + count);
            jQuery(".error", deps_clone).remove();

            var ver_clone = jQuery('.template_script_ver').clone(true, true).removeClass('template_script_ver').removeClass('form-invalid');
            jQuery("th", ver_clone).text('Script ' + count + ' Version');
            jQuery("input", ver_clone).attr('id', jQuery("input", ver_clone).attr('id') + '_' + count);
            jQuery("label", ver_clone).attr('for', jQuery("label", ver_clone).attr('for') + '_' + count);
            jQuery(".error", ver_clone).remove();

            scriptsList.push('script_handle_' + count);

            jQuery(".dashicons-dismiss", handle_clone).show();

            count--;
            if (!count == 1) {
                jQuery('#script_ver' + '_' + count).parent().parent().after(ver_clone).after(deps_clone).after(src_clone).after(handle_clone);
            } else {
                jQuery('#script_ver').parent().parent().after(ver_clone).after(deps_clone).after(src_clone).after(handle_clone);
            }
        });
        /**
         * End scripts section
         */
        /**
         * Begin styles section
         */
            // listen for radio options of the has_styles question
        jQuery('[name="agsg_has_styles"]').change(function (e) {
            var val = jQuery(this).val();
            if (val == 'Yes') {
                stylesList.push('style_handle');

                var html = '<tr class="template_style_handle"><th scope="row">Style 1 Handle</th><td><input type="text" value="" placeholder="" name="agsg_style_handle[]" id="style_handle">' +
                    '<label for="style_handle"><span class="destyleion">This is the name for the style.<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this style and the data associated with it."></span></span></label>' +
                    '</td></tr>';

                html += '<tr class="template_style_src"><th scope="row">Style 1 Source</th><td><input type="text" value="" placeholder="" name="agsg_style_src[]" id="style_src">' +
                    '<label for="style_src"><span class="destyleion">The URL on the server where the file resides.</span></label>' +
                    '</td></tr>';

                html += '<tr class="template_style_deps"><th scope="row">Style 1 Dependents</th><td><input type="text" value="" placeholder="" name="agsg_style_deps[]" id="style_deps">' +
                    '<label for="style_deps"><span class="destyleion">The handles for the styles this style depends on comma seperated.</span></label>' +
                    '</td></tr>';

                html += '<tr class="template_style_ver"><th scope="row">Style 1 Version</th><td><input type="text" value="" placeholder="" name="agsg_style_ver[]" id="style_ver">' +
                    '<label for="style_ver"><span class="destyleion">The version of this style.</span></label>' +
                    '</td></tr>';

                html += '<tr class="template_style_media"><th scope="row">Style 1 Media</th><td><input type="text" value="" placeholder="" name="agsg_style_media[]" id="style_media">' +
                    '<label for="style_media"><span class="destyleion">The media this style should apply to.</span></label>' +
                    '</td></tr>';

                jQuery(this).parent().parent().parent().after(html);
                // listen for shortcode styles to keep input valid
                jQuery('[name="agsg_style_handle[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '-', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode styles to keep input valid
                jQuery('[name="agsg_style_src[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '/', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode styles to keep input valid
                jQuery('[name="agsg_style_deps[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', ',', 'gi');
                    jQuery(this).val(val);
                });
                // listen for shortcode styles to keep input valid
                jQuery('[name="agsg_style_ver[]"]').change(function (e) {
                    var val = jQuery(this).val();
                    val = val.trim().replace(' ', '.', 'gi');
                    jQuery(this).val(val);
                });
                jQuery('[for="style_handle"] .dashicons-dismiss').click(function (e) {
                    if (window.confirm("You will lose this style. Continue?")) {
                        jQuery(this).parent().parent().parent().parent().next().next().next().next().remove();
                        jQuery(this).parent().parent().parent().parent().next().next().next().remove();
                        jQuery(this).parent().parent().parent().parent().next().next().remove();
                        jQuery(this).parent().parent().parent().parent().next().remove();
                        jQuery(this).parent().parent().parent().parent().remove();
                        // cycle through list of shortcode ids
                        for (var i = 0; i < stylesList.length; i++) {
                            // check current id is the same as the -this-
                            if (stylesList[i] === jQuery(this).parent().parent().prev().attr('id')) {
                                stylesList[i] = 0; // if so set this index to 0 so it won't be recognized in matching area
                            }
                        }
                    }
                });
                jQuery('#add_shortcode_style').show();
            } else {
                if (window.confirm("You will lose all styles. Continue?")) {
                    jQuery('[name="agsg_style_handle[]"]').parent().parent().remove();
                    jQuery('[name="agsg_style_src[]"]').parent().parent().remove();
                    jQuery('[name="agsg_style_deps[]"]').parent().parent().remove();
                    jQuery('[name="agsg_style_ver[]"]').parent().parent().remove();
                    jQuery('[name="agsg_style_media[]"]').parent().parent().remove();
                    jQuery('#add_shortcode_style').hide();
                }
                else {
                    jQuery('#has_styles_Yes').attr('checked', 'checked');
                }
            }
        });
        // listen for add style button
        var style_name_count = jQuery('[name="agsg_style_handle[]"]').length;
        jQuery('#add_shortcode_style').click(function (e) {
            // count the names ( defaults will be the same)
            style_name_count++;
            var count = style_name_count + 1;

            var handle_clone = jQuery('.template_style_handle').clone(true, true).removeClass('template_style_handle').removeClass('form-invalid');
            jQuery("th", handle_clone).text('Style ' + count + ' Handle');
            jQuery("input", handle_clone).attr('id', jQuery("input", handle_clone).attr('id') + '_' + count);
            jQuery("label", handle_clone).attr('for', jQuery("label", handle_clone).attr('for') + '_' + count);
            jQuery(".error", handle_clone).remove();

            var src_clone = jQuery('.template_style_src').clone(true, true).removeClass('template_style_src').removeClass('form-invalid');
            jQuery("th", src_clone).text('Style ' + count + ' Source');
            jQuery("input", src_clone).attr('id', jQuery("input", src_clone).attr('id') + '_' + count);
            jQuery("label", src_clone).attr('for', jQuery("label", src_clone).attr('for') + '_' + count);
            jQuery(".error", src_clone).remove();

            var deps_clone = jQuery('.template_style_deps').clone(true, true).removeClass('template_style_deps').removeClass('form-invalid');
            jQuery("th", deps_clone).text('Style ' + count + ' Dependents');
            jQuery("input", deps_clone).attr('id', jQuery("input", deps_clone).attr('id') + '_' + count);
            jQuery("label", deps_clone).attr('for', jQuery("label", deps_clone).attr('for') + '_' + count);
            jQuery(".error", deps_clone).remove();

            var ver_clone = jQuery('.template_style_ver').clone(true, true).removeClass('template_style_ver').removeClass('form-invalid');
            jQuery("th", ver_clone).text('Style ' + count + ' Version');
            jQuery("input", ver_clone).attr('id', jQuery("input", ver_clone).attr('id') + '_' + count);
            jQuery("label", ver_clone).attr('for', jQuery("label", ver_clone).attr('for') + '_' + count);
            jQuery(".error", ver_clone).remove();

            var media_clone = jQuery('.template_style_media').clone(true, true).removeClass('template_style_media').removeClass('form-invalid');
            jQuery("th", media_clone).text('Style ' + count + ' Media');
            jQuery("input", media_clone).attr('id', jQuery("input", media_clone).attr('id') + '_' + count);
            jQuery("label", media_clone).attr('for', jQuery("label", media_clone).attr('for') + '_' + count);
            jQuery(".error", media_clone).remove();

            stylesList.push('style_handle_' + count);

            jQuery(".dashicons-dismiss", handle_clone).show();

            count--;
            if (!count == 1) {
                jQuery('#style_media' + '_' + count).parent().parent().after(media_clone).after(ver_clone).after(deps_clone).after(src_clone).after(handle_clone);
            } else {
                jQuery('#style_media').parent().parent().after(media_clone).after(ver_clone).after(deps_clone).after(src_clone).after(handle_clone);
            }
        });
        /**
         * End styles section
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
                jQuery('[name="agsg_shortcode_condition_eval_1[]"]').each(function (e) {
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
    console.log('The DOM may not be ready');
    // The rest of code goes here!
    /**
     * Build a list of shortcode attribute options in HTML
     * @returns {string}
     */
    function getShortcodeAttributeOptionsList(reset) {
        if (reset === 'reset') {
            var html = '<option value="select" selected="selected">Reset - Select Attribute or Global Variable</option>';
        } else {
            var html = '<option value="select" selected="selected">Select Attribute or Global Variable</option>';
        }
        // build a list of options from the attributes
        jQuery('[name="agsg_att_name[]"]').each(function (e) {
            var oVal = jQuery(this).val();
            html += '<option value="' + oVal + '">' + oVal + '</option>';
        });
        return html;
    }

    /**
     * Build a list of global WordPress variable options in HTML
     * @returns {string}
     */
    function getGlobalVarOptionsList() {
        var html;
        var oVal;
        // build a list of options from the post object
        html += '<optgroup label="Inside The Loop Globals: Post Object">';
        var oVals = [
            'post->ID',
            'post->post_author',
            'post->post_name',
            'post->post_type',
            'post->post_title',
            'post->post_date',
            'post->post_date_gmt',
            'post->post_content',
            'post->post_excerpt',
            'post->post_status',
            'post->comment_status',
            'post->ping_status',
            'post->post_password',
            'post->post_parent',
            'post->post_modified',
            'post->post_modified_gmt',
            'post->comment_count'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

        // build a list of options from the author data object
        html += '<optgroup label="Inside The Loop Globals: Author Data">';
        oVals = [
            'post->authordata->ID',
            'post->authordata->user_login',
            'post->authordata->user_nicename',
            'post->authordata->user_email',
            'post->authordata->user_url',
            'post->authordata->user_registered',
            'post->authordata->user_activation_key',
            'post->authordata->user_status',
            'post->authordata->display_name',
            'post->authordata->firstname',
            'post->authordata->lastname',
            'post->authordata->nickname'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

        // build a list of options from the rest of the inside loop globals
        html += '<optgroup label="Inside The Loop Globals: Misc">';
        oVals = [
            'post->currentday',
            'post->currentmonth',
            'post->page',
            'post->multiplepage',
            'post->more',
            'post->numpages'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

        // build a list of options from the rest of the Browser Detection globals
        html += '<optgroup label="Browser Detection Globals (Boolean Values)">';
        oVals = [
            'is_iphone',
            'is_chrome',
            'is_safari',
            'is_NS4',
            'is_opera',
            'is_macIE',
            'is_winIE',
            'is_gecko',
            'is_lynx',
            'is_IE'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

        // build a list of options from the rest of the Web Server Detectionglobals
        html += '<optgroup label="Web Server Detection Globals (Boolean Values)">';
        oVals = [
            'is_apache',
            'is_IIS',
            'is_iis7'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

        // build a list of options from the rest of the Version Variables globals
        html += '<optgroup label="Version Variables Globals">';
        oVals = [
            'wp_version',
            'wp_db_version',
            'tinymce_version',
            'manifest_version',
            'required_php_version',
            'required_mysql_version'
        ];
        for (var i = 0; i < oVals.length; i++) {
            oVal = oVals[i];
            html += '<option value="' + oVal + '">$' + oVal + '</option>';
        }
        html += '</optgroup>';

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
                    plugins: ["advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu emoticons paste textcolor colorpicker textpattern"],
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
                plugins: ["advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu emoticons paste textcolor colorpicker textpattern"],
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
}));