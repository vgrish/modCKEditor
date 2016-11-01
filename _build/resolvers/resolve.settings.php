<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

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

        if (!$modCKEditor) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[mckedExtraPlugins] Could not load modCKEditor');

            return false;
        }

        $variables = array(
            'string'  => array(
                'skin',
                'language',
                'allowedContent',
                'uiColor',
                'removePlugins',
                'format_tags',
                'codeSnippet_theme',
                'extraPlugins',
            ),
            'integer' => array(
                'enterMode',
                'shiftEnterMode'
            ),
            'boolean' => array(
                'entities',
                'autoParagraph',
                'toolbarCanCollapse',
                'disableObjectResizing',
                'disableNativeSpellChecker',
                'fillEmptyBlocks',
                'basicEntities',
                'enableModTemplates',
            ),
            'array'   => array(
                'toolbar',
                'toolbarGroups',
                'contentsCss',
                'editorCompact',
                'addExternalPlugins',
                'addExternalSkin',
                'addTemplates',
            ),
        );

        foreach ($variables as $type => $row) {
            foreach ($row as $name) {
                $modCKEditor->addConfigVariable($type, $name);
            }
        }

        //////
        $settings = array(
            array(
                'key'   => 'extraPlugins',
                'area'  => 'mcked_cfg',
                'type'  => 'string',
                'value' => array(
                    'mod_resource_view'
                )
            ),
            array(
                'key'   => 'addExternalPlugins',
                'area'  => 'mcked_cfg',
                'type'  => 'array',
                'value' => array(
                    'mod_resource_view' => 'vendor/plugins/mod_resource_view/plugin.js'
                )
            ),
        );

        foreach ($settings as $row) {
            //$modCKEditor->addConfigSetting($row);
        }


        /* core */
        $key = 'which_editor';
        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $modx->newObject('modSystemSetting');
            $tmp->fromArray(array(
                'key'       => $key,
                'xtype'     => 'modx-combo-rte',
                'namespace' => 'core',
                'area'      => 'editor',
                'editedon'  => null,
            ), '', true, true);
        }
        $tmp->set('value', 'modckeditor');
        $tmp->save();

        $key = 'use_editor';
        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $modx->newObject('modSystemSetting');
            $tmp->fromArray(array(
                'key'       => $key,
                'xtype'     => 'combo-boolean',
                'namespace' => 'core',
                'area'      => 'editor',
                'editedon'  => null,
            ), '', true, true);
        }
        $tmp->set('value', 1);
        $tmp->save();

        break;
    case xPDOTransport::ACTION_UNINSTALL:

        /* core */

        $key = 'which_editor';
        if ($tmp = $modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp->set('value', '');
            $tmp->save();
        }

        $key = 'use_editor';
        if ($tmp = $modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp->set('value', 0);
            $tmp->save();
        }

        $modx->removeCollection('modSystemSetting', array('namespace' => 'modckeditor'));

        break;
}

return true;