<?php

/**
 * The base class for modCKEditor.
 */
class modCKEditor
{
    /** @var modX $modx */
    public $modx;
    /** @var mixed|null $namespace */
    public $namespace = 'modckeditor';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modckeditor/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modckeditor/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modckeditor/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'      => $this->namespace,
            'assetsBasePath' => MODX_ASSETS_PATH,
            'assetsBaseUrl'  => MODX_ASSETS_URL,

            'assetsUrl'    => $assetsUrl,
            'cssUrl'       => $assetsUrl . 'css/',
            'jsUrl'        => $assetsUrl . 'js/',
            'connectorUrl' => $connectorUrl,

            'corePath'  => $corePath,
            'modelPath' => $corePath . 'model/'

        ), $config);

        $this->modx->addPackage('modckeditor', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modckeditor:default');
        $this->namespace = $this->getOption('namespace', $config, 'modckeditor');
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public function loadControllerJsCss(modManagerController $controller, array $set = array())
    {
        $controller->addLexiconTopic('modckeditor:default');

        $config = $this->config;
        foreach (array('resource', 'user') as $key) {
            if (isset($config[$key]) AND is_object($config[$key]) AND $config[$key] instanceof xPDOObject) {
                /** @var $config xPDOObject[] */
                $row = $config[$key]->toArray();
                unset($config[$key]);
                $config[$key] = $row;
            }
        }

        $config['connector_url'] = $this->config['connectorUrl'];

        if (!empty($set['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        }

        if (!empty($set['config'])) {
            $controller->addHtml("
            <script type='text/javascript'>
                Ext.ns(\"modckeditor\");
                modckeditor.config={$this->modx->toJSON($config)};
                modckeditor.editorConfig = {$this->modx->toJSON($this->getEditorConfig())};
                Ext.onReady(function(){
                    modckeditor.loadForTVs();
                }); 
            </script>"
            );
        }

        if (!empty($set['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/modckeditor.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
        }

        if (!empty($set['ckeditor'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/ckeditor/ckeditor.js');
        }
    }

    public function getEditorConfig()
    {

        return array();
    }
}