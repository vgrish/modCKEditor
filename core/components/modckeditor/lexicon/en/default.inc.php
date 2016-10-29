<?php

include_once 'setting.inc.php';

$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (strpos($file, 'mcke.') === 0) {
        @include_once($file);
    }
}

$_lang['modckeditor'] = 'modckeditor';
