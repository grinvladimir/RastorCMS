<?php

class Core_Model_Pictures {

    protected $_params = array(
        'preview' => array(
            'width' => 100,
            'height' => 100
        ),
        'fixedThumbSize' => false,
        'sortable' => false,
        'crop' => true,
        'type' => 'picture',
        'postData' => array(),
        'showDataKey' => ''
    );
    protected $_picturesTranslations = array(
        'view' => 'Смотреть',
        'remove' => 'Удалить',
        'edit' => 'Редактировать',
        'upload' => 'Загрузить изображение',
        'pictureEdit' => 'Редактирование изображения',
        'removeDialogTitle' => 'Удалить запись?',
        'removeDialogContent' => 'Вы действительно хотите удалить запись?',
        'imageDialogTitle' => 'Загрузка изображения',
        'imageCropDialogTitle' => 'Миниатюра изображения',
        'addToGallery' => 'Добавить изображение в галерею',
        'buttonYes' => 'Да',
        'buttonNo' => 'Нет',
        'buttonCancel' => 'Отмена'
    );
    protected $_data = array();

    public function __construct($params = array()) {
        $this->_params = array_merge($this->_params, $params);
    }

    public function setData($array) {
        $this->_data = $array;
    }

    public function getTranslations() {
        $view = Zend_Layout::getMvcInstance()->getView();

        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_picturesTranslations as $key => $value) {
            $this->_picturesTranslations[$key] = $translator->_($value);
        }

        if (count($this->_params['postData'])) {
            foreach ($this->_params['postData'] as $key => $value) {
                $this->_params['postData'][$key] = $translator->_($value);
            }
        }

        return $this->_picturesTranslations;
    }

    public function getJSONObject() {
        $this->getTranslations();

        $result = array_merge($this->_params, array(
            'translation' => $this->_picturesTranslations,
            'uploadUrl' => $this->_getUploadUrl(),
            'cropUrl' => $this->_getCropUrl(),
            'deleteUrl' => $this->_getDeleteUrl(),
            'data' => $this->_data
                ));

        return Zend_Json::encode($result);
    }

    protected function _getUploadUrl() {
        return Rastor_Url::get('admin', array('module' => 'core', 'controller' => 'cmspictures', 'action' => 'upload'));
    }

    protected function _getCropUrl() {
        return Rastor_Url::get('admin', array('module' => 'core', 'controller' => 'cmspictures', 'action' => 'crop'));
    }

    protected function _getDeleteUrl() {
        return Rastor_Url::get('admin', array('module' => 'core', 'controller' => 'cmspictures', 'action' => 'delete'));
    }

    public static function getThumbFileName($value, $ext) {
        $fileName = strtolower(pathinfo($value, PATHINFO_FILENAME));
        return $fileName . '_small.' . $ext;
    }

    public static function makeThumb($filename, $t_w, $t_h, $src_x, $src_y, $src_w, $src_h) {
        $config = Zend_Registry::get('config');
        $baseName = pathinfo($filename, PATHINFO_BASENAME);
        $fullImage = $config->rastor->pictures->uploadPath . $baseName;
        $ext = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'jpg':
                $img = imagecreatefromjpeg($fullImage);
                break;
            case 'gif':
                $img = imagecreatefromgif($fullImage);
                break;
            case 'png':
                $img = imagecreatefrompng($fullImage);
                break;
            case 'bmp':
                $img = imagecreatefromwbmp($fullImage);
                break;
            default:
                return false;
        }

        $thumbFileName = self::getThumbFileName($baseName, 'jpg');

        $newImg = imagecreatetruecolor($t_w, $t_h);
        imagecopyresampled($newImg, $img, 0, 0, $src_x, $src_y, $t_w, $t_h, $src_w, $src_h);

        imagejpeg($newImg, $config->rastor->pictures->uploadPath . $thumbFileName, 100);

        imagedestroy($img);
        imagedestroy($newImg);

        return $thumbFileName;
    }

}