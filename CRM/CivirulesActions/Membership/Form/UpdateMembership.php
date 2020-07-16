<?php

use CRM_Membershipapplication_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_CivirulesActions_Membership_Form_UpdateMembership extends CRM_CivirulesActions_Form_Form {
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');
    $membershipTypes = CRM_Member_PseudoConstant::membershipType();
    $attributes = ['multiple' => 'multiple', 'class' => 'crm-select2 huge', 'placeholder' => E::ts('Membership Types')];
    $this->add('select', 'membership_type_ids', E::ts('Membership Type to Action On'), $membershipTypes, TRUE, $attributes);

    $options = [
      1 => E::ts('Set'),
      0 => E::ts('Remove'),
    ];
    $this->addRadio('is_override', E::ts('Override Flag'), $options, ['allowClear' => TRUE], NULL, TRUE);

    $options = ['' => E::ts('-- AUTO --')] + CRM_Member_PseudoConstant::membershipStatus(NULL, NULL, 'label');
    $this->add('select', 'membership_status_to', E::ts('Change Membership Status To'), $options);

    $sql = "select id, label from civicrm_custom_field where data_type = 'Date' and is_active = 1 and label like '%end%'";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $endDateFields = [];
    while ($dao->fetch()) {
      $endDateFields[$dao->id] = $dao->label;
    }
    $this->add('select', 'is_course_end_date', E::ts('Set End Date to Course End Date?'), ['' => E::ts('-- DEFAULT --')] + $endDateFields);

    $this->addElement('checkbox', 'is_set_since_date_today', E::ts('Set Since Date to Today (When Action is Triggerd)?'));

    $this->add('select', 'membership_type_tags', E::ts('Tag to Apply'), ['' => E::ts('-- please select --')] + CRM_Core_BAO_Tag::getTags());

    $attributes['placeholder'] = E::ts('Membership Types to Tag');
    $this->add('select', 'tag_membership_type_ids', E::ts('When Membership Type Is One Of'), $membershipTypes, FALSE, $attributes);

    $this->addButtons(array(
      array('type' => 'next', 'name' => E::ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => E::ts('Cancel'))));

    // export form elemenE::ts
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
    foreach ([
      'membership_type_ids', 
      'is_override', 
      'membership_status_to', 
      'membership_type_tags', 
      'is_course_end_date',
      'is_set_since_date_today',
      'tag_membership_type_ids'] as $field
    ) {
      if (isset($data[$field])) {
        $defaultValues[$field] = $data[$field];
      }
    }
    return $defaultValues;
  }

  public function postProcess() {
    $data = [];
    foreach ([
      'membership_type_ids', 
      'is_override', 
      'membership_status_to', 
      'membership_type_tags', 
      'is_course_end_date',
      'is_set_since_date_today',
      'tag_membership_type_ids'] as $field
    ) {
      $data[$field] = CRM_Utils_Array::value($field, $this->_submitValues);
    }
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
