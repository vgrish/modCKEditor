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

    public function __construct(& $modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = &$modx;
        $this->modCKEditor = &$this->modx->modckeditor;

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