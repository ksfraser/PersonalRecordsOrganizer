<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('EPM DEBUG: test-post.php POST reached');
    echo 'POST OK';
} else {
    echo 'GET OK';
}
