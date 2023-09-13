<?php

/**
 * Table class srCertificateTypeTableGUI
 * @author            Stefan Wanzenried <sw@studer-raimann.ch>
 * @version           $Id:
 **/
class srCertificateTypeTableGUI extends ilTable2GUI
{

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;
    /**
     * @var ilCertificatePlugin
     */
    protected $pl;
    /**
     * @var array
     */
    protected $columns = array(
        'title',
        'description',
        'languages',
        'template_type_id',
        'roles',
        'available_objects',
    );

    public function __construct($a_parent_obj, $a_parent_cmd)
    {
        global $DIC;
        $this->setId('cert_type_table');
        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->pl = ilCertificatePlugin::getInstance();
        $this->ctrl = $DIC->ctrl();
        $this->toolbar = $DIC->toolbar();
        $this->setRowTemplate('tpl.type_row.html', $this->pl->getDirectory());
        $this->initColumns();
        $this->addColumn($this->pl->txt('actions'));
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
        $button = ilLinkButton::getInstance();
        $button->setCaption($this->pl->txt('add_new_type'), false);
        $button->setUrl($this->ctrl->getLinkTargetByClass(srCertificateTypeGUI::class,
            srCertificateTypeGUI::CMD_ADD_TYPE));
        $this->toolbar->addButtonInstance($button);
        $data = srCertificateType::getArray();
        $this->setData($data);
    }

    /**
     * @param array $a_set
     */
    public function fillRow($a_set):void
    {
        $this->tpl->setVariable('TITLE', $a_set['title']);
        $this->tpl->setVariable('DESCRIPTION', $a_set['description']);
        $this->tpl->setVariable('LANGUAGES', implode(', ', $a_set['languages']));
        $template_type = srCertificateTemplateTypeFactory::getById((int) $a_set['template_type_id']);
        $this->tpl->setVariable('TEMPLATE_TYPE_ID', $template_type->getTitle());
        $this->tpl->setVariable('ROLES', is_array($a_set['roles']) ? implode(',', $a_set['roles']) : '');
        $this->tpl->setVariable('AVAILABLE_OBJECTS', implode(',', $a_set['available_objects']));
        $this->tpl->setVariable('ACTIONS', $this->buildActionMenu($a_set)->getHTML());
    }

    /**
     * Build action menu
     * @param array $a_set
     * @return ilAdvancedSelectionListGUI
     */
    protected function buildActionMenu(array $a_set)
    {
        $alist = new ilAdvancedSelectionListGUI();
        $alist->setId($a_set['id']);
        $alist->setListTitle($this->pl->txt('actions'));
        $this->ctrl->setParameterByClass(srCertificateTypeGUI::class, 'type_id', $a_set['id']);
        $alist->addItem($this->lng->txt('edit'), 'edit',
            $this->ctrl->getLinkTargetByClass(srCertificateTypeGUI::class, srCertificateTypeGUI::CMD_EDIT_TYPE));
        $alist->addItem($this->lng->txt('copy'), 'copy',
            $this->ctrl->getLinkTargetByClass(srCertificateTypeGUI::class, srCertificateTypeGUI::CMD_COPY_TYPE));
        return $alist;
    }

    /**
     * Add columns
     */
    protected function initColumns()
    {
        foreach ($this->columns as $column) {
            $this->addColumn($this->pl->txt($column), $column);
        }
    }
}
