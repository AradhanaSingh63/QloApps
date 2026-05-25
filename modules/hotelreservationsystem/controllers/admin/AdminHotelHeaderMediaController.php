<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminHotelHeaderMediaController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table      = 'htl_header_media';
        $this->className  = 'HotelHeaderMedia';
        $this->bootstrap  = true;
        $this->identifier = 'id_header_media';
        $this->lang       = true;
        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text'    => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected images? This cannot be undone.'),
                'icon'    => 'icon-trash',
            ),
        );
    }

    public function initContent()
    {
        if (!$this->ajax) {
            $this->display = 'view';
        }
        parent::initContent();
    }

    public function renderView()
    {
        $mediaType = (int)Tools::getValue(
            'WK_HEADER_MEDIA_TYPE',
            (int)(Configuration::get('WK_HEADER_MEDIA_TYPE') ?: HotelHeaderMedia::MEDIA_TYPE_IMAGE)
        );
        $languages     = Language::getLanguages(false);
        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');

        $imageItems = HotelHeaderMedia::getItems(HotelHeaderMedia::MEDIA_TYPE_IMAGE, 2, $defaultLangId, true);
        foreach ($imageItems as &$item) {
            $item['tag_lines_json'] = json_encode((object)$item['tag_lines']);
        }
        unset($item);

        $videoRows = HotelHeaderMedia::getItems(HotelHeaderMedia::MEDIA_TYPE_VIDEO, 2);
        $videoItem = $videoRows ? $videoRows[0] : null;
        $videoTagLine  = array();
        $videoMimeType = 'video/mp4';
        if ($videoItem) {
            $rows = Db::getInstance()->executeS(
                'SELECT `id_lang`, `tag_line` FROM `'._DB_PREFIX_.'htl_header_media_lang`
                WHERE `id_header_media` = '.(int)$videoItem['id_header_media']
            );
            foreach ($rows as $row) {
                $videoTagLine[$row['id_lang']] = $row['tag_line'];
            }
            if ($videoItem['source_type'] === 'upload') {
                $ext = strtolower(pathinfo($videoItem['name'], PATHINFO_EXTENSION));
                $mimeMap = array('mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg');
                $videoMimeType = isset($mimeMap[$ext]) ? $mimeMap[$ext] : 'video/mp4';
            }
        }
        foreach ($languages as $lang) {
            if (!isset($videoTagLine[$lang['id_lang']])) {
                $videoTagLine[$lang['id_lang']] = '';
            }
        }

        Media::addJsDef(array(
            'wkHmCurrentIndex'  => self::$currentIndex,
            'wkHmToken'         => $this->token,
            'wkHmMediaType'     => $mediaType,
            'wkHmMediaTypeImage' => HotelHeaderMedia::MEDIA_TYPE_IMAGE,
            'wkHmMediaTypeVideo' => HotelHeaderMedia::MEDIA_TYPE_VIDEO,
            'wkHmMaxUpload'     => Tools::getMaxUploadSize(),
            'wkHmDefaultLangId' => $defaultLangId,
            'wkHmI18n'          => array(
                'noFileSelected' => $this->l('Please select at least one image file.'),
            ),
        ));

        $this->tpl_view_vars = array(
            'mediaType'     => $mediaType,
            'imageItems'    => $imageItems,
            'videoItem'     => $videoItem,
            'videoTagLine'  => $videoTagLine,
            'videoMimeType' => $videoMimeType,
            'config'        => array(
                'WK_HEADER_MEDIA_TYPE'       => $mediaType,
                'WK_HEADER_SLIDER_NAV_TYPE'  => (int)Tools::getValue('WK_HEADER_SLIDER_NAV_TYPE', (int)(Configuration::get('WK_HEADER_SLIDER_NAV_TYPE') ?: HotelHeaderMedia::NAV_TYPE_DOTS)),
                'WK_HEADER_SLIDER_AUTO_PLAY' => (int)Tools::getValue('WK_HEADER_SLIDER_AUTO_PLAY', (int)Configuration::get('WK_HEADER_SLIDER_AUTO_PLAY')),
                'WK_HEADER_SLIDER_INTERVAL'  => (int)Tools::getValue('WK_HEADER_SLIDER_INTERVAL', (int)Configuration::get('WK_HEADER_SLIDER_INTERVAL') ?: 5000),
                'WK_HEADER_SLIDER_ANIM_TYPE' => (int)Tools::getValue('WK_HEADER_SLIDER_ANIM_TYPE', (int)(Configuration::get('WK_HEADER_SLIDER_ANIM_TYPE') ?: HotelHeaderMedia::ANIM_TYPE_SLIDE)),
            ),
            'languages'     => $languages,
            'defaultLangId' => $defaultLangId,
            'imgBaseUrl'    => $this->context->link->getMediaLink(_PS_IMG_.'hotel_header_media/'),
            'maxUpload'     => Tools::formatBytes(Tools::getMaxUploadSize()),
        );

        return parent::renderView();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHeaderMedia')) {
            $this->processSaveSettings();
        }
        parent::postProcess();
    }

    protected function processSaveSettings()
    {
        $mediaType = (int)Tools::getValue('WK_HEADER_MEDIA_TYPE', HotelHeaderMedia::MEDIA_TYPE_IMAGE);
        $interval  = Tools::getValue('WK_HEADER_SLIDER_INTERVAL', 5000);

        if (!in_array($mediaType, array(HotelHeaderMedia::MEDIA_TYPE_IMAGE, HotelHeaderMedia::MEDIA_TYPE_VIDEO))) {
            $this->errors[] = $this->l('Invalid media type selected.');
            return;
        }
        $videoRows       = HotelHeaderMedia::getItems(HotelHeaderMedia::MEDIA_TYPE_VIDEO, 2);
        $hasNewVideoFile = isset($_FILES['header_video_file']) && !empty($_FILES['header_video_file']['size']);
        $hasNewVideoUrl  = (Tools::getValue('source_type', '') === 'url' && trim(Tools::getValue('video_url', '')) !== '');
        if ($mediaType === HotelHeaderMedia::MEDIA_TYPE_VIDEO && !$videoRows && !$hasNewVideoFile && !$hasNewVideoUrl) {
            $this->errors[] = $this->l('Please upload or link a video before switching the header to Video mode.');
            return;
        }
        if ($mediaType === HotelHeaderMedia::MEDIA_TYPE_IMAGE && !HotelHeaderMedia::getItems(HotelHeaderMedia::MEDIA_TYPE_IMAGE)) {
            $this->errors[] = $this->l('Please add at least one active image before switching the header to Image mode.');
            return;
        }
        if ((string)$interval !== '' && (!Validate::isUnsignedInt($interval) || (int)$interval < 500)) {
            $this->errors[] = $this->l('Auto Slide Interval must be at least 500 milliseconds.');
            return;
        }

        $navType = (int)Tools::getValue('WK_HEADER_SLIDER_NAV_TYPE', HotelHeaderMedia::NAV_TYPE_DOTS);
        if (!in_array($navType, array(HotelHeaderMedia::NAV_TYPE_DOTS, HotelHeaderMedia::NAV_TYPE_ARROWS, HotelHeaderMedia::NAV_TYPE_BOTH))) {
            $navType = HotelHeaderMedia::NAV_TYPE_DOTS;
        }
        $animType = (int)Tools::getValue('WK_HEADER_SLIDER_ANIM_TYPE', HotelHeaderMedia::ANIM_TYPE_SLIDE);
        if (!in_array($animType, array(HotelHeaderMedia::ANIM_TYPE_SLIDE, HotelHeaderMedia::ANIM_TYPE_FADE, HotelHeaderMedia::ANIM_TYPE_ZOOM, HotelHeaderMedia::ANIM_TYPE_BLUR))) {
            $animType = HotelHeaderMedia::ANIM_TYPE_SLIDE;
        }

        Configuration::updateValue('WK_HEADER_MEDIA_TYPE',      $mediaType);
        Configuration::updateValue('WK_HEADER_SLIDER_NAV_TYPE',  $navType);
        Configuration::updateValue('WK_HEADER_SLIDER_AUTO_PLAY', (int)Tools::getValue('WK_HEADER_SLIDER_AUTO_PLAY', 1));
        Configuration::updateValue('WK_HEADER_SLIDER_INTERVAL',  (int)Tools::getValue('WK_HEADER_SLIDER_INTERVAL', 5000));
        Configuration::updateValue('WK_HEADER_SLIDER_ANIM_TYPE', $animType);

        if ($mediaType === HotelHeaderMedia::MEDIA_TYPE_VIDEO) {
            $this->processSaveVideo();
        }

        if (!count($this->errors)) {
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        }
    }

    protected function processSaveVideo()
    {
        $sourceType  = Tools::getValue('source_type', 'upload');
        $videoUrl    = trim(Tools::getValue('video_url', ''));
        $file        = isset($_FILES['header_video_file']) ? $_FILES['header_video_file'] : null;
        $hasNewFile  = ($file && $file['size']);
        $hasNewUrl   = ($sourceType === 'url' && $videoUrl !== '');

        if ($hasNewUrl && !Validate::isAbsoluteUrl($videoUrl)) {
            $this->errors[] = $this->l('Please enter a valid video URL.');
            return;
        }
        if ($hasNewFile) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array('mp4', 'webm'))) {
                $this->errors[] = $this->l('Only .mp4 and .webm video formats are allowed.');
                return;
            }
            if ($file['size'] > Tools::getMaxUploadSize()) {
                $this->errors[] = $this->l('Video file exceeds the maximum allowed upload size.');
                return;
            }
        }

        $tagLineByLang = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(
                Tools::getValue('vid_tag_line_'.$lang['id_lang'], '')
            );
        }

        $videoRows = HotelHeaderMedia::getItems(HotelHeaderMedia::MEDIA_TYPE_VIDEO, 2);
        $videoRow  = $videoRows ? $videoRows[0] : null;
        if ($videoRow) {
            $objMedia = new HotelHeaderMedia((int)$videoRow['id_header_media']);
        } elseif ($hasNewFile || $hasNewUrl) {
            $objMedia              = new HotelHeaderMedia();
            $objMedia->media_type  = 'video';
            $objMedia->position    = 0;
            $objMedia->active      = 1;
        } else {
            return;
        }

        $objMedia->tag_line = $tagLineByLang;

        if ($hasNewUrl) {
            $objMedia->deleteMediaFile();
            $objMedia->source_type = 'url';
            $objMedia->name        = $videoUrl;
        } elseif ($hasNewFile) {
            $objMedia->deleteMediaFile();
            HotelHeaderMedia::createMediaDirectory();
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            do {
                $uniqueName = uniqid().'.'.$ext;
            } while (file_exists(_PS_IMG_DIR_.'hotel_header_media/'.$uniqueName));
            move_uploaded_file($file['tmp_name'], _PS_IMG_DIR_.'hotel_header_media/'.$uniqueName);
            $objMedia->source_type = 'upload';
            $objMedia->name        = $uniqueName;
        }

        $objMedia->save();
    }

    public function ajaxProcessUploadImage()
    {
        $response = array('errors' => array(), 'success' => false);
        $file = isset($_FILES['header_image_file']) ? $_FILES['header_image_file'] : null;

        if (!$file || !$file['size']) {
            $response['errors'][] = $this->l('No file received.');
            $this->ajaxDie(json_encode($response));
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize())) {
            $response['errors'][] = $error;
            $this->ajaxDie(json_encode($response));
        }

        HotelHeaderMedia::createMediaDirectory();
        $ext    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $isGif  = ($ext === 'gif');
        $outExt = $isGif ? 'gif' : 'jpg';
        do {
            $uniqueName = uniqid().'.'.$outExt;
        } while (file_exists(_PS_IMG_DIR_.'hotel_header_media/'.$uniqueName));

        $destPath = _PS_IMG_DIR_.'hotel_header_media/'.$uniqueName;
        $saved    = $isGif
            ? (bool)move_uploaded_file($file['tmp_name'], $destPath)
            : (bool)ImageManager::resize($file['tmp_name'], $destPath);

        if (!$saved) {
            $response['errors'][] = $this->l('Failed to save image file.');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineByLang = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
        }

        $objMedia              = new HotelHeaderMedia();
        $objMedia->media_type  = 'image'; // DB slug — ObjectModel stores the string, not the int constant
        $objMedia->source_type = 'upload';
        $objMedia->name        = $uniqueName;
        $objMedia->position    = $objMedia->getHigherPosition('image');
        $objMedia->active      = (int)(bool)Tools::getValue('active', 1);
        $objMedia->tag_line    = $tagLineByLang;

        if (!$objMedia->save()) {
            @unlink(_PS_IMG_DIR_.'hotel_header_media/'.$uniqueName);
            $response['errors'][] = $this->l('Failed to save image record.');
            $this->ajaxDie(json_encode($response));
        }

        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $response['success']        = true;
        $response['id']             = (int)$objMedia->id;
        $response['active']         = (int)$objMedia->active;
        $response['imgUrl']         = $this->context->link->getMediaLink(_PS_IMG_.'hotel_header_media/'.$uniqueName);
        $response['tag_line']       = isset($tagLineByLang[$defaultLangId]) ? $tagLineByLang[$defaultLangId] : '';
        $response['tag_lines_json'] = json_encode((object)$tagLineByLang);
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessEditImage()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_media');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objMedia = new HotelHeaderMedia($id);
        if (!Validate::isLoadedObject($objMedia) || $objMedia->media_type !== 'image') {
            $response['errors'][] = $this->l('Image not found.');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineByLang = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
        }

        $activeVal = Tools::getValue('active');
        if ($activeVal !== false) {
            $objMedia->active = (int)(bool)$activeVal;
        }

        $objMedia->tag_line = $tagLineByLang;
        if (!$objMedia->save()) {
            $response['errors'][] = $this->l('Failed to update image.');
            $this->ajaxDie(json_encode($response));
        }

        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $response['success']        = true;
        $response['active']         = (int)$objMedia->active;
        $response['confirmations']  = $this->l('Image updated successfully.');
        $response['tag_line']       = isset($tagLineByLang[$defaultLangId]) ? $tagLineByLang[$defaultLangId] : '';
        $response['tag_lines_json'] = json_encode((object)$tagLineByLang);
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteMedia()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_media');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objMedia = new HotelHeaderMedia($id);
        if (!Validate::isLoadedObject($objMedia)) {
            $response['errors'][] = $this->l('Media item not found.');
            $this->ajaxDie(json_encode($response));
        }

        if (!$objMedia->delete()) {
            $response['errors'][] = $this->l('Unable to delete media item.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success'] = true;
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessToggleImageActive()
    {
        $response = array('errors' => array(), 'success' => false);
        $id     = (int)Tools::getValue('id_header_media');
        $active = (int)(bool)Tools::getValue('active');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objMedia = new HotelHeaderMedia($id);
        if (!Validate::isLoadedObject($objMedia) || $objMedia->media_type !== 'image') {
            $response['errors'][] = $this->l('Media item not found.');
            $this->ajaxDie(json_encode($response));
        }

        $objMedia->active = $active;
        if (!$objMedia->save()) {
            $response['errors'][] = $this->l('Unable to update active status.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success']       = true;
        $response['confirmations'] = $this->l('The status has been successfully updated.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessSaveImagePositions()
    {
        $ids = Tools::getValue('image_ids', array());
        if (!is_array($ids)) {
            $this->ajaxDie(json_encode(array('success' => false)));
        }
        foreach ($ids as $position => $id) {
            Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'htl_header_media`
                SET `position` = '.(int)$position.'
                WHERE `id_header_media` = '.(int)$id.' AND `media_type` = \'image\''
            );
        }
        $this->ajaxDie(json_encode(array(
            'success'       => true,
            'confirmations' => $this->l('The selected images have successfully been moved.'),
        )));
    }

    public function ajaxProcessBulkUpdateTagLines()
    {
        $response      = array('errors' => array(), 'success' => false);
        $languages     = Language::getLanguages(false);
        $tagLineByLang = array();
        foreach ($languages as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
        }

        $ids = Db::getInstance()->executeS(
            'SELECT `id_header_media` FROM `'._DB_PREFIX_.'htl_header_media`
            WHERE `media_type` = \'image\''
        );
        if (!$ids) {
            $response['errors'][] = $this->l('No images found.');
            $this->ajaxDie(json_encode($response));
        }

        foreach ($ids as $row) {
            $objMedia           = new HotelHeaderMedia((int)$row['id_header_media']);
            $objMedia->tag_line = $tagLineByLang;
            $objMedia->save();
        }

        $defaultLangId              = (int)Configuration::get('PS_LANG_DEFAULT');
        $response['success']        = true;
        $response['tag_line']       = isset($tagLineByLang[$defaultLangId]) ? $tagLineByLang[$defaultLangId] : '';
        $response['tag_lines_json'] = json_encode((object)$tagLineByLang);
        $response['confirmations']  = $this->l('Tag line updated for all images.');
        $this->ajaxDie(json_encode($response));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('tablednd');
        $this->addJS(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelHeaderMediaAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}
