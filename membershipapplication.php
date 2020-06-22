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
    $oldMemStatusId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $objectId, 'status_id');
    // For any updates where status is going to be New / Awaiting Approval, always override
    // E.g: for online payments, where there is a change from Pending to New
    if ($oldMemStatusId == 5 && $objectRef['status_id'] == 1) {
      $objectRef['is_override'] = 1;
    }
  }
}

function membershipapplication_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Membership' && $op == 'create' && $objectId) {
    // When a mem of status New is created, always set to override.
    if ($objectRef->status_id == 1) {
      // this shouldn't trigger any hooks
      CRM_Core_DAO::setFieldValue('CRM_Member_DAO_Membership', $objectId, 'is_override', 1);
    }
  }
}
