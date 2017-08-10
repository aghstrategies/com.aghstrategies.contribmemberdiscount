<?php

require_once 'contribmemberdiscount.civix.php';

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function contribmemberdiscount_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    if (contribmemberdiscount_isQualifyingPage($form->getVar('_id'))) {
      // $memInfo = contribmemberdiscount_getQualifyingMemInfo();
      $contactId = $form->getContactID();
      // IF the contact is logged in
      if (!empty($contactId)) {
        // TODO make membership types and statuses into variables so we can print them in messages
        if (contribmemberdiscount_isQualifyingMember($contactId)) {
          CRM_Core_Session::setStatus(ts('You are logged in as a member who qualifies for a discount'), '', 'no-popup');
        }
        else {
          CRM_Core_Session::setStatus(ts('Members recieve a discount on this page sign up today'), '', 'no-popup');
        }
      }
      else {
        CRM_Core_Session::setStatus(ts('You are not logged in, Members receive a discount if you are a member please login, if you are interested in becoming a member please visit XXX'), '', 'no-popup');
      }
    }
  }
}

function contribmemberdiscount_civicrm_buildAmount($pageType, &$form, &$amount) {
  if ($pageType == 'contribution') {
    // If qualifying page
    if (contribmemberdiscount_isQualifyingPage($form->getVar('_id'))) {
      $contactId = $form->getContactID();
      // IF the contact is logged in
      if (!empty($contactId)) {
        // IF qualified member with qualifing status
        if (contribmemberdiscount_isQualifyingMember($contactId)) {
          try {
            $discountAmount = civicrm_api3('Setting', 'get', array(
              'sequential' => 1,
              'return' => array("contribmemberdiscount_amount", "contribmemberdiscount_pricefieldoptions"),
            ));
          }
          catch (CiviCRM_API3_Exception $e) {
            $error = $e->getMessage();
            CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.customcivistylesui')));
          }
          if (!empty($discountAmount['values'][0]['contribmemberdiscount_pricefieldoptions'])) {
            foreach ($amount as $priceField => &$details) {
              foreach ($details['options'] as $priceOptionId => &$values) {
                if (($values['amount'] - $discountAmount['values'][0]['contribmemberdiscount_amount']) > 0) {
                  $values['label'] = $values['label'] . ' - includes a $' . $discountAmount['values'][0]['contribmemberdiscount_amount'] . ' discount because you are logged in as a qualifying member';
                  $values['amount'] = $values['amount'] - $discountAmount['values'][0]['contribmemberdiscount_amount'];
                }
                else {
                  $values['amount'] = 0;
                }
              }
            }
          }
        }
      }
    }
  }
}

function contribmemberdiscount_isQualifyingPage($formId) {
  $applyDiscount = FALSE;
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
  // If setting for contribution pages to apply discount on is set
  if (!empty($pages['values'][0]['contribmemberdiscount_contribpages'])) {
    // IF on a page that the setting is set for
    if (in_array($formId, $pages['values'][0]['contribmemberdiscount_contribpages'])) {
      $memInfo = contribmemberdiscount_getQualifyingMemInfo();
      if (!empty($memInfo['memtype']) && !empty($memInfo['memstatus'])) {
        $applyDiscount = TRUE;
      }
    }
  }
  return $applyDiscount;
}

function contribmemberdiscount_getQualifyingMemInfo() {
  $memtypes = $memstatus = NULL;
  try {
    $qualifyingMemSetting = civicrm_api3('Setting', 'get', array(
      'sequential' => 1,
      'return' => array("contribmemberdiscount_memtypes", "contribmemberdiscount_memstatus"),
    ));
  }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
    CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.customcivistylesui')));
  }
  if (!empty($qualifyingMemSetting['values'][0]['contribmemberdiscount_memtypes'])) {
    $memtypes = $qualifyingMemSetting['values'][0]['contribmemberdiscount_memtypes'];
  }
  if (!empty($qualifyingMemSetting['values'][0]['contribmemberdiscount_memstatus'])) {
    $memstatus = $qualifyingMemSetting['values'][0]['contribmemberdiscount_memstatus'];
  }
  return array(
    'memtype' => $memtypes,
    'memstatus' => $memstatus,
  );
}

function contribmemberdiscount_isQualifyingMember($contactId) {
  // TODO make three responses, expired or non qualifying status, not qualified, qualified
  $qualified = FALSE;
  $memInfo = contribmemberdiscount_getQualifyingMemInfo();
  if (!empty($contactId)) {
    $params = array(
      'contact_id' => $contactId,
      'membership_type_id' => array('IN' => $memInfo['memtype']),
      'status_id' => array('IN' => $memInfo['memstatus']),
      'sequential' => 1,
    );
    try {
      $membership = civicrm_api3('Membership', 'get', $params);
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.customcivistylesui')));
    }
    if (!empty($membership['values'][0]['id'])) {
      $qualified = TRUE;
    }
    else {
      $qualified = FALSE;
    }
  }
  return $qualified;
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
