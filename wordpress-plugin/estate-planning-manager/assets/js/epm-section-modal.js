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

    // Modal open/close logic for Add Contact
    $(document).on('click', '.epm-add-contact-btn', function() {
        var section = $(this).data('section');
        $('#epm-add-contact-modal').attr('data-section', section).show();
        $('#epm-add-contact-form').attr('data-section', section)[0].reset();
        $('#epm-person-fields').show();
        $('#epm-org-fields').hide();
    });
    $(document).on('click', '.epm-close', function() {
        $('#epm-add-contact-modal').hide();
    });
    // Switch fields based on type
    $(document).on('change', '#epm-contact-type', function() {
        if ($(this).val() === 'person') {
            $('#epm-person-fields').show();
            $('#epm-org-fields').hide();
        } else {
            $('#epm-person-fields').hide();
            $('#epm-org-fields').show();
        }
    });
    // AJAX submit for Add Contact
    $(document).on('submit', '#epm-add-contact-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var section = $form.attr('data-section');
        var data = $form.serializeArray();
        data.push({name: 'action', value: 'epm_add_contact'});
        data.push({name: 'section', value: section});
        $.post(ajaxurl, data, function(response) {
            if (response.success && response.data && response.data.id && response.data.name) {
                var selector = section === 'debtors' ? '#epm-debtors-contact-selector' : '#epm-creditors-contact-selector';
                $(selector).append('<option value="' + response.data.id + '">' + response.data.name + '</option>');
                $(selector).val(response.data.id);
                $('#epm-add-contact-modal').hide();
            } else {
                alert(response.data && response.data.message ? response.data.message : 'Error adding contact.');
            }
        });
    });
});
