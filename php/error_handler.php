<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='background-color: #ffebee; padding: 10px; margin: 10px; border: 1px solid #ffcdd2; border-radius: 4px;'>";
    echo "<strong>Error:</strong> [$errno] $errstr<br>";
    echo "<strong>File:</strong> $errfile<br>";
    echo "<strong>Line:</strong> $errline<br>";
    echo "</div>";
    
    // Don't execute PHP internal error handler
    return true;
}

// Set custom error handler
set_error_handler("customErrorHandler");
?> 