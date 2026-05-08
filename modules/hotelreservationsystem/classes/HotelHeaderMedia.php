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

class HotelHeaderMedia extends ObjectModel
{
    const MEDIA_TYPE_IMAGE = 1;
    const MEDIA_TYPE_VIDEO = 2;

    const NAV_TYPE_DOTS   = 1;
    const NAV_TYPE_ARROWS = 2;
    const NAV_TYPE_BOTH   = 3;

    const ANIM_TYPE_SLIDE = 1;
    const ANIM_TYPE_FADE  = 2;
    const ANIM_TYPE_ZOOM  = 3;
    const ANIM_TYPE_BLUR  = 4;

    public $media_type;
    public $name;
    public $source_type;
    public $position;
    public $tag_line;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'     => 'htl_header_media',
        'primary'   => 'id_header_media',
        'multilang' => true,
        'fields'    => array(
            'media_type'  => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 10),
            'name'        => array('type' => self::TYPE_STRING, 'size' => 512),
            'source_type' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 10),
            'tag_line'    => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 512),
            'position'    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active'      => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add'    => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'    => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->image_dir = _PS_IMG_DIR_.'hotel_header_media/';
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function delete()
    {
        if (!$this->deleteMediaFile()
            || !parent::delete()
            || !$this->cleanPositions($this->media_type)
        ) {
            return false;
        }
        return true;
    }

    public function deleteMediaFile()
    {
        if ($this->source_type === 'url' || !$this->name) {
            return true;
        }
        $filePath = $this->image_dir.$this->name;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return true;
    }

    /**
     * Universal query method
     *
     * @param int|string $mediaType  MEDIA_TYPE_* constant (int) or DB slug ('image'/'video')
     * @param int        $active     1=active only, 0=inactive only, 2=all
     * @param int|null   $idLang     Language for tag_line; null uses context default
     * @param bool       $withAllLangs  true = include tag_lines[id_lang] map (for admin edit forms)
     * @return array  Array of rows; empty array when none found
     */
    public static function getItems($mediaType, $active = 1, $idLang = null, $withAllLangs = false)
    {
        if (is_int($mediaType)) {
            $mediaType = ($mediaType === self::MEDIA_TYPE_VIDEO) ? 'video' : 'image';
        }
        if (!$idLang) {
            $idLang = (int)Context::getContext()->language->id;
        }

        if (!$withAllLangs) {
            $sql = 'SELECT m.*, IFNULL(ml.`tag_line`, \'\') AS `tag_line`
                    FROM `'._DB_PREFIX_.'htl_header_media` m
                    LEFT JOIN `'._DB_PREFIX_.'htl_header_media_lang` ml
                        ON (m.`id_header_media` = ml.`id_header_media`
                            AND ml.`id_lang` = '.(int)$idLang.')
                    WHERE m.`media_type` = \''.pSQL($mediaType).'\'';
            if ($active != 2) {
                $sql .= ' AND m.`active` = '.(int)$active;
            }
            $sql .= ' ORDER BY m.`position` ASC';
            $result = Db::getInstance()->executeS($sql);
            return $result ? $result : array();
        }

        $sql = 'SELECT m.*, ml.`id_lang`, IFNULL(ml.`tag_line`, \'\') AS `lang_tag_line`
                FROM `'._DB_PREFIX_.'htl_header_media` m
                LEFT JOIN `'._DB_PREFIX_.'htl_header_media_lang` ml
                    ON m.`id_header_media` = ml.`id_header_media`
                WHERE m.`media_type` = \''.pSQL($mediaType).'\'';
        if ($active != 2) {
            $sql .= ' AND m.`active` = '.(int)$active;
        }
        $sql .= ' ORDER BY m.`position` ASC, ml.`id_lang` ASC';

        $rows = Db::getInstance()->executeS($sql);
        if (!$rows) {
            return array();
        }

        $itemsMap = array();
        foreach ($rows as $row) {
            $id = (int)$row['id_header_media'];
            if (!isset($itemsMap[$id])) {
                $itemsMap[$id]              = $row;
                $itemsMap[$id]['tag_line']  = '';
                $itemsMap[$id]['tag_lines'] = array();
                unset($itemsMap[$id]['id_lang'], $itemsMap[$id]['lang_tag_line']);
            }
            if (isset($row['id_lang'])) {
                $langId  = (int)$row['id_lang'];
                $tagLine = $row['lang_tag_line'];
                $itemsMap[$id]['tag_lines'][$langId] = $tagLine;
                if ($langId === (int)$idLang) {
                    $itemsMap[$id]['tag_line'] = $tagLine;
                }
            }
        }

        return array_values($itemsMap);
    }

    public function getHigherPosition($mediaType = null)
    {
        if (!$mediaType) {
            $mediaType = $this->media_type;
        }
        $position = Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_header_media`
            WHERE `media_type` = \''.pSQL($mediaType).'\''
        );
        return (is_numeric($position) ? (int)$position : -1) + 1;
    }

    public function cleanPositions($mediaType = null)
    {
        if (!$mediaType) {
            $mediaType = $this->media_type;
        }
        $items = Db::getInstance()->executeS(
            'SELECT `id_header_media` FROM `'._DB_PREFIX_.'htl_header_media`
            WHERE `media_type` = \''.pSQL($mediaType).'\' ORDER BY `position` ASC'
        );
        if (!$items) {
            return true;
        }
        foreach ($items as $i => $item) {
            Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'htl_header_media`
                SET `position` = '.(int)$i.'
                WHERE `id_header_media` = '.(int)$item['id_header_media']
            );
        }
        return true;
    }

    public static function createMediaDirectory()
    {
        $dir = _PS_IMG_DIR_.'hotel_header_media/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!file_exists($dir.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
            @copy(_PS_IMG_DIR_.'index.php', $dir.'index.php');
        }
        return true;
    }

}
