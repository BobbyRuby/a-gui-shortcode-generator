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
});