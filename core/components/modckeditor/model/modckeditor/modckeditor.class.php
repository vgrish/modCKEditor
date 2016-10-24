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

    public $typesVariables;

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
        $output = '';

        $config = $this->config;
        $config['connector_url'] = $this->config['connectorUrl'];

        foreach (array('resource', 'user') as $key) {
            ${$key} = $this->modx->getOption($key, $scriptProperties);
            if (is_object(${$key}) AND ${$key} instanceof xPDOObject) {
                $config[$key] = ${$key}->toArray();
            }
        }

        if (!empty($set['config'])) {
            $output .= "
            <script type='text/javascript'>
                Ext.ns('modckeditor');
                modckeditor.config={$this->modx->toJSON($config)};
                modckeditor.editorConfig = {$this->modx->toJSON($this->getEditorConfig())};
                Ext.onReady(function(){
                    modckeditor.loadForTVs();
                });
            </script>";
        }

        if (!empty($set['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        }

        if (!empty($set['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/modckeditor.js');
        }

        if (!empty($set['ckeditor'])) {
            $controller->addJavascript($this->config['assetsUrl'] . 'vendor/ckeditor/ckeditor.js');
        }

        $controller->addLexiconTopic('modckeditor:default');

        return $output;
    }

    public function getEditorConfig()
    {
        $config = array(
            'baseHref' => $this->modx->getOption('site_url', null, '/', true),
        );

        $prefix = 'modckeditor_ckeditor';
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

        foreach ($config as $key => $value) {
            $config[$key] = $this->getValueVariable($key, $config);
        }

        return $config;
    }

    public function getTypesVariables($reload = false)
    {
        if (!$this->typesVariables OR $reload) {
            $result = array();
            $typesVariables = json_decode($this->getOption('types_variables', null), 1);
            foreach ($typesVariables as $type => $variables) {
                foreach ($variables as $key) {
                    $result[$key] = $type;
                }
            }
            $this->typesVariables = $result;
        }

        return $this->typesVariables;
    }

    public function getTypeVariable($key = '')
    {
        $typesVariables = $this->getTypesVariables();

        return isset($typesVariables[$key]) ? $typesVariables[$key] : null;
    }

    public function getValueVariable($key = '', array $values = array())
    {
        if (!isset($values[$key])) {
            return null;
        }

        $type = $this->getTypeVariable($key);
        switch ($type) {
            case 'array':
                $value = json_decode($values[$key], 1);
                break;
            case 'bool':
                $value = (bool)$values[$key];
                break;
            case 'int':
                $value = (int)$values[$key];
                break;
            default:
                $value = $values[$key];
                break;
        }

        return $value;
    }

}