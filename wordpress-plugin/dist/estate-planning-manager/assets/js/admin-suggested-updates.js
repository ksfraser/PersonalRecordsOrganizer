/**
 * Admin Suggested Updates JavaScript
 * 
 * Handles the admin interface interactions for suggested updates
 */

jQuery(document).ready(function($) {
    
    // Handle approve update button clicks
    $('.epm-approve-update').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm(epm_suggested_updates.strings.confirm_approve)) {
            return;
        }
        
        var updateId = $(this).data('update-id');
        var $button = $(this);
        var $row = $button.closest('tr');
        
        // Disable button and show loading
        $button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: epm_suggested_updates.ajax_url,
            type: 'POST',
            data: {
                action: 'epm_approve_suggested_update',
                update_id: updateId,
                nonce: epm_suggested_updates.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update the row to show approved status
                    $row.find('.epm-status').removeClass('epm-status-pending').addClass('epm-status-approved').text('Approved');
                    $row.find('td:last-child').html('<span class="description">No actions available</span>');
                    
                    // Show success message
                    showNotice('success', response.data);
                } else {
                    showNotice('error', response.data || epm_suggested_updates.strings.error);
                    $button.prop('disabled', false).text('Approve');
                }
            },
            error: function() {
                showNotice('error', epm_suggested_updates.strings.error);
                $button.prop('disabled', false).text('Approve');
            }
        });
    });
    
    // Handle reject update button clicks
    $('.epm-reject-update').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm(epm_suggested_updates.strings.confirm_reject)) {
            return;
        }
        
        var updateId = $(this).data('update-id');
        var $button = $(this);
        var $row = $button.closest('tr');
        
        // Disable button and show loading
        $button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: epm_suggested_updates.ajax_url,
            type: 'POST',
            data: {
                action: 'epm_reject_suggested_update',
                update_id: updateId,
                nonce: epm_suggested_updates.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update the row to show rejected status
                    $row.find('.epm-status').removeClass('epm-status-pending').addClass('epm-status-rejected').text('Rejected');
                    $row.find('td:last-child').html('<span class="description">No actions available</span>');
                    
                    // Show success message
                    showNotice('success', response.data);
                } else {
                    showNotice('error', response.data || epm_suggested_updates.strings.error);
                    $button.prop('disabled', false).text('Reject');
                }
            },
            error: function() {
                showNotice('error', epm_suggested_updates.strings.error);
                $button.prop('disabled', false).text('Reject');
            }
        });
    });
    
    // Handle select all checkbox
    $('#cb-select-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('input[name="suggested_updates[]"]').prop('checked', isChecked);
    });
    
    // Handle individual checkbox changes
    $('input[name="suggested_updates[]"]').on('change', function() {
        var totalCheckboxes = $('input[name="suggested_updates[]"]').length;
        var checkedCheckboxes = $('input[name="suggested_updates[]"]:checked').length;
        
        $('#cb-select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
    
    // Handle bulk actions form submission
    $('form').on('submit', function(e) {
        var action = $('select[name="action"]').val();
        var selectedUpdates = $('input[name="suggested_updates[]"]:checked');
        
        if (action && selectedUpdates.length > 0) {
            var confirmMessage = '';
            
            if (action === 'approve') {
                confirmMessage = epm_suggested_updates.strings.confirm_bulk_approve;
            } else if (action === 'reject') {
                confirmMessage = epm_suggested_updates.strings.confirm_bulk_reject;
            }
            
            if (confirmMessage && !confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Show notice function
    function showNotice(type, message) {
        var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
        
        // Remove existing notices
        $('.notice').remove();
        
        // Add new notice
        $('.wrap h1').after($notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut();
        }, 5000);
        
        // Handle dismiss button
        $notice.on('click', '.notice-dismiss', function() {
            $notice.fadeOut();
        });
    }
    
    // Add dismiss functionality to existing notices
    $(document).on('click', '.notice-dismiss', function() {
        $(this).closest('.notice').fadeOut();
    });
    
    // Highlight differences in suggested values
    $('.epm-suggested-value').each(function() {
        var $suggestedCell = $(this);
        var $currentCell = $suggestedCell.closest('tr').find('.epm-value-display').first();
        
        var currentText = $currentCell.text().trim();
        var suggestedText = $suggestedCell.text().trim();
        
        if (currentText !== suggestedText) {
            $suggestedCell.addClass('epm-value-different');
        }
    });
    
    // Add tooltips for long values
    $('.epm-value-display').each(function() {
        var $element = $(this);
        var text = $element.text();
        
        if (text.length > 100) {
            $element.attr('title', text);
        }
    });
    
    // Filter form auto-submit on change
    $('.epm-suggested-updates-filters select').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Add loading state to filter form
    $('.epm-suggested-updates-filters form').on('submit', function() {
        var $form = $(this);
        var $submitButton = $form.find('input[type="submit"]');
        
        $submitButton.prop('disabled', true).val('Loading...');
        
        // Re-enable after a delay in case of errors
        setTimeout(function() {
            $submitButton.prop('disabled', false).val('Filter');
        }, 5000);
    });
    
    // Add row highlighting on hover
    $('table.wp-list-table tbody tr').on('mouseenter', function() {
        $(this).addClass('epm-row-highlight');
    }).on('mouseleave', function() {
        $(this).removeClass('epm-row-highlight');
    });
    
    // Add expand/collapse for long values
    $('.epm-value-display').each(function() {
        var $element = $(this);
        var text = $element.html();
        
        if (text.length > 200) {
            var shortText = text.substring(0, 200) + '...';
            var $expandLink = $('<a href="#" class="epm-expand-value">Show more</a>');
            var $collapseLink = $('<a href="#" class="epm-collapse-value">Show less</a>');
            
            $element.html(shortText + ' ').append($expandLink);
            
            $expandLink.on('click', function(e) {
                e.preventDefault();
                $element.html(text + ' ').append($collapseLink);
            });
            
            $(document).on('click', '.epm-collapse-value', function(e) {
                e.preventDefault();
                $element.html(shortText + ' ').append($expandLink);
            });
        }
    });
    
    // Add keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + A to select all
        if ((e.ctrlKey || e.metaKey) && e.which === 65) {
            var $checkboxes = $('input[name="suggested_updates[]"]');
            if ($checkboxes.length > 0) {
                e.preventDefault();
                $checkboxes.prop('checked', true);
                $('#cb-select-all').prop('checked', true);
            }
        }
        
        // Escape to deselect all
        if (e.which === 27) {
            $('input[name="suggested_updates[]"]').prop('checked', false);
            $('#cb-select-all').prop('checked', false);
        }
    });
    
    // Add status indicators
    $('.epm-status').each(function() {
        var $status = $(this);
        var status = $status.text().toLowerCase();
        
        var icon = '';
        switch (status) {
            case 'pending':
                icon = '⏳';
                break;
            case 'approved':
                icon = '✅';
                break;
            case 'rejected':
                icon = '❌';
                break;
        }
        
        if (icon) {
            $status.prepend(icon + ' ');
        }
    });
    
    // Add progress indicator for bulk actions
    var originalSubmitHandler = $('form').data('events') ? $('form').data('events').submit : null;
    
    $('form').off('submit').on('submit', function(e) {
        var $form = $(this);
        var action = $form.find('select[name="action"]').val();
        var selectedCount = $form.find('input[name="suggested_updates[]"]:checked').length;
        
        if (action && selectedCount > 0) {
            var $submitButton = $form.find('input[name="bulk_action"]');
            $submitButton.prop('disabled', true).val('Processing ' + selectedCount + ' items...');
            
            // Show progress bar
            var $progressBar = $('<div class="epm-progress-bar"><div class="epm-progress-fill"></div></div>');
            $form.after($progressBar);
            
            // Animate progress bar
            setTimeout(function() {
                $progressBar.find('.epm-progress-fill').css('width', '100%');
            }, 100);
        }
        
        // Call original handler if it exists
        if (originalSubmitHandler) {
            return originalSubmitHandler.apply(this, arguments);
        }
    });
});
