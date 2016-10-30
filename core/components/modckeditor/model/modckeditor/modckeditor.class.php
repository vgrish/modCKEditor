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

    /** @var bool $initEditor */
    public $initEditor = false;

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
            } elseif (array_key_exists("mcked_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("mcked_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param string $key
     *
     * @return null
     */
    public function getTypeVariable($key = '')
    {
        return isset($this->config['config_variables'][$key]) ? $this->config['config_variables'][$key] : null;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->config = array_merge($this->config, array(), $config);

        if (!isset($this->config['config_variables'])) {
            $tmp = array();
            $rows = json_decode($this->getOption('config_variables', null), true);
            foreach ($rows as $type => $variables) {
                foreach ($variables as $key) {
                    $tmp[$key] = $type;
                }
            }
            $this->config['config_variables'] = $tmp;
        }
    }


    /**
     * @param string $ctx
     * @param array  $config
     *
     * @return bool|mixed
     */
    public function initialize($ctx = 'web', array $config = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $initialize = false;
        $this->modx->error->reset();
        $this->setConfig(array_merge($config, array('ctx' => $ctx)));

        if ((!defined('MODX_API_MODE') OR !MODX_API_MODE)) {

            $useEditor = $this->modx->getOption('use_editor', false);
            $whichEditor = $this->modx->getOption('which_editor', '');
            if ($useEditor AND $whichEditor == 'modckeditor') {
                $initialize = true;
            }
        }

        $this->initialized[$ctx] = $this->initEditor = $initialize;

        return $initialize;
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

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public function array_merge_recursive_ex(array & $array1 = array(), array & $array2 = array())
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) AND isset($merged[$key]) AND is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_ex($merged[$key], $value);
            } else {
                if (is_numeric($key)) {
                    if (!in_array($value, $merged)) {
                        $merged[] = $value;
                    }
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * @return array
     */
    public function getCKEditorConfig()
    {
        $config = $variables = array();

        $q = $this->modx->newQuery('modSystemSetting');
        $q->where(array(
            'key:LIKE'      => "%_cfg_%",
            'AND:area:LIKE' => "%_cfg%",
        ));
        $q->select('key,area');
        if ($q->prepare() AND $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $key = str_replace($row['area'] . '_', '', $row['key']);
                $value = $this->modx->getOption($row['key'], null);
                if (isset($variables[$key])) {
                    $variables[$key][] = $value;
                } else {
                    $variables[$key] = array($value);
                }
            }
        }

        /* merge variables */
        foreach ($variables as $key => $values) {
            foreach ($values as $value) {
                $tmp = json_decode($value, true);
                if (empty($tmp)) {
                    $tmp = $value;
                }
                if ($tmp == '') {
                    continue;
                }
                if (!is_array($tmp)) {
                    $tmp = array($tmp);
                }

                if (isset($config[$key]) AND is_array($tmp)) {
                    $config[$key] = $this->array_merge_recursive_ex($config[$key], $tmp);
                } else {
                    $config[$key] = $tmp;
                }
            }
        }

        /* change type variables */
        foreach ($config as $key => $value) {
            $type = $this->getTypeVariable($key);

            switch ($type) {
                case 'string':
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $value = (string)$value;
                    break;
                case 'integer':
                    if (is_array($value)) {
                        $value = end($value);
                    }
                    $value = (integer)$value;
                    break;
                case 'boolean':
                    if (is_array($value)) {
                        $value = end($value);
                    }
                    $value = (boolean)$value;
                    break;
                case 'array':
                    break;
                default:
                    break;
            }

            $config[$key] = $value;
        }

        return $config;
    }

    /**
     * @return array
     */
    public function getEditorConfig()
    {
        $config = array(
            'baseHref' => $this->modx->getOption('site_url', null, '/', true),
        );
        $config = array_merge($config, $this->getCKEditorConfig());

        return $config;
    }

    /**
     * @param modManagerController $controller
     * @param array                $set
     * @param array                $scriptProperties
     *
     * @return string
     */
    public function loadControllerJsCss(
        modManagerController $controller,
        array $set = array(),
        array $scriptProperties = array()
    ) {
        $output = '';

        $config = $this->config;
        $config['connector_url'] = $this->config['connectorUrl'];

        foreach (array('resource', 'user') as $key) {
            /** @var xPDOObject $resource */
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

        $this->modx->invokeEvent('modCKEditorOnLoadControllerJsCss', array('controller' => &$controller));

        return $output;
    }

    /**
     * @param array $row
     * @param bool  $addVariable
     *
     * @return mixed
     */
    public function addConfigSetting(array $row = array(), $addVariable = true)
    {
        $row = array_merge(array(
            'key'   => 'mcked_undefined_undefined',
            'area'  => 'mcked_undefined',
            'type'  => 'undefined',
            'value' => ''
        ), $row);

        $variable = str_replace($row['area'] . '_', '', $row['key']);
        $key = $row['area'] . '_' . $variable;

        if ($addVariable) {
            $this->addConfigVariable($row['type'], $variable);
        }

        $value = $this->_getSetting($key, $row);
        $value = json_decode($value, true);

        if (is_array($value)) {
            $value = $this->array_merge_recursive_ex($value, $row['value']);
        } else {
            $value = $row['value'];
        }

        if (is_array($value)) {
            $value = json_encode($value, true);
        }

        return $this->_updateSetting($key, $value);
    }

    /**
     * @param $type
     * @param $name
     *
     * @return mixed
     */
    public function addConfigVariable($type, $name)
    {
        $key = 'mcked_config_variables';
        $variables = $this->_getSetting($key);
        $variables = json_decode($variables, true);

        $type = strtolower($type);

        if (isset($variables[$type])) {
            $variables[$type][] = $name;
        } else {
            $variables[$type] = array($name);
        }

        $variables[$type] = array_keys(array_flip($variables[$type]));
        $variables[$type] = array_diff($variables[$type], array(''));

        return $this->_updateSetting($key, json_encode($variables, true));
    }

    /**
     * @param $type
     * @param $name
     *
     * @return mixed
     */
    public function removeConfigVariable($type, $name)
    {
        $key = 'mcked_config_variables';
        $variables = $this->_getSetting($key);
        $variables = json_decode($variables, true);

        $type = strtolower($type);
        if (isset($variables[$type])) {
            $variables[$type] = array_flip($variables[$type]);
            unset($variables[$type][$name]);
            $variables[$type] = array_flip($variables[$type]);
            sort($variables[$type]);
        }

        return $this->_updateSetting($key, json_encode($variables, true));
    }

    /**
     * @param       $key
     * @param array $row
     *
     * @return mixed
     */
    protected function _getSetting($key, array $row = array())
    {
        if (strlen($key) > 50) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "<b>{$key}</b> length > 50");

            return false;
        }

        if (!$tmp = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $this->modx->newObject('modSystemSetting');

            $tmp->fromArray(array_merge(array(
                'xtype'     => 'textarea',
                'namespace' => 'modckeditor',
                'area'      => 'mcked_main'
            ), $row), '', true, true);

            $tmp->set('key', $key);
            $tmp->set('value', '');
            $tmp->save();
        }

        return $tmp->get('value');
    }

    /**
     * @param       $key
     * @param       $value
     * @param array $row
     *
     * @return mixed
     */
    protected function _updateSetting($key, $value, array $row = array())
    {
        if (strlen($key) > 50) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "<b>{$key}</b> length > 50");

            return false;
        }

        if (!$tmp = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $this->modx->newObject('modSystemSetting');

            $tmp->fromArray(array_merge(array(
                'xtype'     => 'textarea',
                'namespace' => 'modckeditor',
                'area'      => 'mcked_main'
            ), $row), '', true, true);

            $tmp->set('key', $key);
        }
        $tmp->set('value', $value);
        $tmp->save();

        return $tmp->get('value');
    }

}