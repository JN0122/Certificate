<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use srag\DIC\Certificate\DICTrait;

/**
 * GUI-Class srCertificateTypeGUI
 * @author            Stefan Wanzenried <sw@studer-raimann.ch>
 * @version           $Id:
 * @ilCtrl_isCalledBy srCertificateTypeGUI: ilRouterGUI, ilUIPluginRouterGUI
 */
class srCertificateTypeGUI
{

    use DICTrait;

    const PLUGIN_CLASS_NAME = ilCertificatePlugin::class;

    const CMD_ADD_CUSTOM_SETTING = 'addCustomSetting';
    const CMD_ADD_PLACEHOLDER = 'addPlaceholder';
    const CMD_ADD_SIGNATURE = 'addSignature';
    const CMD_ADD_TYPE = 'addType';
    const CMD_CONFIRM_DELETE_CUSTOM_SETTING = 'confirmDeleteCustomSetting';
    const CMD_CONFIRM_DELETE_PLACEHOLDER = 'confirmDeletePlaceholder';
    const CMD_CONFIRM_DELETE_SIGNATURE = 'confirmDeleteSignature';
    const CMD_COPY_TYPE = 'copyType';
    const CMD_CREATE_PLACEHOLDER = 'createPlaceholder';
    const CMD_CREATE_SIGNATURE = 'createSignature';
    const CMD_DELETE_CUSTOM_SETTING = 'deleteCustomSetting';
    const CMD_DELETE_PLACEHOLDER = 'deletePlaceholder';
    const CMD_DELETE_SIGNATURE = 'deleteSignature';
    const CMD_DOWNLOAD_DEFAULT_TEMPLATE = 'downloadDefaultTemplate';
    const CMD_DOWNLOAD_SIGNATURE = 'downloadSignature';
    const CMD_DOWNLOAD_TEMPLATE = 'downloadTemplate';
    const CMD_EDIT_CUSTOM_SETTING = 'editCustomSetting';
    const CMD_EDIT_PLACEHOLDER = 'editPlaceholder';
    const CMD_EDIT_SETTING = 'editSetting';
    const CMD_EDIT_SIGNATURE = 'editSignature';
    const CMD_EDIT_TEMPLATE = 'editTemplate';
    const CMD_EDIT_TYPE = 'editType';
    const CMD_SAVE_CUSTOM_SETTING = 'saveCustomSetting';
    const CMD_SAVE_TYPE = 'saveType';
    const CMD_SHOW_PLACEHOLDERS = 'showPlaceholders';
    const CMD_SHOW_SETTINGS = 'showSettings';
    const CMD_SHOW_SIGNATURES = 'showSignatures';
    const CMD_SHOW_TYPES = 'showTypes';
    const CMD_UPDATE_PLACEHOLDER = 'updatePlaceholder';
    const CMD_UPDATE_SETTING = 'updateSetting';
    const CMD_UPDATE_SIGNATURE = 'updateSignature';
    const CMD_UPDATE_TEMPLATE = 'updateTemplate';
    const CMD_VIEW = 'view';

    const TAB_GENERAL = 'general';
    const TAB_PLACEHOLDERS = 'placeholders';
    const TAB_SETTINGS = 'settings';
    const TAB_SIGNATURES = 'signatures';
    const TAB_TEMPLATE = 'template';

    /**
     * @var srCertificateDefinitionFormGUI
     */
    protected $form;
    /**
     * @var srCertificateType
     */
    protected $type;

    public function __construct()
    {
        $this->type = (isset($_GET['type_id'])) ? srCertificateType::find((int) $_GET['type_id']) : null;
    }

