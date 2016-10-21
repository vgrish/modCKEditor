<?php

/**
 * The home manager controller for modckeditor.
 *
 */
class modckeditorHomeManagerController extends modckeditorMainController
{
    /* @var modckeditor $modckeditor */
    public $modckeditor;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('modckeditor');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->modckeditor->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->modckeditor->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "modckeditor-page-home"});
		});
		</script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->modckeditor->config['templatesPath'] . 'home.tpl';
    }
}