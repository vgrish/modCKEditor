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