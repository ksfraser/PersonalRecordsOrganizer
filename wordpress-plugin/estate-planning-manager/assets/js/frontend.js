// estate-planning-manager/assets/js/frontend.js
// Frontend validation for Estate Planning Manager

(function($) {
    function validateEmail(email) {
        // RFC 5322 Official Standard
        return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(email);
    }
    function validatePhone(phone) {
        // Accepts international numbers, e.g. +1 555-555-5555, +44 20 7123 1234, +91-9876543210, etc.
        return /^\+?[0-9 .\-()]{7,20}$/.test(phone);
    }
    $(document).on('submit', '.epm-form', function(e) {
        var valid = true;
        var emailFields = $(this).find('input[type="email"], input.epm-email');
        var phoneFields = $(this).find('input[type="tel"], input.epm-phone');
        emailFields.each(function() {
            if ($(this).val() && !validateEmail($(this).val())) {
                valid = false;
                $(this).addClass('epm-invalid');
            } else {
                $(this).removeClass('epm-invalid');
            }
        });
        phoneFields.each(function() {
            if ($(this).val() && !validatePhone($(this).val())) {
                valid = false;
                $(this).addClass('epm-invalid');
            } else {
                $(this).removeClass('epm-invalid');
            }
        });
        if (!valid) {
            alert('Please correct invalid email or phone fields.');
            e.preventDefault();
        }
    });
})(jQuery);

// Add Person/Institute modal logic
(function($) {
    function showModal(type) {
        var modalId = type === 'person' ? '#epm-add-person-modal' : '#epm-add-institute-modal';
        $(modalId).show();
    }
    function hideModal(type) {
        var modalId = type === 'person' ? '#epm-add-person-modal' : '#epm-add-institute-modal';
        $(modalId).hide();
    }
    $(document).on('click', '.epm-add-person-btn', function() {
        showModal('person');
    });
    $(document).on('click', '.epm-add-institute-btn', function() {
        showModal('institute');
    });
    $(document).on('click', '.epm-modal-cancel', function() {
        $(this).closest('.epm-modal').hide();
    });
    // AJAX submit for Add Person
    $(document).on('submit', '#epm-add-person-form', function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(ajaxurl, form.serialize() + '&action=epm_add_person', function(resp) {
            if (resp.success) {
                alert('Person added!');
                hideModal('person');
                // Update all person selects
                $("select.epm-person-select").each(function() {
                    $(this).append('<option value="' + resp.data.id + '">' + resp.data.name + '</option>');
                });
            } else {
                alert('Error: ' + resp.data);
            }
        });
    });
    // AJAX submit for Add Institute
    $(document).on('submit', '#epm-add-institute-form', function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(ajaxurl, form.serialize() + '&action=epm_add_institute', function(resp) {
            if (resp.success) {
                alert('Institute added!');
                hideModal('institute');
                // Update all institute selects
                $("select.epm-institute-select").each(function() {
                    $(this).append('<option value="' + resp.data.id + '">' + resp.data.name + '</option>');
                });
            } else {
                alert('Error: ' + resp.data);
            }
        });
    });
})(jQuery);

// Investments lender type logic
(function($) {
    $(document).on('change', '#epm-lender-type-select', function() {
        var v = $(this).val();
        if (v === 'person') {
            $("select[name='lender_org_id']").val('');
        } else if (v === 'organization') {
            $("select[name='lender_person_id']").val('');
        } else {
            $("select[name='lender_person_id'], select[name='lender_org_id']").val('');
        }
    });
})(jQuery);
