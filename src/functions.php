<?php

    // sanitize text
    function html_escape($text): string
    {
        if (!$text) {
            $text = '';
        }

        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }

    // handle errors
    function handle_error($error_type, $error_message, $error_file, $error_line)
    {
        error_log($error_message);
        http_response_code(500);
    }

    // handle fatal errors
    function handle_shutdown()
    {
        $error = error_get_last();            // Check for error in script
        if ($error !== null) {
            error_log($error['message']);
        }
    }
?>