<?php

$settings = array();

$tmp = array(

    'ckeditor_skin'           => array(
        'value' => 'moono',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_language'       => array(
        'value' => 'ru',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_allowedContent' => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_contentsCss'    => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_uiColor'        => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_removePlugins'  => array(
        'value' => 'autogrow,bidi,font,forms,liststyle,justify,pagebreak,preview,print,colorbutton,indentblock,newpage,language,save,selectall,smiley',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_toolbar'        => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_toolbarGroups'  => array(
        'value' => '[{"name":"document","groups":["mode","document","doctools"]},{"name":"clipboard","groups":["clipboard","undo"]},{"name":"editing","groups":["find","selection"]},{"name":"links"},{"name":"insert"},{"name":"forms"},"/",{"name":"basicstyles","groups":["basicstyles","cleanup"]},{"name":"paragraph","groups":["list","indent","blocks","align","bidi"]},{"name":"styles"},{"name":"colors"},{"name":"tools"},{"name":"others"},{"name":"about"}]',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_format_tags'    => array(
        'value' => 'p;h1;h2;h3;h4;h5;h6;pre;div',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_extraPlugins'   => array(
        'value' => '',
        'xtype' => 'textarea',
        'area'  => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_entities' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_toolbarCanCollapse' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'modckeditor_ckeditor_config',
    ),
    'ckeditor_disableObjectResizing' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'modckeditor_ckeditor_config',
    ),
    //

    //allowedContent
    //временные
    'assets_path'             => array(
        'value' => '{base_path}modckeditor/assets/components/modckeditor/',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_temp',
    ),
    'assets_url'              => array(
        'value' => '/modckeditor/assets/components/modckeditor/',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_temp',
    ),
    'core_path'               => array(
        'value' => '{base_path}modckeditor/core/components/modckeditor/',
        'xtype' => 'textfield',
        'area'  => 'modckeditor_temp',
    ),

    /*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'modckeditor_main',
	),
	*/
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
