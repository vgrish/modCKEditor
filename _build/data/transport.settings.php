<?php

$settings = array();

$tmp = array(

    /* CKEditor config */

    'config_skin'              => array(
        'value' => 'flat',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_language'          => array(
        'value' => 'ru',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_allowedContent'    => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_uiColor'           => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_removePlugins'     => array(
        'value' => '["autogrow","bidi","font","forms","liststyle","pagebreak","preview","print","colorbutton","indentblock","newpage","language","save","selectall","smiley","scayt","wsc"]',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_format_tags'       => array(
        'value' => 'p;h1;h2;h3;h4;h5;h6;pre;div',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_codeSnippet_theme' => array(
        'value' => 'default',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_extraPlugins'      => array(
        'value' => '["codesnippet","uploadimage","image2","embed"]',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),


    'config_enterMode'      => array(
        'value' => 2,
        'xtype' => 'numberfield',
        'area'  => 'modckeditor_config',
        'type'  => 'integer'
    ),
    'config_shiftEnterMode' => array(
        'value' => 2,
        'xtype' => 'numberfield',
        'area'  => 'modckeditor_config',
        'type'  => 'integer'
    ),


    'config_entities'                  => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_toolbarCanCollapse'        => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_disableObjectResizing'     => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_disableNativeSpellChecker' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_fillEmptyBlocks'           => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_basicEntities'             => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),
    'config_enableModTemplates'        => array(
        'value' => true,
        'xtype' => 'combo-boolean',
        'area'  => 'modckeditor_config',
        'type'  => 'boolean'
    ),


    'config_toolbar'       => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),
    'config_toolbarGroups' => array(
        'value' => '[{"name":"document","groups":["mode","document","doctools"]},{"name":"clipboard","groups":["clipboard","undo"]},{"name":"editing","groups":["find","selection"]},{"name":"links"},{"name":"insert"},{"name":"forms"},"/",{"name":"basicstyles","groups":["basicstyles","cleanup"]},{"name":"paragraph","groups":["list","indent","blocks","align","bidi"]},{"name":"styles"},{"name":"colors"},{"name":"tools"},{"name":"others"},{"name":"about"}]',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'string'
    ),
    'config_contentsCss'   => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),
    'config_editorCompact' => array(
        'value' => '{"tvs":true,"content":false}',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),

    'config_addExternalPlugins' => array(
        'value' => '',
        //'value' => '{"pagecut":"/components/modckeditor/vendor/plugins/pagecut/pagecut/plugin.js"}',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),
    'config_addExternalSkin'    => array(
        'value' => '{"flat":"/components/modckeditor/vendor/skins/flat/"}',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),
    'config_addTemplates'       => array(
        'value' => '{"default":"/components/modckeditor/vendor/ckeditor/plugins/templates/templates/default.js"}',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_config',
        'type'  => 'array'
    ),


    'source_default'  => array(
        'value' => '0',
        'xtype' => 'modx-combo-source',
        'area'  => 'modckeditor_main',
    ),
    'config_variables' => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_main',
    ),

    //временные
    /* 'assets_path'    => array(
         'value' => '{base_path}modckeditor/assets/components/modckeditor/',
         'xtype' => 'textfield',
         'area'  => 'modckeditor_temp',
     ),
     'assets_url'     => array(
         'value' => '/modckeditor/assets/components/modckeditor/',
         'xtype' => 'textfield',
         'area'  => 'modckeditor_temp',
     ),
     'core_path'      => array(
         'value' => '{base_path}modckeditor/core/components/modckeditor/',
         'xtype' => 'textfield',
         'area'  => 'modckeditor_temp',
     ),*/

);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'modckeditor_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
