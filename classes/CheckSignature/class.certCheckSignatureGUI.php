<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use srag\DIC\Certificate\DICTrait;

/**
 * GUI-Class certCheckSignatureGUI
 * @author            Martin Studer <ms@studer-raimann.ch>
 * @version           $Id:
 * @ilCtrl_IsCalledBy certCheckSignatureGUI: ilRouterGUI, ilUIPluginRouterGUI
 */
class certCheckSignatureGUI
{
    use DICTrait;
    const CMD_DECRYPT_SIGNATURE = 'decryptSignature';
    const CMD_SHOW_FORM = 'showForm';

    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var ilGlobalTemplateInterface
     */
    protected ilGlobalTemplateInterface $global_tpl;
    /**
     * @var ilCertificatePlugin
     */
    protected $pl;
    /**
     * @var ilCtrl
     */
    protected $ctrl;

    function __construct()
    {
        global $DIC;
        $this->global_tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilCertificatePlugin::getInstance();
        $this->ctrl = $DIC->ctrl();
    }

    /**
     * @return bool
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd(self::CMD_SHOW_FORM);
        if (self::version()->is6()) {
            $this->global_tpl->loadStandardTemplate();
        } else {
        $this->global_tpl->getStandardTemplate();
        }
        switch ($cmd) {
            case self::CMD_SHOW_FORM:
            default:
                $this->showForm();
                break;
            case self::CMD_DECRYPT_SIGNATURE:
                $this->decryptSignature();
                break;
        }
        if (self::version()->is6()) {
            $this->global_tpl->printToStdout();
        } else {
        $this->global_tpl->show();
        }
    }

    public function showForm()
    {

        $form = new certCheckSignatureFormGUI();
        $this->global_tpl->setContent($form->getHTML());
    }

    public function decryptSignature()
    {
        $form = new certCheckSignatureFormGUI();
        if (!$form->checkInput()) {
            $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_FAILURE, $this->pl->txt('decrypt_failed'), true);
        }

        $signature = $form->getInput('signature');
        $decrypted = srCertificateDigitalSignature::decryptSignature($signature);

        if ($decrypted) {
            $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_INFO, $this->pl->txt('decrypt_successful') . '<br/>' . $decrypted, true);
        } else {
            $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_FAILURE, $this->pl->txt('decrypt_failed'), true);
        }
    }
}