    public function executeCommand()
    {
        self::dic()->mainTemplate()->addJavaScript(self::plugin()->getPluginObject()->getStyleSheetLocation('uihk_certificate.js'));
        self::dic()->mainTemplate()->setTitleIcon(ilCertificatePlugin::getPluginIconImage());
        if (!$this->checkPermission()) {
            ilUtil::sendFailure(self::plugin()->translate('msg_no_permission'), true);
            if (self::version()->is6()) {
                self::dic()->ctrl()->redirectByClass(ilDashboardGUI::class);
            } else {
            self::dic()->ctrl()->redirectByClass(ilPersonalDesktopGUI::class);
            }
        }

        self::dic()->mainMenu()->setActive('none');

        $cmd = self::dic()->ctrl()->getCmd();
        $next_class = self::dic()->ctrl()->getNextClass($this);

        if (!in_array($cmd, array(self::CMD_ADD_TYPE, ''))) {
            self::dic()->ctrl()->saveParameter($this, 'type_id');
            self::dic()->ctrl()->saveParameter($this, 'signature_id');
        }
        if (self::version()->is6()) {
            self::dic()->mainTemplate()->loadStandardTemplate();
        } else {
        self::dic()->mainTemplate()->getStandardTemplate();
        }
        switch ($next_class) {
            case '':
                switch ($cmd) {
                    case self::CMD_SHOW_TYPES:
                        $this->showTypes();
                        break;
                    case self::CMD_EDIT_TYPE:
                        $this->editType();
                        $this->setTabs(self::TAB_GENERAL);
                        break;
                    case self::CMD_COPY_TYPE:
                        $this->copyType();
                        $this->setTabs(self::TAB_GENERAL);
                        break;
                    case self::CMD_ADD_TYPE:
                        $this->addType();
                        $this->setTabs(self::TAB_GENERAL);
                        break;
                    case self::CMD_SAVE_TYPE:
                        $this->saveType();
                        $this->setTabs(self::TAB_GENERAL);
                        break;
                    case self::CMD_EDIT_TEMPLATE:
                        $this->editTemplate();
                        $this->setTabs(self::TAB_TEMPLATE);
                        break;
                    case self::CMD_UPDATE_TEMPLATE:
                        $this->updateTemplate();
                        $this->setTabs(self::TAB_TEMPLATE);
                        break;
                    case self::CMD_DOWNLOAD_DEFAULT_TEMPLATE:
                        $this->downloadDefaultTemplate();
                        $this->setTabs(self::TAB_TEMPLATE);
                        break;
                    case self::CMD_DOWNLOAD_TEMPLATE:
                        $this->downloadTemplate();
                        $this->setTabs(self::TAB_TEMPLATE);
                        break;
                    case self::CMD_SHOW_SETTINGS:
                        $this->showSettings();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_EDIT_SETTING:
                        $this->editSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_UPDATE_SETTING:
                        $this->updateSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_ADD_CUSTOM_SETTING:
                        $this->addCustomSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_EDIT_CUSTOM_SETTING:
                        $this->editCustomSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_CONFIRM_DELETE_CUSTOM_SETTING:
                        $this->confirmDeleteCustomSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_DELETE_CUSTOM_SETTING:
                        $this->deleteCustomSetting();
                        break;
                    case self::CMD_SAVE_CUSTOM_SETTING:
                        $this->saveCustomSetting();
                        $this->setTabs(self::TAB_SETTINGS);
                        break;
                    case self::CMD_SHOW_PLACEHOLDERS:
                        $this->showPlaceholders();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_ADD_PLACEHOLDER:
                        $this->addPlaceholder();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_EDIT_PLACEHOLDER:
                        $this->editPlaceholder();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_UPDATE_PLACEHOLDER:
                        $this->updatePlaceholder();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_CREATE_PLACEHOLDER:
                        $this->createPlaceholder();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_DELETE_PLACEHOLDER:
                        $this->deletePlaceholder();
                        break;
                    case self::CMD_CONFIRM_DELETE_PLACEHOLDER:
                        $this->confirmDeletePlaceholder();
                        $this->setTabs(self::TAB_PLACEHOLDERS);
                        break;
                    case self::CMD_SHOW_SIGNATURES:
                        $this->showSignatures();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_ADD_SIGNATURE:
                        $this->addSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_EDIT_SIGNATURE:
                        $this->editSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_CREATE_SIGNATURE:
                        $this->createSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_UPDATE_SIGNATURE:
                        $this->updateSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_CONFIRM_DELETE_SIGNATURE:
                        $this->confirmDeleteSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_DELETE_SIGNATURE:
                        $this->deleteSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case self::CMD_DOWNLOAD_SIGNATURE:
                        $this->downloadSignature();
                        $this->setTabs(self::TAB_SIGNATURES);
                        break;
                    case '':
                        $this->showTypes();
                        break;
                }
                break;
        }
        if (self::version()->is6()) {
            self::dic()->mainTemplate()->printToStdout();
        } else {
        self::dic()->mainTemplate()->show();
        }
    }

