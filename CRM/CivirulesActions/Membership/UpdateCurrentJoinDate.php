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
    $label = ts('Membership Type to Action On: ');
    $label .= $membershipTypes[$params['membership_type_id']];
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
        'status_id' => "New",//fixme: setting
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
        //if (!$joinDate) {
        //  $joinDate = date('Y-m-d');
        //}
        $join_date = $getResult['values'][0]['join_date'];
        if ($join_date == date('Y-m-d')) {
          $join_date = date("Y-m-d", strtotime("yesterday"));
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
        if (!empty($params)) {
          $updateResult = civicrm_api3('Membership', 'create', $params);
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

