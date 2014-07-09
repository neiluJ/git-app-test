<?php
$vh = $this->_helper;

switch($this->type) {
    case 'display_image':
        include __DIR__ .'/blob/display-image.php';
        break;
    case 'display_binary':
        include __DIR__ .'/blob/display-binary.php';
        break;
    case 'display_markdown':
        include __DIR__ .'/blob/display-markdown.php';
        break;
    case 'display_text':
        include __DIR__ .'/blob/display-text.php';
        break;
}
