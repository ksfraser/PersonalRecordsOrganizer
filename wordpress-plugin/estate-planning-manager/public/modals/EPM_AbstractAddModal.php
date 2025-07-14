<?php
/**
 * EPM_AbstractAddModal
 * Renders the modal HTML
 */


interface ModalViewInterface
{
    public function render($user_id = null);
}


require_once __DIR__ . '/EPM_NonceTrait.php';

abstract class EPM_AbstractAddModal implements ModalViewInterface {
    use EPM_NonceTrait;
    // Child classes must implement these
    abstract protected function getFields();
    abstract protected function getTitle();
    protected function getModalId() { return 'epm-add-record-modal'; }
    protected function getFormId() { return 'epm-add-record-form'; }
    protected function getFormAction() { return admin_url('admin-post.php'); }
    protected function getActionName() { return 'epm_add_record'; }

    public function render($user_id = null) {
        $fields = $this->getFields();
        $title = $this->getTitle();
        ob_start();
        ?>
        <div id="<?php echo esc_attr($this->getModalId()); ?>" class="epm-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;border:1px solid #ccc;border-radius:5px;padding:30px;z-index:9999;max-width:400px;width:90%;">
            <h3><?php echo esc_html($title); ?></h3>
            <form id="<?php echo esc_attr($this->getFormId()); ?>" method="post" action="<?php echo esc_url($this->getFormAction()); ?>">
                <input type="hidden" name="action" value="<?php echo esc_attr($this->getActionName()); ?>">
                <?php wp_nonce_field($this->getNonceAction(), $this->getNonceName()); ?>
                <?php
                foreach ($fields as $field) {
                    echo $this->renderFormField($field, $user_id);
                }
                ?>
                <button type="submit" class="epm-btn epm-btn-primary">Add</button>
                <button type="button" class="epm-btn epm-btn-secondary epm-modal-cancel" style="margin-left:10px;">Cancel</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    // Basic field renderer, override for custom logic
    protected function renderFormField($field, $user_id = null) {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $name = isset($field['name']) ? $field['name'] : '';
        $label = isset($field['label']) ? $field['label'] : ucfirst($name);
        $required = !empty($field['required']) ? 'required' : '';
        $html = '<div class="epm-form-group">';
        $html .= '<label for="epm_' . esc_attr($name) . '">' . esc_html($label) . ':</label>';
        switch ($type) {
            case 'select':
                $html .= '<select name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . '>';
                if (!empty($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $opt_value => $opt_label) {
                        $html .= '<option value="' . esc_attr($opt_value) . '">' . esc_html($opt_label) . '</option>';
                    }
                }
                $html .= '</select>';
                break;
            case 'textarea':
                $html .= '<textarea name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . '></textarea>';
                break;
            case 'email':
                $html .= '<input type="email" name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . ' />';
                break;
            case 'tel':
                $html .= '<input type="tel" name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . ' />';
                break;
            case 'date':
                $html .= '<input type="date" name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . ' />';
                break;
            default:
                $html .= '<input type="text" name="epm_' . esc_attr($name) . '" id="epm_' . esc_attr($name) . '" ' . $required . ' />';
        }
        $html .= '</div>';
        return $html;
    }
}
