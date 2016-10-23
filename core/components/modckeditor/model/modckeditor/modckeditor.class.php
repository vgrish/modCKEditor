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
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modckeditor/');

        $this->config = array_merge(array(
            'namespace'    => $this->namespace,
            'corePath'     => $corePath,
            'modelPath'    => $corePath . 'model/',
            'assetsUrl'    => $assetsUrl,
            'cssUrl'       => $assetsUrl . 'css/',
            'jsUrl'        => $assetsUrl . 'js/',
            'connectorUrl' => $assetsUrl . 'connector.php',
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

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }


    public function loadControllerJsCss(
        modManagerController $controller,
        array $set = array(),
        array $scriptProperties = array()
    ) {
        $controller->addLexiconTopic('modckeditor:default');

        $config = $this->config;
        $config['connector_url'] = $this->config['connectorUrl'];

        foreach (array('resource', 'user') as $key) {
            $object = $this->modx->getOption($key, $scriptProperties);
            if (is_object($object) AND $object instanceof xPDOObject) {
                $config[$key] = $object->toArray();
            }
        }

        if (!empty($set['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        }

        if (!empty($set['config'])) {
            $controller->addHtml("
            <script type='text/javascript'>
                Ext.ns(\"modckeditor\");
                modCKEditor = {};
                modCKEditor.config={$this->modx->toJSON($config)};
                modCKEditor.editorConfig = {$this->modx->toJSON($this->getEditorConfig())};
                Ext.onReady(function(){
                    modckeditor.loadForTVs();
                }); 
            </script>"
            );
        }

        if (!empty($set['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/modckeditor.js');
        }

        if (!empty($set['ckeditor'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/ckeditor/ckeditor.js');
        }

    }

    public function getCKEditorConfig()
    {
        $prefix = 'modckeditor_ckeditor';
        $config = array();

        $q = $this->modx->newQuery('modSystemSetting');
        $q->where(array(
            'area' => "{$prefix}_config"
        ));
        $q->select('key');
        if ($q->prepare() AND $q->stmt->execute()) {
            while ($key = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                $config[str_replace("{$prefix}_", '', $key)] = $this->modx->getOption($key, null);
            }
        }

        return (array)$config;
    }


    public function getEditorConfig()
    {
        $config = array_merge(array(
            'baseHref' => $this->modx->getOption('site_url', null, '/', true),
        ), $this->getCKEditorConfig());


        /* list to array */
        foreach (array(
                     'contentsCss'
                 ) as $key) {
            if (isset($config[$key])) {
                $config[$key] = $this->explodeAndClean($config[$key]);
            }
        }

        /* json to array */
        foreach (array(
                     'toolbar',
                     'toolbarGroups',
                     'editorCompact',
                     'addExternalPlugins',
                     'addExternalSkin',
                 ) as $key) {
            if (isset($config[$key])) {
                $config[$key] = json_decode($config[$key], 1);
            }
        }

        /* string to bool */
        foreach (array(
                     'entities',
                     'toolbarCanCollapse',
                     'disableObjectResizing',
                     'disableNativeSpellChecker',

                 ) as $key) {
            if (isset($config[$key])) {
                $config[$key] = (bool)$config[$key];
            }
        }

        return $config;
    }

}