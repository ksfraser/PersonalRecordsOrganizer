<?php
// Trait for singleton pattern for EPM admin classes
trait EPM_Singleton {
    private static $instance = null;
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
