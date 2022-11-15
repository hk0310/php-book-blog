<?php
    function generateSlug($string) {
        $string = strtolower($string);
        $replaceDash = array(' ', '_', '"', '/');
        $string = str_replace($replaceDash, '-', $string);
        $replaceBlank = array('\'', '\\', ':', ',', '.', '[', ']');
        $string = str_replace($replaceBlank, '', $string);

        return $string;
    }
?>