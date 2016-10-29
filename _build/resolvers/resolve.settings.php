<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

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


        /* setting config_variables */
        $key = 'modckeditor_config_variables';
        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $modx->newObject('modSystemSetting');
            $tmp->fromArray(array(
                'key'       => $key,
                'xtype'     => 'textarea',
                'namespace' => 'modckeditor',
                'area'      => 'modckeditor_main',
                'editedon'  => null,
            ), '', true, true);
        }

        $value = array(
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

        $tmp->set('value', json_encode($value, 1));
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

        break;
}

return true;