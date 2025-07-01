// assets/js/epm-section-modal.js
jQuery(document).ready(function($) {
    // Commented out AJAX submit intercept to allow standard POST
    /*
    $(document).on('submit', '.epm-modal-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var data = $form.serializeArray();
        var section = $form.closest('.epm-modal-content').find('input[name=section]').val() || '';
        data.push({name: 'action', value: 'epm_save_client_data'});
        data.push({name: 'section', value: section});
        data.push({name: 'nonce', value: epmSectionModal.nonce});
        $.post(epmSectionModal.ajaxurl, data, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || 'Error saving record.');
            }
        });
    });
    */
});
