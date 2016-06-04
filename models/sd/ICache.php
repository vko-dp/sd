<?php
/**
 * Created by PhpStorm.
 * User: Varenko Oleg
 * Date: 21.05.2016
 * Time: 11:16
 */
namespace app\models\sd;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use tpmanc\imagick\Imagick;
use app\models\PositionImage;

class ICache extends ActiveRecord {

    const URL_PARAM = 'uri';
    const CACHE_DIR = 'iCache';
    const DEFAULT_EXT = 'jpg';
    
    /** @var ICache  */
    protected static $_instance;

    /** @var array конфиги фоток */
    protected $_config = array(
        'no_photo' => array(
            'sourcePath' => '/' . self::CACHE_DIR . '/no_photo',
            'pathPart' => null
        ),
        PositionImage::I_CACHE_ALIAS_CONFIG => array(
            'allowedSizes'  => array(
                'sq20' => '20x20',
                'sq40' => '40x40',
                'sq60' => '60x60',
                'sq100' => '100x100',
                'sq200' => '200x200',
                'sq300' => '300x300',
                'sq450' => '450x450',
                'gallery' => '1024x800'
            ),
            'sourcePath' => '/' . self::CACHE_DIR . '/position',
            'sourceSize' => ['width' => 1024, 'height' => 800],
            'dbTableSource' => 'PositionImage',
            'pathPart' => null,
            'noPhotoData' => array(
                'id' => 2,
                'name' => 'no_photo',
                'ext' => 'png'
            )
        )
    );

