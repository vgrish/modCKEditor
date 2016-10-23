<?php


class modCKEditorBrowserFileUploadProcessor extends modProcessor
{
    /** @var modMediaSource $source */
    public $source;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('file_upload');
    }

    public function getLanguageTopics()
    {
        return array('file');
    }

    public function initialize()
    {
        $this->setDefaultProperties(array(
            'source' => 1,
            'path'   => false,
        ));
        if (!$this->getProperty('path')) {
            return $this->modx->lexicon('file_folder_err_ns');
        }

        return true;
    }

    public function process()
    {
        if (!$this->getSource()) {
            return $this->_failure($this->modx->lexicon('permission_denied'));
        }
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('create')) {
            return $this->_failure($this->modx->lexicon('permission_denied'));
        }

        $path = '';
        foreach (explode('/', rtrim($this->getProperty('path'), '/')) as $dir) {
            $path .= $dir . '/';
            $this->source->createContainer($path, '/');
        }
        $this->source->createContainer($path, '/');
        $this->source->errors = array();

        $success = $this->source->uploadObjectsToContainer($path, $_FILES);
        if (empty($success)) {
            $errors = $this->source->getErrors();

            return $this->_failure($errors, $_FILES);
        }

        return $this->_success('', $_FILES);
    }

    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource()
    {
        $this->modx->loadClass('sources.modMediaSource');
        $this->source = modMediaSource::getDefaultSource($this->modx, $this->getProperty('source'));
        if (empty($this->source) || !$this->source->getWorkingContext()) {
            return false;
        }

        return $this->source;
    }

    public function _success($message = '', array $data = array())
    {
        $response = array(
            'url'      => '',
            'fileName' => '',
            'uploaded' => true,
            'data'     => $data,
            'error'    => array(
                'message' => is_array($message) ? implode('<br>', $message) : $message
            )
        );

        $upload = isset($data['upload']) ? $data['upload'] : $data['file'];
        if (isset($upload['name'])) {
            $response['fileName'] = $upload['name'];
            $response['url'] = $this->source->getBaseUrl() . ltrim($this->getProperty('path'), '/') . $upload['name'];
        }

        $funcNum = $this->getProperty('CKEditorFuncNum');
        if (is_null($funcNum)) {
            return json_encode($response, 1);
        }

        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('{$funcNum}', '{$response['url']}', '');</script>";
    }

    public function _failure($message = '', array $data = array())
    {
        $response = array(
            'url'      => '',
            'fileName' => '',
            'uploaded' => false,
            'data'     => $data,
            'error'    => array(
                'message' => is_array($message) ? implode('<br>', $message) : $message
            )
        );

        return json_encode($response, 1);
    }
}

return 'modCKEditorBrowserFileUploadProcessor';