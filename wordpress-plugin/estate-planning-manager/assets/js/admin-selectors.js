jQuery(document).ready(function($) {
    // Handle delete button clicks
    $('.epm-delete-btn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm(epm_ajax.confirm_delete)) {
            return;
        }
        
        var $button = $(this);
        var selectorType = $button.data('selector-type');
        var optionId = $button.data('option-id');
        
        // Disable button and show loading
        $button.text('Deleting...').css('pointer-events', 'none');
        
        $.ajax({
            url: epm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'epm_delete_selector',
                nonce: epm_ajax.nonce,
                selector_type: selectorType,
                option_id: optionId
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row from the table
                    $button.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Error: ' + response.data);
                    $button.text('Delete').css('pointer-events', 'auto');
                }
            },
            error: function() {
                alert('An error occurred while deleting the option.');
                $button.text('Delete').css('pointer-events', 'auto');
            }
        });
    });
    
    // Auto-generate value from label
    $('#option_label').on('input', function() {
        var label = $(this).val();
        var value = label.toLowerCase()
                        .replace(/[^a-z0-9\s]/g, '') // Remove special characters
                        .replace(/\s+/g, '_') // Replace spaces with underscores
                        .replace(/_+/g, '_') // Replace multiple underscores with single
                        .replace(/^_|_$/g, ''); // Remove leading/trailing underscores
        
        $('#option_value').val(value);
    });
});
