<?php
/**
 * Public entry point for XAMPP/Apache
 * Delegates to the API entry point
 */

$base = dirname(__DIR__);
require_once $base . '/api/index.php';