    /**
     * Add tabs to GUI
     * @param string $active_tab_id ID of activated tab
     */
    protected function setTabs($active_tab_id = self::TAB_GENERAL)
    {
        self::dic()->tabs()->addTab(self::TAB_GENERAL, self::plugin()->translate(self::TAB_GENERAL),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_TYPE));
        if ($this->type) {
            self::dic()->tabs()->addTab(self::TAB_TEMPLATE, self::plugin()->translate(self::TAB_TEMPLATE),
                self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_TEMPLATE));
            self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate(self::TAB_SETTINGS),
                self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW_SETTINGS));
            self::dic()->tabs()->addTab(self::TAB_PLACEHOLDERS, self::plugin()->translate(self::TAB_PLACEHOLDERS),
                self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW_PLACEHOLDERS));
            self::dic()->tabs()->addTab(self::TAB_SIGNATURES, self::plugin()->translate(self::TAB_SIGNATURES),
                self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW_SIGNATURES));
            self::dic()->mainTemplate()->setTitle($this->type->getTitle());
            self::dic()->mainTemplate()->setDescription($this->type->getDescription());
        }
        self::dic()->tabs()->activateTab($active_tab_id);
        self::dic()->tabs()->setBackTarget(self::plugin()->translate('back_to_overview'),
            self::dic()->ctrl()->getLinkTarget($this));
    }

    /**
     * Show existing certificate types in table
     */
    public function showTypes()
    {
        self::dic()->mainTemplate()->setTitle(self::plugin()->translate('manage_cert_types'));
        $table = new srCertificateTypeTableGUI($this, self::CMD_SHOW_TYPES);
        self::dic()->mainTemplate()->setContent($table->getHTML());
    }

    /**
     * Show form for creating a type
     */
    public function addType()
    {
        $form = new srCertificateTypeFormGUI($this, new srCertificateType());
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Show form for editing a type (General)
     */
    public function editType()
    {
        $form = new srCertificateTypeFormGUI($this, $this->type);
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * create a copy of a type
     */
    public function copyType()
    {
        $new_type = new srCertificateType();
        $new_type->cloneType($this->type);
        ilUtil::sendSuccess(self::plugin()->translate('msg_type_copied'), true);
        ilUtil::sendInfo(self::plugin()->translate('msg_type_copied_info'), true);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_TYPES);
    }

    /**
     * Show form for editing template settings of a type
     */
    public function editTemplate()
    {
        $form = new srCertificateTypeTemplateFormGUI($this, $this->type);
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Update template related stuff
     */
    public function updateTemplate()
    {
        $form = new srCertificateTypeTemplateFormGUI($this, $this->type);
        if ($form->saveObject()) {
            ilUtil::sendSuccess(self::plugin()->translate('msg_type_saved'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_EDIT_TEMPLATE);
        } else {
            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }

    /**
     * Download default template
     */
    public function downloadDefaultTemplate()
    {
        ilFileDelivery::deliverFileLegacy(self::plugin()->getPluginObject()->getDirectory() . '/resources/template.jrxml',
            'template.jrxml');
    }

    /**
     * Download template file
     */
    public function downloadTemplate()
    {
        if (is_file($this->type->getCertificateTemplatesPath(true))) {
            $filename = srCertificateTemplateTypeFactory::getById($this->type->getTemplateTypeId())->getTemplateFilename();
            ilFileDelivery::deliverFileLegacy($this->type->getCertificateTemplatesPath(true), $filename);
        }
        $this->editTemplate();
    }

    /**
     * Show table with settings
     */
    public function showSettings()
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption(self::plugin()->translate('add_new_custom_setting'), false);
        $button->setUrl(self::dic()->ctrl()->getLinkTargetByClass(srCertificateTypeGUI::class,
            self::CMD_ADD_CUSTOM_SETTING));
        self::dic()->toolbar()->addButtonInstance($button);
        $table = new srCertificateTypeSettingsTableGUI($this, self::CMD_SHOW_SETTINGS, $this->type);
        $table_custom_settings = new srCertificateTypeCustomSettingsTableGUI($this, self::CMD_SHOW_SETTINGS,
            $this->type);
        $spacer = '<div style="height: 30px;"></div>';
        self::dic()->mainTemplate()->setContent($table->getHTML() . $spacer . $table_custom_settings->getHTML());
    }

    public function confirmDeleteCustomSetting()
    {
        /** @var srCertificateCustomTypeSetting $setting */
        $setting = srCertificateCustomTypeSetting::findOrFail((int) $_GET['custom_setting_id']);
        $gui = new ilConfirmationGUI();
        $gui->setFormAction(self::dic()->ctrl()->getFormAction($this));
        $gui->setHeaderText(self::plugin()->translate('info_delete_custom_setting'));
        $gui->addItem('custom_setting_id', $setting->getId(), $setting->getLabel(self::dic()->user()->getLanguage()));
        $gui->setConfirm(self::plugin()->translate('confirm'), self::CMD_DELETE_CUSTOM_SETTING);
        $gui->setCancel(self::plugin()->translate('cancel'), self::CMD_SHOW_SETTINGS);
        self::dic()->mainTemplate()->setContent($gui->getHTML());
    }

    public function deleteCustomSetting()
    {
        $setting = srCertificateCustomTypeSetting::findOrFail((int) $_POST['custom_setting_id']);
        $setting->delete();
        ilUtil::sendSuccess(self::plugin()->translate('msg_success_custom_setting_deleted'), true);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SETTINGS);
    }

    public function confirmDeletePlaceholder()
    {
        /** @var srCertificatePlaceholder $placeholder */
        $placeholder = srCertificatePlaceholder::find((int) $_GET['placeholder_id']);
        $gui = new ilConfirmationGUI();
        $gui->setFormAction(self::dic()->ctrl()->getFormAction($this));
        $gui->setHeaderText(self::plugin()->translate('info_delete_custom_placeholder'));
        $gui->addItem('placeholder_id', $placeholder->getId(),
            $placeholder->getLabel(self::dic()->user()->getLanguage()));
        $gui->setConfirm(self::plugin()->translate('confirm'), self::CMD_DELETE_PLACEHOLDER);
        $gui->setCancel(self::plugin()->translate('cancel'), self::CMD_SHOW_PLACEHOLDERS);
        self::dic()->mainTemplate()->setContent($gui->getHTML());
    }

    public function deletePlaceholder()
    {
        $placeholder = srCertificatePlaceholder::findOrFail((int) $_POST['placeholder_id']);
        $placeholder->delete();
        ilUtil::sendSuccess(self::plugin()->translate('msg_success_custom_placeholder_deleted'), true);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_PLACEHOLDERS);
    }

    /**
     * Show form for editing settings of a type
     */
    public function editSetting()
    {
        try {
            $form = new srCertificateTypeSettingFormGUI($this, $this->type, $_REQUEST['identifier']);
            self::dic()->mainTemplate()->setContent($form->getHTML());
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SETTINGS);
        }
    }

    /**
     * Update settings
     */
    public function updateSetting()
    {
        try {
            $form = new srCertificateTypeSettingFormGUI($this, $this->type, $_REQUEST['identifier']);
            if ($form->saveObject()) {
                ilUtil::sendSuccess(self::plugin()->translate('msg_setting_saved'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SETTINGS);
            } else {
                self::dic()->mainTemplate()->setContent($form->getHTML());
            }
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SETTINGS);
        }
    }

    /**
     * @return string
     */
    public function addCustomSetting()
    {
        $form = new srCertificateCustomTypeSettingFormGUI($this, new srCertificateCustomTypeSetting());
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * @return string
     */
    public function editCustomSetting()
    {
        $form = new srCertificateCustomTypeSettingFormGUI($this,
            srCertificateCustomTypeSetting::find((int) $_GET['custom_setting_id']));
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Create/Update a custom setting
     */
    public function saveCustomSetting()
    {
        if (isset($_POST['custom_setting_id']) && $_POST['custom_setting_id']) {
            $setting = srCertificateCustomTypeSetting::find((int) $_POST['custom_setting_id']);
        } else {
            $setting = new srCertificateCustomTypeSetting();
        }

        $form = new srCertificateCustomTypeSettingFormGUI($this, $setting);
        if ($form->saveObject()) {
            ilUtil::sendSuccess(self::plugin()->translate('msg_setting_saved'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SETTINGS);
        } else {
            $form->setValuesByPost();
            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }

    /**
     * Show table with available placeholders for this type
     */
    public function showPlaceholders()
    {
        $table1 = new srCertificateTypeStandardPlaceholdersTableGUI($this, self::CMD_SHOW_PLACEHOLDERS);
        $table2 = new srCertificateTypePlaceholdersTableGUI($this, self::CMD_SHOW_PLACEHOLDERS, $this->type);
        $spacer = '<div style="height: 30px;"></div>';
        self::dic()->mainTemplate()->setContent($table1->getHTML() . $spacer . $table2->getHTML());
        ilUtil::sendInfo(self::plugin()->translate('msg_placeholder_format_info', '',
            [srCertificatePlaceholder::PLACEHOLDER_START_SYMBOL, srCertificatePlaceholder::PLACEHOLDER_END_SYMBOL]));
    }

    /**
     * Add a new placeholder
     */
    public function addPlaceholder()
    {
        $placeholder = new srCertificatePlaceholder();
        $placeholder->setCertificateType($this->type);
        $form = new srCertificateTypePlaceholderFormGUI($this, $placeholder);
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Show form for editing a placeholder
     */
    public function editPlaceholder()
    {
        try {
            $placeholder = srCertificatePlaceholder::find($_REQUEST['placeholder_id']);
            if ($placeholder === null) {
                throw new ilException("Placeholder with ID " . $_REQUEST['placeholder_id'] . " not found");
            }
            $form = new srCertificateTypePlaceholderFormGUI($this, $placeholder);
            self::dic()->mainTemplate()->setContent($form->getHTML());
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_PLACEHOLDERS);
        }
    }

    /**
     * Create a new placeholder
     */
    public function createPlaceholder()
    {
        $placeholder = new srCertificatePlaceholder();
        $placeholder->setCertificateType($this->type);
        $form = new srCertificateTypePlaceholderFormGUI($this, $placeholder);
        if ($form->saveObject()) {
            ilUtil::sendSuccess(self::plugin()->translate('msg_placeholder_saved'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_PLACEHOLDERS);
        } else {
            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }

    /**
     * Update placeholder
     */
    public function updatePlaceholder()
    {
        try {
            $placeholder = srCertificatePlaceholder::find($_REQUEST['placeholder_id']);
            if ($placeholder === null) {
                throw new srCertificateException("Placeholder with ID " . $_REQUEST['placeholder_id'] . " not found");
            }
            $form = new srCertificateTypePlaceholderFormGUI($this, $placeholder);
            if ($form->saveObject()) {
                ilUtil::sendSuccess(self::plugin()->translate('msg_placeholder_saved'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_SHOW_PLACEHOLDERS);
            } else {
                self::dic()->mainTemplate()->setContent($form->getHTML());
            }
        } catch (ilException $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_PLACEHOLDERS);
        }
    }

    /**
     * Show form for editing singatures
     */
    public function showSignatures()
    {
        $table = new srCertificateTypeSignaturesTableGUI($this, self::CMD_SHOW_SIGNATURES, $this->type);
        self::dic()->mainTemplate()->setContent($table->getHTML());
    }

    /**
     * Add a new placeholder
     */
    public function addSignature()
    {
        $signature = new srCertificateSignature();
        $signature->setCertificateType($this->type);
        $form = new srCertificateTypeSignatureFormGUI($this, $signature, $this->type);
        self::dic()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Create a new signature
     */
    public function createSignature()
    {
        $signature = new srCertificateSignature();
        $signature->setCertificateType($this->type);
        $form = new srCertificateTypeSignatureFormGUI($this, $signature, $this->type);
        if ($form->saveObject()) {
            ilUtil::sendSuccess(self::plugin()->translate('msg_signature_saved'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SIGNATURES);
        } else {
            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }

    /**
     *
     */
    public function editSignature()
    {
        try {
            $signature = srCertificateSignature::find((int) $_GET['signature_id']);
            if ($signature === null) {
                throw new ilException("Signature with ID " . (int) $_GET['signature_id'] . " not found");
            }
            $form = new srCertificateTypeSignatureFormGUI($this, $signature, $this->type);
            self::dic()->mainTemplate()->setContent($form->getHTML());
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SIGNATURES);
        }
    }

    /**
     * Update signature related stuff
     */
    public function updateSignature()
    {
        try {
            $signature = srCertificateSignature::find($_GET['signature_id']);
            if ($signature === null) {
                throw new srCertificateException("Signature with ID " . $_GET['signature_id'] . " not found");
            }
            $form = new srCertificateTypeSignatureFormGUI($this, $signature, $this->type);
            if ($form->saveObject()) {
                ilUtil::sendSuccess(self::plugin()->translate('msg_signature_saved'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SIGNATURES);
            } else {
                self::dic()->mainTemplate()->setContent($form->getHTML());
            }
        } catch (ilException $e) {
            ilUtil::sendFailure($e->getMessage(), true);
            self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SIGNATURES);
        }
    }

    /**
     *
     */
    public function confirmDeleteSignature()
    {
        $signature = srCertificateSignature::find($_GET['signature_id']);
        $item_html = $signature->getFirstName() . " " . $signature->getLastName() . '<br>';
        self::dic()->tabs()->clearTargets();
        self::dic()->tabs()->setBackTarget(self::plugin()->translate('common_back'),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_VIEW));
        ilUtil::sendQuestion(self::plugin()->translate('signatures_confirm_delete'));

        $toolbar = new ilToolbarGUI();
        self::dic()->ctrl()->saveParameter($this, 'signature_id');
        $button = ilLinkButton::getInstance();
        $button->setCaption(self::plugin()->translate('confirm'), false);
        $button->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_DELETE_SIGNATURE));
        self::dic()->toolbar()->addButtonInstance($button);
        $button = ilLinkButton::getInstance();
        $button->setCaption(self::plugin()->translate('cancel'), false);
        $button->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW_SIGNATURES));
        self::dic()->toolbar()->addButtonInstance($button);

        self::dic()->mainTemplate()->setContent($item_html . '</br>' . $toolbar->getHTML());
    }

    /**
     *
     */
    public function deleteSignature()
    {
        $signature = srCertificateSignature::find($_GET['signature_id']);
        $signature->delete();
        ilUtil::sendSuccess(self::plugin()->translate('msg_delete_signature_success'), true);
        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_SIGNATURES);
    }

    public function downloadSignature()
    {
        $signature = srCertificateSignature::find($_GET['signature_id']);
        $signature->download();
    }

    /**
     * Create or update a type
     */
    public function saveType()
    {
        $type = ($this->type === null) ? new srCertificateType() : $this->type;
        $form = new srCertificateTypeFormGUI($this, $type);
        if ($form->saveObject()) {
            ilUtil::sendSuccess(self::plugin()->translate('msg_type_saved'), true);
            self::dic()->ctrl()->setParameter($this, 'type_id', $type->getId());
            self::dic()->ctrl()->redirect($this, self::CMD_EDIT_TYPE);
        } else {
            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }

    /**
     * Check permissions
     */
    public function checkPermission()
    {
        $allowed_roles = ilCertificateConfig::getX('roles_administrate_certificate_types');

        return self::dic()->rbacreview()->isAssignedToAtLeastOneGivenRole(self::dic()->user()->getId(),
            json_decode($allowed_roles, true));
    }
}
