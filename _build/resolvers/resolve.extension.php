<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx->addExtensionPackage('modckeditor', '[[++core_path]]components/modckeditor/model/');
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->removeExtensionPackage('modckeditor');
        break;
}
return true;