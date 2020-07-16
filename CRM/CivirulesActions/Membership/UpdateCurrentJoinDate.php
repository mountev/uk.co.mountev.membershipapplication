<?php

class CRM_CivirulesActions_Membership_UpdateCurrentJoinDate extends CRM_Civirules_Action {

  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/action/membership/updatecurrentjoindate', 'rule_action_id='.$ruleActionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $params = $this->getActionParameters();
    $membershipTypes = CRM_Member_PseudoConstant::membershipType();
    $membershipStatuses = CRM_Member_PseudoConstant::membershipStatus();
    $labels = [];
    if (!empty($params['membership_type_ids'])) {
      $label  = ts('Membership Type to Action On') . ': ';
      $mtIds  = [];
      foreach ($params['membership_type_ids'] as $memTypeId) {
        $mtIds[] = $membershipTypes[$memTypeId];
      }
      $labels[] = $label . implode(', ', $mtIds);;
    }
    if (isset($params['is_override'])) {
      $label  = ts('Override') . ': ';
      $label .= $params['is_override'] ? ts('Yes') : ts('No');
      $labels[] = $label;

      $mst = FALSE;
      if ($params['is_override'] && !empty($params['membership_status_to'])) {
        $mst = $membershipStatuses[$params['membership_status_to']];
      } else if (empty($params['is_override'])) {
        $mst = ts('AUTO');
      }
      if ($mst) {
        $label = ts('Change Membership Status To') . ': ';
        $labels[] = $label . $mst;
      }
    }
    if (!empty($params['is_course_end_date'])) {
      $label  = ts('Set End Date to Course End Date') . ': ' . ts('Yes');
      $labels[] = $label;
    }
    if (!empty($params['is_set_since_date_today'])) {
      $label  = ts('Set Since Date to Today (When Action is Triggerd)?') . ': ' . ts('Yes');
      $labels[] = $label;
    }
    if (!empty($params['membership_type_tags']) || !empty($params['tag_membership_type_ids'])) {
      $tags   = CRM_Core_BAO_Tag::getTags();
      $label  = ts('Tags to Apply') . ': ';
      $label .= $tags[$params['membership_type_tags']];
      $label .= ' <-> ' . ts('If Membership Type is One Of') . ': ';
      $mtIds  = [];
      if (!empty($params['tag_membership_type_ids'])) {
        foreach ($params['tag_membership_type_ids'] as $memTypeId) {
          $mtIds[] = $membershipTypes[$memTypeId];
        }
      }
      $labels[] = $label . implode(', ', $mtIds);;
    }
    $label = implode('<br/>', $labels);
    return $label;
  }

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $actionParams = $this->getActionParameters();
    $contactId = $triggerData->getContactId();

    foreach ($actionParams['membership_type_ids'] as $memTypeId) {
      $getResult = civicrm_api3('Membership', 'get', [
        'sequential' => 1,
        'contact_id' => $contactId,
        'membership_type_id' => $memTypeId,
        'status_id' => "New",
      ]);
      if (!empty($getResult['id'])) {
        // dates based on today
        $memTypeDates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($memTypeId);

        // Convert all dates to 'Y-m-d' format.
        foreach (['join_date', 'start_date', 'end_date'] as $dateParam) {
          if (!empty($memTypeDates[$dateParam])) {
            $$dateParam = CRM_Utils_Date::processDate($memTypeDates[$dateParam], NULL, FALSE, 'Y-m-d');
          }
        }
        if (!empty($actionParams['is_set_since_date_today'])) {
          $join_date = date('Y-m-d');
        }
        $params = [];
        if (empty($actionParams['is_override'])) {
          $params = [
            'membership_type_id' => $memTypeId,
            'id'            => $getResult['id'],
            'contact_id'    => $contactId,
            'join_date'     => $join_date,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'is_override'   => 0,
            'skipStatusCal' => 0,
          ];
        } else if ($actionParams['membership_status_to']) {
          $params = [
            'membership_type_id' => $memTypeId,
            'id'            => $getResult['id'],
            'contact_id'    => $contactId,
            'is_override'   => 1,
            'skipStatusCal' => 1,
            'status_id'     => $actionParams['membership_status_to'],
          ];
        }
        if (!empty($params) && !empty($actionParams['is_course_end_date'])) {
          $courseResult = civicrm_api3('CustomValue', 'get', [
            'sequential' => 1,
            'return' => ["custom_{$actionParams['is_course_end_date']}"],
            'entity_id' => $contactId,
          ]);
          if (!empty($courseResult['values'][0]['latest'])) {
            $params['end_date'] = $courseResult['values'][0]['latest'];
          }
        }
        if (!empty($params)) {
          CRM_Core_Error::debug_var('civirule membership update params', $params);
          $updateResult = civicrm_api3('Membership', 'create', $params);
          CRM_Core_Error::debug_var('civirule membership update result', $updateResult);
        }

        if (in_array($memTypeId, $actionParams['tag_membership_type_ids']) && 
          !empty($actionParams['membership_type_tags'])
        ) {
          $params = [
            'contact_id' => $contactId,
            'tag_id' => $actionParams['membership_type_tags'],
          ];
          $result = civicrm_api3('EntityTag', 'create', $params);
        }
      }
    }
  }

}

