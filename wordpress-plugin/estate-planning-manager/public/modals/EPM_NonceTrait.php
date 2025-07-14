<?php
/**
 * EPM_NonceTrait
 * Provides nonce action/name helpers for modals and views
 */
trait EPM_NonceTrait {
    protected function getNonceAction() { return 'epm_add_record'; }
    protected function getNonceName() { return 'epm_add_record_nonce'; }
}
