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

    public function getCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cached = '';
        if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
            $cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
        }

        return $cached;
    }

    public function getCacheKey($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        if (!empty($options['cache_key'])) {
            return $options['cache_key'];
        }
        $key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

        return $key . '/' . sha1(serialize($options));
    }

    protected function getCacheOptions($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        $cacheOptions = array(
            xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
                ? 'default'
                : 'default/' . $this->namespace . '/',
            xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
                ? $options['cache_handler']
                : $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
            xPDO::OPT_CACHE_EXPIRES => (isset($options['cacheTime']) AND $options['cacheTime'] !== '')
                ? (integer)$options['cacheTime']
                : (integer)$this->modx->getOption('cache_resource_expires', null, 0),
        );

        return $cacheOptions;
    }

    public function setCache($data = array(), $options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
            $this->modx->cacheManager->set(
                $cacheKey,
                $data,
                $cacheOptions[xPDO::OPT_CACHE_EXPIRES],
                $cacheOptions
            );
        }

        return $cacheKey;
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
        $options = array(
            'cache_key' => $prefix,
            'cacheTime' => 0,
        );
        if (!$config = $this->getCache($options)) {
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
            //$this->setCache($config, $options);
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


        $config['skin'] = 'flat';

        return $config;
    }

}