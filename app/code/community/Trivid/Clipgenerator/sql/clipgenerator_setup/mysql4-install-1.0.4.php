<?php
/**
 * Install Script
 *
 * PHP version 5
 *
 * LICENSE: is available through the world-wide-web at the following URI:
 * http://www.clipgenerator.com/static/public/legal.php If you did not receive a copy of
 * the Clipgenerator - End User License Agreement and are unable to obtain it through the web, please
 * send a note to info@trivid.com so we can mail you a copy immediately.
 *
 * @package    Trivid
 * @author     Trivid GmbH <author@example.com>
 * @copyright  2013 Trivid GmbH
 * @license    http://www.clipgenerator.com/static/public/legal.php Clipgenerator - End User License Agreement
 * @version    1.0.4
 * @since      File available since Release 1.0.4
 */
/**
 * Creates all necessary product attributes.
 */
$installer = $this;
$installer->startSetup();
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_title', array(
	'group' => 'Clipgenerator',
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'Video Titel',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_description', array(
	'group' => 'Clipgenerator',
	'input' => 'textarea',
	'type' => 'varchar',
	'label' => 'Beschreibung',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_keywords', array(
	'group' => 'Clipgenerator',
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'Video Keywords',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_logo', array(
	'group' => 'Clipgenerator',
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'Video Logo URL',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_show', array(
	'group' => 'Clipgenerator',
	'input' => 'boolean',
	'type' => 'int',
	'default' => 0,
	'label' => 'Video ausgeben',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_design', array(
	'group' => 'Clipgenerator',
	'input' => 'select',
	'type' => 'varchar',
	'label' => 'Design',
	'source' => 'trivid_clipgenerator_model_source_designs',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_images_select', array(
	'group' => 'Clipgenerator',
	'type' => 'text',
	'input' => 'text',
	'backend' => '',
	'input_renderer' => 'clipgenerator/catalog_product_helper_form_images', //definition of renderer
	'label' => 'Bildauswahl',
	'class' => '',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_song', array(
	'group' => 'Clipgenerator',
	'input' => 'select',
	'type' => 'varchar',
	'label' => 'Musik',
	'source' => 'trivid_clipgenerator_model_source_musics',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_music_select', array(
	'group' => 'Clipgenerator',
	'type' => 'varchar',
	'backend' => '',
	'input_renderer' => 'clipgenerator/catalog_product_helper_form_music', //definition of renderer
	'label' => 'erweiterte Auswahl',
	'class' => '',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'clipgenerator_video_id', array(
	'group' => 'Clipgenerator',
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'Video ID',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => TRUE,
	'required' => FALSE,
	'user_defined' => TRUE,
	'searchable' => FALSE,
	'filterable' => FALSE,
	'comparable' => FALSE,
	'visible_on_front' => FALSE,
	'unique' => FALSE,
	'apply_to' => 'simple,configurable,bundle,grouped',
	'is_configurable' => FALSE,
));
$installer->endSetup();