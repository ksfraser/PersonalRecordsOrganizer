jQuery(document).ready(function($) {
    // Open modal for add/edit
    $(document).on('click', '.epm-add-phone, .epm-edit-phone', function(e) {
        e.preventDefault();
        var $modal = $('#epm-contact-phone-modal');
        var data = $(this).data('phone') || {};
        // Populate form fields
        $modal.find('input[name="id"]').val(data.id || '');
        $modal.find('input[name="contact_id"]').val(data.contact_id || '');
        $modal.find('input[name="phone"]').val(data.phone || '');
        $modal.find('select[name="type_id"]').val(data.type_id || '');
        $modal.find('input[name="is_primary"]').prop('checked', data.is_primary == 1);
        $modal.show();
    });
    // Close modal on background click (optional)
    $(document).on('click', '#epm-contact-phone-modal', function(e) {
        if (e.target === this) $(this).hide();
    });
    // Submit form via AJAX
    $('#epm-contact-phone-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var data = $form.serialize();
        $.post(ajaxurl, $.extend({ action: 'epm_save_contact_phone' }, $form.serializeObject ? $form.serializeObject() : $form.serializeArray()), function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data && response.data.message ? response.data.message : 'Error saving phone.');
            }
        });
    });
});
// Helper for serializeObject if not present
if (typeof jQuery.fn.serializeObject !== 'function') {
    jQuery.fn.serializeObject = function() {
        var obj = {};
        jQuery.each(this.serializeArray(), function(i, o) {
            obj[o.name] = o.value;
        });
        return obj;
    };
}
