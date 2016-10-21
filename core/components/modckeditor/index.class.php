<?php

/**
 * Class modckeditorMainController
 */
abstract class modckeditorMainController extends modExtraManagerController
{
    /** @var modckeditor $modckeditor */
    public $modckeditor;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('modckeditor_core_path', null,
            $this->modx->getOption('core_path') . 'components/modckeditor/');
        require_once $corePath . 'model/modckeditor/modckeditor.class.php';

        $this->modckeditor = new modckeditor($this->modx);
        $this->addCss($this->modckeditor->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->modckeditor->config['jsUrl'] . 'mgr/modckeditor.js');
        $this->addHtml('
		<script type="text/javascript">
			modckeditor.config = ' . $this->modx->toJSON($this->modckeditor->config) . ';
			modckeditor.config.connector_url = "' . $this->modckeditor->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modckeditor:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends modckeditorMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}