<?php

require_once 'membershipapplication.civix.php';
use CRM_Membershipapplication_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function membershipapplication_civicrm_config(&$config) {
  _membershipapplication_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function membershipapplication_civicrm_xmlMenu(&$files) {
  _membershipapplication_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function membershipapplication_civicrm_install() {
  _membershipapplication_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function membershipapplication_civicrm_postInstall() {
  _membershipapplication_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function membershipapplication_civicrm_uninstall() {
  _membershipapplication_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function membershipapplication_civicrm_enable() {
  _membershipapplication_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function membershipapplication_civicrm_disable() {
  _membershipapplication_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function membershipapplication_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipapplication_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function membershipapplication_civicrm_managed(&$entities) {
  _membershipapplication_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function membershipapplication_civicrm_caseTypes(&$caseTypes) {
  _membershipapplication_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function membershipapplication_civicrm_angularModules(&$angularModules) {
  _membershipapplication_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function membershipapplication_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipapplication_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function membershipapplication_civicrm_entityTypes(&$entityTypes) {
  _membershipapplication_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function membershipapplication_civicrm_themes(&$themes) {
  _membershipapplication_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function membershipapplication_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function membershipapplication_civicrm_navigationMenu(&$menu) {
  _membershipapplication_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _membershipapplication_civix_navigationMenu($menu);
} // */

function membershipapplication_civicrm_pre($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Membership' && $op == 'edit' && $objectId) {
    $membershipStatuses = CRM_Member_PseudoConstant::membershipStatus();
    $membershipStatuses = array_flip($membershipStatuses);
    $oldMemStatusId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $objectId, 'status_id');
    // For any updates where status is going to be New / Awaiting Approval, always override
    // E.g: for online payments, where there is a change from Pending to New
    if ($oldMemStatusId == $membershipStatuses['Pending'] && $objectRef['status_id'] == $membershipStatuses['New']) {
      $objectRef['is_override'] = 1;
    } else if ($oldMemStatusId == $membershipStatuses['New'] && $objectRef['status_id'] == $membershipStatuses['New']) {
      $path = CRM_Utils_System::currentPath();
      if ($path == 'civicrm/contact/view/contribution') {
        // If new to new change is due to related contribution update
        // This happens when backend contribution is updated from pending to completed, the related 
        // membership gets updated to new using dao->save() (no hooks called) and then actual save thru hook.
        $objectRef['is_override'] = 1;
      }
    }
  }
}

function membershipapplication_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Membership' && $op == 'create' && $objectId) {
    $membershipStatuses = CRM_Member_PseudoConstant::membershipStatus();
    $membershipStatuses = array_flip($membershipStatuses);
    // When a mem of status New is created, always set to override.
    if ($objectRef->status_id == $membershipStatuses['New']) {
      // this shouldn't trigger any hooks
      CRM_Core_DAO::setFieldValue('CRM_Member_DAO_Membership', $objectId, 'is_override', 1);
    }
  }
}

function membershipapplication_civicrm_alterCalculatedMembershipStatus(&$membershipDetails, $arguments, $membership) {
  // name based list
  $membershipStatuses = CRM_Member_PseudoConstant::membershipStatus();
  $membershipStatuses = array_flip($membershipStatuses);
  if (empty($membership['id']) && empty($membership['is_override'])) {
    // If a new membership is created (may be from backend) - irrespective of dates, 
    // always try to set it to New. Override would automatically apply through post hook.
    $membershipDetails['id'] = $membershipStatuses['New'];
    $membershipDetails['name'] = 'New';
  } else if (!empty($membership['id']) &&
    empty($membership['is_override']) &&
    !empty($membershipDetails) &&
    empty(\Civi::$statics[E::SHORT_NAME]['status_calculated'][$membership['id']])
  ) {
    // If it's a membership update where override is not set
    if ($membershipDetails['name'] != 'New' &&
      ($membership['status_id'] == $membershipStatuses['Pending'])
    ) {
      // If membership is going to get a new status from Pending,
      // Set it to New, so it goes through approval workflow.
      $membershipDetails['id']   = $membershipStatuses['New'];
      $membershipDetails['name'] = 'New';
      // set override flag so it stays "Awaiting Approval / New", unless approved.
      //$membership['is_override'] = 1;
      //CRM_Core_DAO::setFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'is_override', 1);
    } else if ($membershipDetails['name'] == 'New' &&
      ($membership['status_id'] != $membershipStatuses['Pending'])
    ) {
      // It's a membership update/renew without override (Approval workflow).
      //
      // If status was calculated as New,
      // - because join date matched with today. Due to start and end event config of NEW status rule
      // - or some other reason - we don't care
      // SET it to current instead.
      //
      // NOTE: $membershipDetails remains empty for online signup because it gets created as New 
      //       but pending without since date. And then updates to completed. And then hardcodedly
      //       changed to New by bao contribution file. Which is fine for us.
      if (!empty($membershipStatuses['Current'])) {
        $membershipDetails['id'] = $membershipStatuses['Current'];
        $membershipDetails['name'] = 'Current';
      }
    } else if ($membershipDetails['name'] == 'New' &&
      ($membership['status_id'] == $membershipStatuses['Pending'])
    ) {
      // record it in static var, so we don't change it to current if hook is called again in the same
      // request. 
      // Test case: create a membership with pending contribution from backend and then complete the payment.
      \Civi::$statics[E::SHORT_NAME]['status_calculated'][$membership['id']] = 1;
    }
  }
}
