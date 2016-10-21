<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var modckeditor $modckeditor */
$modckeditor = $modx->getService('modckeditor', 'modckeditor', $modx->getOption('modckeditor_core_path', null,
        $modx->getOption('core_path') . 'components/modckeditor/') . 'model/modckeditor/');
$modx->lexicon->load('modckeditor:default');

// handle request
$corePath = $modx->getOption('modckeditor_core_path', null, $modx->getOption('core_path') . 'components/modckeditor/');
$path = $modx->getOption('processorsPath', $modckeditor->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location'        => '',
));