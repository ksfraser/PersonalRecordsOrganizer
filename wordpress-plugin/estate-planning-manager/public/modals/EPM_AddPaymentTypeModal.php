<?php
/**
 * EPM_AddPaymentTypeModal
 * Renders the Add Payment Type modal HTML for Scheduled Payments
 */
class EPM_AddPaymentTypeModal {
    public static function render() {
        ob_start();
        ?>
        <div id="epm-add-payment-type-modal" style="display:none; position:fixed; left:50%; top:30%; background:#fff; border:1px solid #ccc; padding:20px; z-index:10000;">
            <h3>Add Scheduled Payment Type</h3>
            <form id="epm-add-payment-type-form">
                <label>Value: <input type="text" name="value" required></label><br>
                <label>Label: <input type="text" name="label" required></label><br>
                <button type="submit" class="button button-primary">Add</button>
                <button type="button" id="epm-cancel-payment-type" class="button">Cancel</button>
            </form>
            <div id="epm-add-payment-type-msg"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
