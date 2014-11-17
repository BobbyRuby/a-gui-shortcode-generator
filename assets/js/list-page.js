/**
 * Created by Bobby on 11/15/14.
 */
jQuery(document).ready(function ($) {
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
    });
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
    });
    var check_labels = [];
    var t_ck_ids = [];
    jQuery('.check-box-container').children('label').each(function (j) {
        check_labels.push(jQuery(this).text());
        t_ck_ids.push(jQuery(this).next().attr('id'));
    });
    jQuery('.check-box-container-bottom').children('label').each(function (i) {
        jQuery(this).click(function (e) {
            var label = jQuery(this).text();
            // compare this label to the top
            for (var j = 0; j < check_labels.length; j++) {
                var t_label = check_labels[j];
                var t_ch_id = t_ck_ids[j];
                // are the labels the same
                if (label === t_label) {
                    // is the input checked
                    if (jQuery(t_ch_id).is(':checked')) {
                        // uncheck
                        jQuery('#' + t_ch_id).prop('checked', false);
                    } else {
                        // check it
                        jQuery('#' + t_ch_id).prop('checked', true);
                    }
                }
            }
        });
    });

});