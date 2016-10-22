<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $tmp = explode('/', MODX_ASSETS_URL);
        $assets = $tmp[count($tmp) - 2];
        $properties = array(
            'name'        => 'modCKEditor Files',
            'description' => 'Default media source for files of modCKEditorFiles',
            'class_key'   => 'sources.modFileMediaSource',
            'properties'  => array(
                'basePath' => array(
                    'name'    => 'basePath',
                    'desc'    => 'prop_file.basePath_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => $assets . '/modckeditor/',
                ),
                'baseUrl'  => array(
                    'name'    => 'baseUrl',
                    'desc'    => 'prop_file.baseUrl_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => 'assets/modckeditor/',
                ),
            )
        ,
            'is_stream'   => 1
        );
        /* @var $source modMediaSource */
        if (!$source = $modx->getObject('sources.modMediaSource', array('name' => $properties['name']))) {
            $source = $modx->newObject('sources.modMediaSource', $properties);
        } else {
            $default = $source->get('properties');
            foreach ($properties['properties'] as $k => $v) {
                if (!array_key_exists($k, $default)) {
                    $default[$k] = $v;
                }
            }
            $source->set('properties', $default);
        }
        $source->save();
        if ($setting = $modx->getObject('modSystemSetting', array('key' => 'modckeditor_source_default'))) {
            if (!$setting->get('value')) {
                $setting->set('value', $source->get('id'));
                $setting->save();
            }
        }
        @mkdir(MODX_ASSETS_PATH . 'modckeditor/');
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;