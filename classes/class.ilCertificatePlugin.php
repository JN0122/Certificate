<?php
require_once __DIR__ . '/../vendor/autoload.php';
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\DIC\Certificate\DICTrait;
use srag\Plugins\Certificate\Menu\Menu;
/**
 * Certificate Plugin
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 * @version $Id$
 */
class ilCertificatePlugin extends ilUserInterfaceHookPlugin
{
    use DICTrait;

    const CTYPE = "Services";
    /** @var string */
    const CNAME = "UIComponent";
    /** @var string */
    const SLOT_ID = "uihk";

    const PLUGIN_ID = 'cert';
    const PLUGIN_NAME = 'Certificate';
    /**
     * Name of class that can implement hooks
     */
    const CLASS_NAME_HOOKS = 'srCertificateCustomHooks';
    /**
     * Default path for hook class (can be changed in plugin config)
     */
    const DEFAULT_PATH_HOOK_CLASS = './Customizing/global/certificate/';
    /**
     * Default formats (can be changed in plugin config)
     */
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d, H:i';
    const DEFAULT_DISK_SPACE_WARNING = 10;
    /**
     * Default permission settings
     */
    const DEFAULT_ROLES_ADMINISTRATE_CERTIFICATES = '["2"]';
    const DEFAULT_ROLES_ADMINISTRATE_CERTIFICATE_TYPES = '["2"]';
    /**
     * @var srCertificateHooks
     */
    protected $hooks;
    /**
     * @var ilCertificatePlugin
     */
    protected static $instance;
    /**
     * @var ilPluginAdmin
     */
    protected $ilPluginAdmin;
    /**
     * @var ilTree
     */
    protected $tree;
    /**
     * @var ilDB
     */
    protected ilDBInterface $db;

    /**
     * @return ilCertificatePlugin
     */
    public static function getInstance()
    {
	global $DIC;
        if (self::$instance instanceof self) {
            return self::$instance;
        }

	 /** @var ilComponentRepository $component_repository */
        $component_repository = $DIC['component.repository'];
        /** @var ilComponentFactory $component_factory */
        $component_factory = $DIC['component.factory'];

        $plugin_info = $component_repository->getComponentByTypeAndName(
            self::CTYPE,
            self::CNAME
        )->getPluginSlotById(self::SLOT_ID)->getPluginByName(self::PLUGIN_NAME);

        self::$instance = $component_factory->getPlugin($plugin_info->getId());

        return self::$instance;
    }

    protected function init():void
    {
        parent::init();
        if (isset($_GET['ulx'])) {
            $this->updateLanguages();
        }
    }

    /**
     * @return string
     */
    public function getPluginName():string
    {
        return self::PLUGIN_NAME;
    }

    public function __construct(ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id)
    {
        parent::__construct($db, $component_repository, $id);
        global $DIC;

        $this->ilPluginAdmin = $DIC["ilPluginAdmin"];
        $this->tree = $DIC->repositoryTree();
        //$this->db = $DIC->database();
	$this->db = $db;
    }

    /**
     * Get a config value
     * @param string $name
     * @return string|null
     */
    public function config($name)
    {
        return ilCertificateConfig::getX($name);
    }

    /**
     * Get Hooks object
     * @return srCertificateHooks
     */
    public function getHooks()
    {
        if (is_null($this->hooks)) {
            $class_name = self::CLASS_NAME_HOOKS;
            $path = ilCertificateConfig::getX('path_hook_class');
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
            $file = $path . "class.{$class_name}.php";
            if (is_file($file)) {
                require_once $file;
                $object = new $class_name($this);
            } else {
                $object = new srCertificateHooks($this);
            }
            $this->hooks = $object;
        }

        return $this->hooks;
    }

    /**
     * Check if course is a "template course"
     * This method returns true if the given ref-ID is a children of a category defined in the plugin options
     * @param int $ref_id Ref-ID of the object to check
     * @return bool
     */
    public function isCourseTemplate($ref_id)
    {
        if (ilCertificateConfig::getX('course_templates') && ilCertificateConfig::getX('course_templates_ref_ids')) {
            // Course templates enabled -> check if given ref_id is defined as template
            $ref_ids = explode(',', ilCertificateConfig::getX('course_templates_ref_ids'));
            $parent_ref_id = $this->tree->getParentId($ref_id);

            return in_array($parent_ref_id, $ref_ids);
        }

        return false;
    }

    /**
     * Check if preconditions are given to use this plugin
     * @return bool
     */
    public function checkPreConditions()
    {
        return !file_exists(__DIR__ . "/../../../../EventHandling/EventHook/CertificateEvents");
    }

    /**
     * Don't activate plugin if preconditions are not given
     * @return bool
     */
    protected function beforeUpdate():bool
    {
        if (!$this->checkPreConditions()) {
            ilUtil::sendFailure("Please uninstall and remove legacy 'CertificateEvents' plugin from server, because it is incompatible / give conflict - It's now integrated in 'Certificate' plugin", true);

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public static function getPluginIconImage()
    {
        return ilUtil::getImagePath('icon_cert.svg');
    }

    /**
     * @return bool
     */
    protected function beforeUninstall():bool
    {
        $this->db->dropTable(ilCertificateConfig::TABLE_NAME, false);
        $this->db->dropTable(srCertificateType::TABLE_NAME, false);
        $this->db->dropTable(srCertificateDefinition::TABLE_NAME, false);
        $this->db->dropTable(srCertificatePlaceholder::TABLE_NAME, false);
        $this->db->dropTable(srCertificatePlaceholderValue::TABLE_NAME, false);
        $this->db->dropTable(srCertificate::TABLE_NAME, false);
        $this->db->dropTable(srCertificateTypeSetting::TABLE_NAME, false);
        $this->db->dropTable(srCertificateDefinitionSetting::TABLE_NAME, false);
        $this->db->dropTable(srCertificateSignatureDef::TABLE_NAME, false);
        $this->db->dropTable(srCertificateCustomDefinitionSetting::TABLE_NAME, false);
        $this->db->dropTable(srCertificateCustomTypeSetting::TABLE_NAME, false);

        ilUtil::delDir(CLIENT_DATA_DIR . '/cert_signatures');
        ilUtil::delDir(CLIENT_DATA_DIR . '/cert_templates');
        ilUtil::delDir(CLIENT_DATA_DIR . '/cert_data');
        ilUtil::delDir(CLIENT_DATA_DIR . '/cert_keys');

        return true;
    }


    /**
     * @param string $component
     * @param string $event
     * @param array  $parameters
     */
    public function handleEvent($component, $event, $parameters) {
        // Generate certificate if course is completed
        switch ($component) {
            case 'Modules/Course':
                $course = NULL;
                if (isset($parameters['object']) && $parameters['object'] instanceof ilObjCourse) {
                    $course = $parameters['object'];
                } elseif (isset($parameters['obj_id'])) {
                    $course = new ilObjCourse(array_pop(ilObject::_getAllReferences($parameters['obj_id'])));
                }
                if (!$course) {
                    return;
                }
                $handler = new srCertificateEventsCourseHandler($course);
                $handler->handle($event, $parameters);
                break;
            case 'Certificate/srCertificate':
                $certificate = $parameters['object'];
                $handler = new srCertificateEventsCertificateHandler($certificate);
                $handler->handle($event, $parameters);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider
    {
        return new Menu(self::dic()->dic(), $this);
    }

    public function getPrefix(): string
    {
        $lh = $this->getLanguageHandler();
        return $lh->getPrefix();
    }
}