    /**
     * @return ICache
     */
    public static function i() {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $name
     * @param $id
     * @return string
     */
    protected function _getPathPartByConfig($name, $id) {

        if(!is_null($this->_config[$name]['pathPart'])) {
            return $this->_config[$name]['pathPart'];
        }
        if(!isset($this->_config[$name]['dbTableSource'])) {
            return '';
        }
        $pathPart = '';
        switch($this->_config[$name]['dbTableSource']) {
            case 'PositionImage' :
                $pathPart = PositionImage::getPathPart($id);
                break;
        }
        return $pathPart;
    }

    /**
     * @param $name
     * @param $pathPart
     */
    protected function _setPathPart($name, $pathPart) {
        if(isset($this->_config[$name])) {
            $this->_config[$name]['pathPart'] = $pathPart;
        }
    }

    /**
     * @param $id
     * @param $name
     * @param bool|true $source
     * @param null $pathPart
     * @param string $aliasPath
     * @return bool|string
     */
    protected function _getSourcePath($id, $name, $source = true, $pathPart = null, $aliasPath = '@webroot') {
        if(!isset($this->_config[$name]['sourcePath'])) {
            return false;
        }
        $pathPart = !is_null($pathPart) ? $pathPart : $this->_getPathPartByConfig($name, $id);
        $path = Yii::getAlias($aliasPath . $this->_config[$name]['sourcePath'] . '/' . $pathPart . $id . ($source ? '/source' : ''));
        return ($aliasPath == '@web') ? $path : FileHelper::normalizePath($path);
    }

    /**
     * @param $name
     * @return array
     */
    protected function _getSourceSize($name) {
        return isset($this->_config[$name]['sourceSize']) ? $this->_config[$name]['sourceSize'] : array();
    }

    /**
     * @param $name
     * @return array
     */
    protected function _getAllowedSize($name) {
        return isset($this->_config[$name]['allowedSizes']) ? $this->_config[$name]['allowedSizes'] : array();
    }

    /**
     * @param $name
     * @return array
     */
    protected function _getConfig($name) {
        return isset($this->_config[$name]) ? $this->_config[$name] : array();
    }

    /**
     * @return bool
     */
    protected function _checkUrl() {
        return (bool)preg_match("|^\/" . self::CACHE_DIR . "\/[a-z]{3,15}\/[^\&\?\.\,]*[\d]+\_[\d]{1,4}x[\d]{1,4}\.[a-zA-Z]{3,4}$|", trim(Yii::$app->getRequest()->get('uri')));
    }

    /**
     * @param $id
     * @param array $size
     * @param $ext
     * @return string
     */
    protected function _getFileName($id, array $size, $ext) {
        return $id . '_' . implode('x', $size) . '.' . $ext;
    }

    /**
     * разбираем путь переданный в урл
     * @return array
     */
    function _getPathParams() {
        $params = parse_url(trim(Yii::$app->getRequest()->get('uri')));
        $paramsUri =(isset($params['path']) && $params['path']) ? explode('/', trim($params['path'])) : array();
        if(!$paramsUri) {
            return array();
        }
        if(is_null(ArrayHelper::remove($paramsUri, 0))) {
            return array();
        }
        if(ArrayHelper::remove($paramsUri, 1, '') != self::CACHE_DIR) {
            return array();
        }
        if(is_null($configName = ArrayHelper::remove($paramsUri, 2))) {
            return array();
        }
        $return = array(
            'configName' => $configName,
            'fileName' => end($paramsUri),
        );
        if(preg_match("|{$configName}\/(.+)\/[\d]+\/[\d]+\_[\d]{1,4}x[\d]{1,4}\.[a-zA-Z]{3,4}|i", $params['path'], $matches)) {
            $return['pathPart'] = $matches[1] . '/';
        } else {
            $return['pathPart'] = '';
        }

        return $return;
    }

    /**
     * @param $name
     * @return array
     */
    protected function _getThumbAllowedSizes($name) {
        $thumbSizes = array();
        $config = $this->_getConfig($name);
        if(isset($config['allowedSizes'], $config['sourceSize']) && $config['allowedSizes'] && $config['sourceSize']) {
            $sourceSize = implode('x', $config['sourceSize']);
            foreach($config['allowedSizes'] as $v) {
                if($v != $sourceSize) {
                    $thumbSizes[] = $v;
                }
            }
        }
        return $thumbSizes;
    }

    /**
     * @param string $size
     * @return array
     */
    protected function _sizeToArray($size) {
        list($width, $height) = explode('x', $size);
        return array(
            'width' => $width,
            'height' => $height
        );
    }

    /**
     * @param $id
     * @param $name
     * @param $path
     */
    protected function _clearDirectory($id, $name, $path) {

        $ext = self::DEFAULT_EXT;
        $files = FileHelper::findFiles($path);
        if($files) {
            foreach($files as $v) {
                if(file_exists($v)) {
                    $info = pathinfo($v);
                    $ext = isset($info['extension']) ? $info['extension'] : $ext;
                    unlink($v);
                }
            }
            $thumbPath = $this->_getSourcePath($id, $name, false);
            $thumbFileList = FileHelper::findFiles($thumbPath, ['filter' => function($path) use ($id, $ext) {
                $info = pathinfo($path);
                return (isset($info['basename']) && $info['basename']) ? (bool)preg_match("|{$id}\_\d+x\d+\.{$ext}|is", $info['basename']) : false;
            }]);
            if($thumbFileList) {
                foreach($thumbFileList as $v) {
                    if(file_exists($v)) {
                        unlink($v);
                    }
                }
            }

        }
    }

    /**
     * этот метод должен вызываться только при получении изображения
     * @return array
     */
    protected function _parseUrl() {

        $return = array(
            'config' => array(),
            'configName' => false,
            'fileName' => '',
            'pathPart' => '',
            'id' => 0,
            'width' => 0,
            'height' => 0,
            'ext' => ''
        );

        if(!$this->_checkUrl()) {
            return $return;
        }

        $params = $this->_getPathParams();
        if($params) {
            $return['fileName'] = $params['fileName'];
            $return['configName'] = $params['configName'];
            $return['pathPart'] = $params['pathPart'];
            $return['config'] = $this->_getConfig($return['configName']);
            //--- нужно сохранить часть пути файла в конфиг
            $this->_setPathPart($params['configName'], $params['pathPart']);
            //--- разбираем имя файла
            list($part, $return['ext']) = explode('.', $return['fileName']);
            if(!empty($part)) {
                list($return['id'], $size) = explode('_', $part);
                if(!empty($size)) {
                    list($return['width'], $return['height']) = explode('x', $size);
                }
            }
        }

        return $return;
    }

    /**
     * @param $id
     * @param $name
     * @param $ext
     * @return string
     */
    protected function _sourcePath($id, $name, $ext) {

        $path = $this->_getSourcePath($id, $name);
        if(!is_dir($path)) {
            return '';
        }
        $files = FileHelper::findFiles($path, ['filter' => function($path) use ($id, $ext) {
            $info = pathinfo($path);
            return (isset($info['basename']) && $info['basename']) ? (bool)preg_match("|{$id}\_\d+x\d+\.{$ext}|is", $info['basename']) : false;
        }]);
        return isset($files[0]) ? $files[0] : '';
    }

    /**
     * @param $id
     * @param $name
     * @return array|mixed
     */
    protected function _sourceInfo($id, $name) {

        $path = $this->_getSourcePath($id, $name);
        if(!is_dir($path)) {
            return array();
        }
        $files = FileHelper::findFiles($path);
        return isset($files[0]) ? pathinfo($files[0]) : array();
    }

    /**
     * масштабируем по ширине
     * @param $srcWidth
     * @param $srcHeight
     * @param $destWidth
     * @return array
     */
    protected function _getScaleByWidth($srcWidth, $srcHeight, $destWidth) {

        if($destWidth >= $srcWidth) {

            return array(
                'width' => $srcWidth,
                'height' => $srcHeight
            );
        }

        $width = $destWidth;
        $height = round($srcHeight * $destWidth / $srcWidth);
        return array(
            'width' => $width,
            'height' => $height
        );
    }

    /**
     * масштабируем по высоте
     * @param $srcWidth
     * @param $srcHeight
     * @param $destHeight
     * @return array
     */
    protected function _getScaleByHeight($srcWidth, $srcHeight, $destHeight) {

        if($destHeight >= $srcHeight) {

            return array(
                'width' => $srcWidth,
                'height' => $srcHeight
            );
        }

        $height = $destHeight;
        $width = round($srcWidth * $destHeight / $srcHeight);
        return array(
            'width' => $width,
            'height' => $height
        );
    }

    /**
     * Получаем размеры рисунка после масшатабирования
     * По высоте $destWidth = 0
     * По ширине $destHeight = 0
     *
     * @static
     * @param $srcWidth
     * @param $srcHeight
     * @param int $destWidth
     * @param int $destHeight
     * @param bool $clip
     * @return array
     */
    protected function _getScaleSize($srcWidth, $srcHeight, $destWidth = 0, $destHeight = 0, $clip = false) {

        if ($destWidth || $destHeight){

            if(!$destWidth && $destHeight) {

                return $this->_getScaleByHeight($srcWidth, $srcHeight, $destHeight);
            } elseif($destWidth && !$destHeight) {

                return $this->_getScaleByWidth($srcWidth, $srcHeight, $destWidth);
            } else {

                $arrScaleHeight = $this->_getScaleByHeight($srcWidth, $srcHeight, $destHeight);
                $arrScaleWidth = $this->_getScaleByWidth($srcWidth, $srcHeight, $destWidth);

                if($arrScaleHeight['width'] * $arrScaleHeight['height'] >= $arrScaleWidth['width'] * $arrScaleWidth['height']) {

                    $arrMax = $arrScaleHeight;
                    $arrMin = $arrScaleWidth;
                } else {

                    $arrMax = $arrScaleWidth;
                    $arrMin = $arrScaleHeight;
                }

                if($clip) {

                    return $arrMin;
                } else {

                    return $arrMax;
                }
            }
        } else {

            return array(
                'width' => $srcWidth,
                'height' => $srcHeight
            );
        }
    }

    /**
     * @param $width
     * @param $height
     * @param $allowedSizes
     * @return bool
     */
    protected function _isAllowedSize($width, $height, $allowedSizes) {
        return in_array($width . 'x' . $height, $allowedSizes);
    }

    // TODO: сделать возможность добавления подписи для источника
    /**
     * сохраняет изображение-источник
     * @param $id
     * @param $name
     * @param $filePath
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function writeSource($id, $name, $filePath) {

        if(!in_array($name, array_keys($this->_config))) {
            return false;
        }

        //--- нужно чтобы был вызван метод возвращающий правильный путь для источника
        $this->_setPathPart($name, null);

        $sourcePath = $this->_getSourcePath($id, $name);
        if(!$sourcePath) {
            return false;
        }

        if(!FileHelper::createDirectory($sourcePath, 0777, true)) {
            return false;
        }

        $size = $this->_getSourceSize($name);
        if(!$size) {
            return false;
        }

        if(!file_exists($filePath)) {
            return false;
        }

        //--- удаляем старый источник и все превьюшки если они есть
        $this->_clearDirectory($id, $name, $sourcePath);

        $img = Imagick::open($filePath);
        $width = $img->getWidth();
        $height = $img->getHeight();
        $info = pathinfo($filePath);
        if(!isset($info['extension']) || !$info['extension']) {
            return false;
        }
        $ext = strtolower($info['extension']);

        if($width > $size['width'] || $height > $size['height']) {

            $sourcePath .= '/' . $this->_getFileName($id, $size, $ext);
            $img->resize($size['width'], $size['height'])->saveTo($sourcePath);
        } else {

            $size['width'] = $width;
            $size['height'] = $height;
            $sourcePath .= '/' . $this->_getFileName($id, $size, $ext);
            Imagick::open($filePath)->saveTo($sourcePath);
        }

        return true;
    }

    /**
     * возвращает данные для превью фоток
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getImageData() {

        $return = array(
            'path' => '',
            'mimeType' => 'image/jpeg'
        );
        $data = $this->_parseUrl();
        if(isset($data['width'], $data['height']) && $data['config']) {

            $id = $data['id'];
            $name = $data['configName'];
            $ext = $data['ext'];
            $isAllowed = $this->_isAllowedSize($data['width'], $data['height'], $data['config']['allowedSizes']);
            $sourcePath = $this->_sourcePath($id, $name, $ext);

            if(!$isAllowed || !$sourcePath) {

                $noPhotoData = $data['config']['noPhotoData'];
                $id = $noPhotoData['id'];
                $name = $noPhotoData['name'];
                $ext = $noPhotoData['ext'];
                $sourcePath = $this->_sourcePath($id, $name, $ext);
            }

            $path = $this->_getSourcePath($id, $name, false) . '/' . $this->_getFileName($id, array($data['width'], $data['height']), $ext);
            $path = FileHelper::normalizePath($path);
            if(!file_exists($path)) {

                $img = Imagick::open($sourcePath);
                $width = $img->getWidth();
                $height = $img->getHeight();
                $size = $this->_getScaleSize($width, $height, $data['width'], $data['height']);
                $img->thumb($size['width'], $size['height'])->saveTo($path);
            }
            $return = array(
                'path' => $path,
                'mimeType' => FileHelper::getMimeType($path)
            );
        }

        return $return;
    }

    /**
     * возвращает массив урл проиндексироавнный алиасами из конфига
     * если присутствует источник - если нет - заглушки
     * @param $name
     * @param $id
     * @param $pathPart
     * @return array
     */
    public function getUrlData($name, $id, $pathPart) {

        $return = array();
        $config = $this->_getConfig($name);
        $allowedSize = $this->_getAllowedSize($name);
        $this->_setPathPart($name, $pathPart);
        $info = $this->_sourceInfo($id, $name);
        $ext = isset($info['extension']) ? $info['extension'] : 'jpg';
        if($allowedSize) {
            foreach($allowedSize as $alias => $size) {

                $fileName = $this->_getFileName($id, explode('x', $size), $ext);
                $path = $this->_getSourcePath($id, $name, false, $pathPart) . '/' . $fileName;
                $webUrl = $this->_getSourcePath($id, $name, false, $pathPart, '@web') . '/' . $fileName;

                if(!$this->_sourcePath($id, $name, $ext)) {

                    $noPhotoData = $config['noPhotoData'];

                    $fileNameNoPhoto = $this->_getFileName($noPhotoData['id'], explode('x', $size), $noPhotoData['ext']);
                    $pathNoPhoto = $this->_getSourcePath($noPhotoData['id'], $noPhotoData['name'], false, '') . '/' . $fileNameNoPhoto;
                    $webUrlNoPhoto = $this->_getSourcePath($noPhotoData['id'], $noPhotoData['name'], false, '', '@web') . '/' . $fileNameNoPhoto;
                    $isExistsNoPhoto = file_exists($pathNoPhoto);
                }
                $return[$alias] = (isset($isExistsNoPhoto, $webUrlNoPhoto) && $isExistsNoPhoto) ? $webUrlNoPhoto : (file_exists($path) ? $webUrl : urldecode(Url::toRoute(['i-cache/index', 'uri' => $webUrl])));
            }
        }
        return $return;
    }
}