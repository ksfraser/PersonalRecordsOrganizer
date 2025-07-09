// assets/js/admin-invite.js
(function($){
    $(document).ready(function(){
        // Open invite modal
        $(document).on('click', '.epm-invite-contact-btn', function(e){
            e.preventDefault();
            $('#epm-invite-modal').remove();
            $('body').append('<div id="epm-invite-modal" class="epm-modal"><div class="epm-modal-content"><span class="epm-modal-close">&times;</span><h2>Invite Contact</h2><form id="epm-invite-form"><input type="email" name="invite_email" placeholder="Email address" required><input type="hidden" name="contact_id" value="'+$(this).data('contact-id')+'"><button type="submit">Send Invite</button></form><div class="epm-invite-result"></div></div></div>');
        });
        // Close modal
        $(document).on('click', '.epm-modal-close', function(){
            $('#epm-invite-modal').remove();
        });
        // Submit invite
        $(document).on('submit', '#epm-invite-form', function(e){
            e.preventDefault();
            var $form = $(this);
            var $result = $('.epm-invite-result');
            $result.text('Sending...');
            $.post(ajaxurl, $form.serialize()+'&action=epm_invite_contact', function(resp){
                if(resp.success){
                    $result.text('Invite sent!');
                } else {
                    $result.text(resp.data || 'Error sending invite.');
                }
            });
        });
    });
})(jQuery);
