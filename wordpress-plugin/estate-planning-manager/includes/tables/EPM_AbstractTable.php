<?php
namespace EstatePlanningManager\Tables;
// Abstract base class for EPM table classes
if (!defined('ABSPATH')) exit;

abstract class EPM_AbstractTable {
    /**
     * Create the table. Override in child if needed.
     */
    public function create($charset_collate) {
        // Default: do nothing
    }
    /**
     * Populate the table with default data. Override in child if needed.
     */
    public function populate($charset_collate) {
        // Default: do nothing
    }
}
