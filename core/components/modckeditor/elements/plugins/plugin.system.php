<?php

$corePath = $modx->getOption('modckeditor_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modckeditor/');
/** @var modCKEditor $modCKEditor */
$modCKEditor = $modx->getService(
    'modckeditor',
    'modCKEditor',
    $corePath . 'model/modckeditor/',
    array(
        'core_path' => $corePath
    )
);

$className = 'modCKEditor' . $modx->event->name;
$modx->loadClass('modCKEditorPlugin', $modCKEditor->getOption('modelPath') . 'modckeditor/systems/', true, true);
$modx->loadClass($className, $modCKEditor->getOption('modelPath') . 'modckeditor/systems/', true, true);
if (class_exists($className)) {
    /** @var $modCKEditor $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;
