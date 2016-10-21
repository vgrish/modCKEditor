<?php

abstract class modCKEditorPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var modCKEditor $modCKEditor */
    protected $modCKEditor;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->modCKEditor = $this->modx->modCKEditor;

        if (!is_object($this->modCKEditor) OR !($this->modCKEditor instanceof modCKEditor)) {
            $corePath = $this->modx->getOption('modckeditor_core_path', null,
                $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modckeditor/');
            $this->modCKEditor = $this->modx->getService(
                'modckeditor',
                'modCKEditor',
                $corePath . 'model/modckeditor/',
                $this->scriptProperties
            );
        }

        if (!$this->modCKEditor) {
            return;
        }
    }

    abstract public function run();
}