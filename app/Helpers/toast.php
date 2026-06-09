<?php

if (!function_exists('toast')) {
    function toast($message, $type = 'success', $title = '')
    {
        session()->flash('toast', [
            'message' => $message,
            'type' => $type,
            'title' => $title
        ]);
    }
}