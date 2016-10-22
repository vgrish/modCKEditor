<?php

abstract class modCKEditorPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var modCKEditor $modCKEditor */
    protected $modCKEditor;
    /** @var array $scriptProperties */
    protected $scriptProperties;
    /** @var bool $initEditor */
    public $initEditor = false;

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

        $this->initializeEditor();
    }

    public function initializeEditor()
    {
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', '');
        if ($useEditor AND $whichEditor == 'modckeditor') {
            $this->initEditor = true;
        }

        return $this->initEditor;
    }

    abstract public function run();
}