/**
 * Created by Bobby on 11/15/14.
 */
jQuery(document).ready(function ($) {
    //   Screen Options Hack Markup***
    jQuery('#adv-settings').prepend('<div class="metabox-prefs">' +
        '<label for="type-hide"><input type="checkbox" checked="checked" value="type" id="type-hide" name="type-hide" class="hide-column-tog">Type</label>' +
        '<label for="name-hide"><input type="checkbox" checked="checked" value="name" id="name-hide" name="name-hide" class="hide-column-tog">Name</label>' +
        '<label for="tag-hide"><input type="checkbox" checked="checked" value="tag" id="tag-hide" name="tag-hide" class="hide-column-tog">Tag</label>' +
        '<label for="kind-hide"><input type="checkbox" checked="checked" value="kind" id="kind-hide" name="kind-hide" class="hide-column-tog">Kind</label>' +
        '<label for="example-hide"><input type="checkbox" checked="checked" value="example" id="example-hide" name="example-hide" class="hide-column-tog">Example</label>' +
        '<label for="code-hide"><input type="checkbox" checked="checked" value="code" id="code-hide" name="code-hide" class="hide-column-tog">Code</label>' +
        '<label for="created_datetime-hide"><input type="checkbox" checked="checked" value="created_datetime" id="created_datetime-hide" name="created_datetime-hide" class="hide-column-tog">Created Datetime</label>' +
        '<br class="clear">' +
        '</div>'
    );

    //   Screen Options Control ***
    // grab and cycle through each checkbox
    jQuery('.metabox-prefs label').children('input').change(function () {
            console.log(jQuery(this));
            var selector_text = jQuery(this).val();
            console.log(selector_text);
            var checked = jQuery(this).attr('checked');
            console.log('checked');
            if (checked !== 'checked') {
                jQuery(this).prop("checked", false); // set to not checked
                jQuery("." + selector_text).hide(); // hide (tds)
                jQuery(".column-" + selector_text).hide(); // hide (ths)
            } else {
                jQuery(this).prop("checked", true); // set to checked
                jQuery("." + selector_text).show(); // (tds)
                jQuery(".column-" + selector_text).show(); // (ths)
            }
        }
    );
    jQuery('[name="action"]').change(function (e) {
        val = jQuery(this).val();
        if (val === 'delete_selected') {
            jQuery(this).parent().addClass('form-invalid');
            jQuery(this).parent().find('.error').remove();
            jQuery(this).parent().prepend('<span class="error">Are you SURE you want to delete these shortcodes?  This cannot be undone. You should make sure your not using the shortcodes selected ANYWHERE or your site will break where they are used.</span>');
        } else {
            jQuery(this).parent().removeClass('form-invalid');
            jQuery(this).parent().find('.error').remove();
        }
    })
    jQuery('[name="action2"]').change(function (e) {
        val = jQuery(this).val();
        if (val === 'delete_selected') {
            jQuery(this).parent().addClass('form-invalid');
            jQuery(this).parent().find('.error').remove();
            jQuery(this).parent().prepend('<span class="error">Are you SURE you want to delete these shortcodes?  This cannot be undone. You should make sure your not using the shortcodes selected ANYWHERE or your site will break where they are used.</span>');
        } else {
            jQuery(this).parent().removeClass('form-invalid');
            jQuery(this).parent().find('.error').remove();
        }
    })
});
