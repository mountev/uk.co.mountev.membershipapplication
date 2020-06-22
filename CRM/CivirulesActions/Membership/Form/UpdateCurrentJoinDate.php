<?php

use CRM_Membershipapplication_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_CivirulesActions_Membership_Form_UpdateCurrentJoinDate extends CRM_CivirulesActions_Form_Form {
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');
    $membershipTypes = CRM_Member_PseudoConstant::membershipType();
    $attributes = ['multiple' => 'multiple', 'class' => 'crm-select2 huge', 'placeholder' => ts('Membership Types')];
    $this->add('select', 'membership_type_ids', ts('Membership Type to Action On'), $membershipTypes, TRUE, $attributes);

    $options = [
      1 => ts('Set'),
      0 => ts('Remove'),
    ];
    $this->addRadio('is_override', ts('Override Flag'), $options, ['allowClear' => TRUE], NULL, TRUE);

    $options = ['' => ts('-- please select --')] + CRM_Member_PseudoConstant::membershipStatus(NULL, NULL, 'label');
    $this->add('select', 'membership_status_to', ts('Change Membership Status To'), $options);

    $this->add('select', 'membership_type_tags', ts('Tag to Apply'), CRM_Core_BAO_Tag::getTags());

    $attributes['placeholder'] = ts('Membership Types to Tag');
    $this->add('select', 'tag_membership_type_ids', ts('Tag Membership Type Condition'), $membershipTypes, FALSE, $attributes);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleAction->action_params);
    if (!empty($data['membership_type_id'])) {
      $defaultValues['membership_type_id'] = $data['membership_type_id'];
    }
    return $defaultValues;
  }

  public function postProcess() {
    $data = [];
    $data['membership_type_id'] = $this->_submitValues['membership_type_id'];
    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
