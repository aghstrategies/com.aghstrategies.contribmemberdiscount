<?php

require_once 'contribmemberdiscount.civix.php';

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function contribmemberdiscount_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    try {
      $pages = civicrm_api3('Setting', 'get', array(
        'sequential' => 1,
        'return' => 'contribmemberdiscount_contribpages',
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.customcivistylesui')));
    }
    if (!empty($pages['values'][0]['contribmemberdiscount_contribpages'])) {
      print_r($pages['values'][0]['contribmemberdiscount_contribpages']); die();
      if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
        if (in_array($form->getVar('_id'), $pages['values'][0]['contribmemberdiscount_contribpages'])) {
          // check if user is logged in if so continue if not display message about discount
          // check if user has proper membership type / status
          // If user is logged in and has proper membership type and status do discount of amount to all price options
          // CRM_Core_Resources::singleton()->addScriptFile('com.aghstrategies.customcivistylesui', 'js/pricesetbuttons.js');
          // CRM_Core_Resources::singleton()->addStyleFile('com.aghstrategies.customcivistylesui', 'css/pricesetbuttons.css');
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function contribmemberdiscount_civicrm_config(&$config) {
  _contribmemberdiscount_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function contribmemberdiscount_civicrm_xmlMenu(&$files) {
  _contribmemberdiscount_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function contribmemberdiscount_civicrm_install() {
  _contribmemberdiscount_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function contribmemberdiscount_civicrm_postInstall() {
  _contribmemberdiscount_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function contribmemberdiscount_civicrm_uninstall() {
  _contribmemberdiscount_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function contribmemberdiscount_civicrm_enable() {
  _contribmemberdiscount_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function contribmemberdiscount_civicrm_disable() {
  _contribmemberdiscount_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function contribmemberdiscount_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contribmemberdiscount_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function contribmemberdiscount_civicrm_managed(&$entities) {
  _contribmemberdiscount_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function contribmemberdiscount_civicrm_caseTypes(&$caseTypes) {
  _contribmemberdiscount_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function contribmemberdiscount_civicrm_angularModules(&$angularModules) {
  _contribmemberdiscount_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function contribmemberdiscount_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _contribmemberdiscount_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function contribmemberdiscount_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function contribmemberdiscount_civicrm_navigationMenu(&$menu) {
  _contribmemberdiscount_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.aghstrategies.contribmemberdiscount')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _contribmemberdiscount_civix_navigationMenu($menu);
} // */
