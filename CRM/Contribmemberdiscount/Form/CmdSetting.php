<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Contribmemberdiscount_Form_CmdSetting extends CRM_Core_Form {

  protected $select2style = array();

  public function buildQuickForm() {

    $this->addEntityRef('contribpages', ts('Select Contribution Pages'), array(
      'entity' => 'ContributionPage',
      'placeholder' => ts('- Select Contribution Pages -'),
      'select' => array('minimumInputLength' => 0),
      'multiple' => TRUE,
    ));

    //From cividiscount for styling price field option select
    $this->select2style = array(
      'placeholder' => ts('- none -'),
      'multiple' => TRUE,
      'class' => 'crm-select2 huge',
    );
    $pricesets = CRM_CiviDiscount_Utils::getNestedPriceSets();
    if (!empty($pricesets)) {
      // $this->_multiValued['pricesets'] = $pricesets;
      $this->add('select',
        'pricefieldoptions',
        ts('Price Field Options'),
        $pricesets,
        FALSE,
        array('placeholder' => ts('- any -')) + $this->select2style
      );
    }
    // end code from cividiscount

    $this->addEntityRef('memtypes', ts('Select Membership Types'), array(
      'entity' => 'MembershipType',
      'placeholder' => ts('- Select Membership Type -'),
      'select' => array('minimumInputLength' => 0),
      'multiple' => TRUE,
    ));

    $this->addEntityRef('memstatus', ts("Select Membership Status's"), array(
      'entity' => 'MembershipStatus',
      'placeholder' => ts('- Select Membership Status -'),
      'select' => array('minimumInputLength' => 0),
      'multiple' => TRUE,
    ));

    // TODO add validation to make sure this is a number here
    $this->add('text', 'amount', ts('Discount Amount'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save', array('domain' => 'com.aghstrategies.contribmemberdiscount')),
        'isDefault' => TRUE,
      ),
    ));
    // Send element names to the form.
    $this->assign('elementNames', array('contribpages', 'pricefieldoptions', 'memtypes', 'memstatus', 'amount'));

    // Set Defaults
    $defaults = array();
    try {
      $existingSetting = civicrm_api3('Setting', 'get', array(
        'sequential' => 1,
        'return' => array("contribmemberdiscount_contribpages", "contribmemberdiscount_memtypes", "contribmemberdiscount_memstatus", "contribmemberdiscount_amount"),
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.contribmemberdiscount')));
    }

    $fieldsToSettings = $this->fieldsToSetting();
    foreach ($fieldsToSettings as $field => $setting) {
      if (!empty($existingSetting['values'][0][$setting])) {
        $defaults[$field] = $existingSetting['values'][0][$setting];
      }
    }

    $this->setDefaults($defaults);
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $params = array();
    $fieldsToSettings = $this->fieldsToSetting();
    foreach ($fieldsToSettings as $field => $setting) {
      if (!empty($values[$field])) {
        if ($field !== 'amount' && gettype($values[$field]) == 'string') {
          $params[$setting] = explode(',', $values[$field]);
        }
        else {
          $params[$setting] = $values[$field];
        }
      }
    }
    try {
      $result = civicrm_api3('Setting', 'create', $params);
      CRM_Core_Session::setStatus(ts('You have successfully updated the civicontribution pages to apply the membership discount to.', array('domain' => 'com.aghstrategies.contribmemberdiscount')), 'Settings saved', 'success');
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(t('API Error: %1', array(1 => $error, 'domain' => 'com.aghstrategies.contribmemberdiscount')));
      CRM_Core_Session::setStatus(ts('Error saving pages for contribution pages', array('domain' => 'com.aghstrategies.contribmemberdiscount')), 'Error', 'error');
    }
    parent::postProcess();
  }

  /**
   * Array that matches field to setting
   * @return [type] [description]
   */
  public function fieldsToSetting() {
    return array(
      'contribpages' => 'contribmemberdiscount_contribpages',
      'pricefieldoptions' => 'contribmemberdiscount_pricefieldoptions',
      'memtypes' => 'contribmemberdiscount_memtypes',
      'memstatus' => 'contribmemberdiscount_memstatus',
      'amount' => 'contribmemberdiscount_amount',
    );
  }

}
