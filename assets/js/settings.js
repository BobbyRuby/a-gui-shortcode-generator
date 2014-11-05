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

    /***** Custom settings for pages - Concrete Javscript *****/

    var htmlTagATTList = []; // holds our html attributes
    var shortcodeTagATTList = []; // holds our shortcode attributes
    var serializedMatchData;
    /**
     * These two arrays will be aligned perfectly with each index matching one another on the post.
     */


        // prevent attribute_matching_form submit  @todo - may not need
    jQuery("#attribute_matching_form").submit(function (event) {
        // setup some local variables
        var form = jQuery(this);
        // let's select and cache all the fields
        var inputs = jQuery(form).find("input, select, button, textarea");
        // serialize the data in the form
        serializedMatchData = jQuery(form).serialize();
        // prevent default posting of form
        event.preventDefault();
    });
    // listen for the link to load our attributes for mapping
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
                selects += '<select id="match_html_tag_att_name_' + i + '" name="match_html_tag_att_name_' + i + '[]">' + html_att_options + '</select> = ';
                selects += '<select id="match_att_name_' + i + '" name="match_att_name_' + i + '[]">' + att_options + '</select>';
            }
            html += selects;
            html += '<br/><p style="text-align: center;"><input name="submit_attribute_matching_form" type="submit" class="button-primary" value="Set Mapping" /></p>';
        } else {
            html = error;
        }
        jQuery(form).html(html);
    });

    // listen for radio options of the has_atts question
    jQuery('[name="agsg_has_html_tag_atts"]').change(function (e) {
        var val = jQuery(this).val();
        if (val == 'Yes') {
            htmlTagATTList.push('html_tag_att_name');
            var html = '<tr class="html_tag_template_name"><th scope="row">HTML TAG Attribute 1 Name</th><td><input type="text" value="" placeholder="" name="agsg_html_tag_att_name[]" id="html_tag_att_name">' +
                '<label for="html_tag_att_name"><span class="description">This is the name for the HTML TAG attribute.<span style="display: none;" class="dashicons dashicons-dismiss" title="Remove this attribute and the default vaule associated with it."></span></span></label>' +
                '</td></tr>';
            html += '<tr class="html_tag_template_value"><th scope="row">HTML TAG Attribute 1 Set Value</th><td><input type="text" value="" placeholder="" name="agsg_html_tag_default[]" id="html_tag_default">' +
                '<label for="html_tag_default"><span class="description">The SET value for the attribute.  If you want to match the HTML TAG attribute name with a shortcode attribute value, leave this blank.  If you intend on this HTML TAG attribute value staying the same each time this shortcode is used, then fill this in.</span></label>' +
                '</td></tr>';
            jQuery(this).parent().parent().parent().after(html);
            jQuery('[for="html_tag_att_name"] .dashicons-dismiss').click(function (e) {
//                if(jQuery('#has_atts_Yes').is(':checked')){
                if (window.confirm("You will lose this HTML TAG attribute. Continue?")) {
                    jQuery(this).parent().parent().parent().parent().next().remove();
                    jQuery(this).parent().parent().parent().parent().remove();
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
    jQuery('#add_html_tag_att').click(function (e) {
        // count the names ( defaults will be the same)
        var count = jQuery('[name="agsg_html_tag_att_name[]"]').length;
        console.log(count);
        count++;
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
                if (window.confirm("You will lose this attribute. Continue?")) {
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
    jQuery('#add_shortcode_att').click(function (e) {
        // count the names ( defaults will be the same)
        var count = jQuery('[name="agsg_att_name[]"]').length;
        console.log(count);
        count++;
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
     * Shortcode Generator Forms
     */
    var dir = jQuery('[name="agsg_install_url"]').val(); // dir url of install from hidden meta
    var page = dir + 'class-agsgPlugin.php';
    /**
     * ncagsg page form
     */
    var request = '';
    jQuery("#agsg_ncagsg_form").submit(function (event) {
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
            var kind = '';
            // set kind of shortcode
            if (has_atts == 'Yes') {
                kind = 'ATT';
            }
            else {
                kind = 'NonATT';
            }

            // set type of shortcode
            var type = 'enclosed';
            // let's disable the inputs for the duration of the ajax request
            jQuery(inputs).prop("disabled", true);
            // fire off the request
            request = jQuery.ajax({
                url: page,
                type: "post",
                data: { kind: kind, type: type, form_info: serializedData, matched_attributes: serializedMatchData }
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
     * ATT kinds
     */
    var kind = 'NonATT';
});