// Backup of the original modal AJAX JS logic from AbstractSectionView.php
// This file is for reference only. You can re-enable AJAX in the future if needed.
document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("epm-modal");
    var btn = document.querySelector(".epm-add-new");
    var close = document.querySelector(".epm-modal-close");
    if(btn && modal && close) {
        btn.onclick = function() { modal.style.display = "block"; };
        close.onclick = function() { modal.style.display = "none"; };
        window.onclick = function(event) { if(event.target == modal) { modal.style.display = "none"; } };
    }
    // AJAX submit logic (for reference only)
    var form = document.querySelector('.epm-modal-form');
    if(form) {
        form.onsubmit = function(e) {
            e.preventDefault();
            var formData = new FormData(form);
            fetch(window.epm_ajax_url || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.data || 'Error saving record.');
                }
            });
        };
    }
});
