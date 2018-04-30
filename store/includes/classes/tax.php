<?php
/*
  $Id: tax.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Tax {
    var $tax_rates;

// class constructor
    function osC_Tax() {
      $this->tax_rates = array();
    }

// class methods
    function getTaxRate($class_id, $ZIP = -1, $zone_id = -1) {
      global $osC_Database, $osC_ShoppingCart;

      if ( ($ZIP == -1) && ($zone_id == -1) ) {
        $ZIP = $osC_ShoppingCart->getTaxingAddress('ZIP');
        $zone_id = $osC_ShoppingCart->getTaxingAddress('zone_id');
      }

      if (isset($this->tax_rates[$class_id][$ZIP][$zone_id]['rate']) == false) {
        $ZIPs = false;
	$States = false;
	$Taxable = false;

	$ZIPtax = $osC_Database->query('select Tax from :table_zip_rates WHERE ZIP = :ZIP');
	$ZIPtax->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
	$ZIPtax->bindInt(':ZIP', $ZIP);
	$ZIPtax->execute();
	
	if ($ZIPtax->numberOfRows() > 0) {
		$ZIPs = true;	
	} 
		
	$StateCheck = $osC_Database->query('select Tax from :table_state_zips WHERE State_ID = :zone_id');
	$StateCheck->bindTable(':table_state_zips', TABLE_STATE_ZIPS);
	$StateCheck->bindInt(':zone_id', $zone_id);
	$StateCheck->execute();
	
	if ($StateCheck->numberOfRows() > 0) {
		$States = true;	
	}
	
	if ($class_id > 0) {
		$Taxable = true;
	}
	
	$taxer = 0;
	
	if ($ZIPs == true) {
		$taxer = $taxer + $ZIPtax->value('Tax');
	}
	
	if ($States == true) {
		$taxer = $taxer + $StateCheck->value('Tax');
	}
	
	if ($Taxable == true) {
		$tax_rate = $taxer;
        } else {
          $tax_rate = 0;
        }

        $this->tax_rates[$class_id][$ZIP][$zone_id]['rate'] = $tax_rate;
      }

      return $this->tax_rates[$class_id][$ZIP][$zone_id]['rate'];
    }

    function getTaxRateDescription($class_id, $ZIP, $zone_id) {
      global $osC_Database, $osC_Language;

      if (isset($this->tax_rates[$class_id][$ZIP][$zone_id]['description']) == false) {
        $ZIPtax = $osC_Database->query('select Name from :table_zip_rates WHERE ZIP = :ZIP');
	$ZIPtax->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
	$ZIPtax->bindInt(':ZIP', $ZIP);
	$ZIPtax->execute();

	$STATEtax = $osC_Database->query('select (select zone_code from :table_zones WHERE zone_id = :zone_id) as abbrv (select Tax from :table_state_zips WHERE State_ID = :zone_id) as state_tax');
	$STATEtax->bindTable(':table_zones', TABLES_ZONES);
	$STATEtax->bindTable(':table_state_zips', TABLE_STATE_ZIPS);
	$STATEtax->bindInt(':zone_id', $zone_id);
	$STATEtax->execute();
	
	if ($STATEtax->value('state_tax') && $STATEtax->value('state_tax') > 0) {
		$state_description = $STATEtax->value('abbrv') + " State Tax";
		$tax_description = $state_description;
	}

        if ($ZIPtax->numberOfRows()) {
          $zip_description = $ZIPtax->value('Name');
          
          if ($tax_description && !isempty($tax_description)) {
          	$tax_description .= "+" + $zip_description + " Local Taxes";
          } else {
          	$tax_description = $zip_description + " Local Sales Tax";
          }

	  $this->tax_rates[$class_id][$ZIP][$zone_id]['description'] = $tax_description;
          //$this->tax_rates[$class_id][$ZIP][$zone_id]['description'] = substr($tax_description, 0, -3);
        } else {
          $this->tax_rates[$class_id][$ZIP][$zone_id]['description'] = $osC_Language->get('tax_rate_unknown');
        }
      }
      return $this->tax_rates[$class_id][$ZIP][$zone_id]['description'];
    }

    function calculate($price, $tax_rate) {
      global $osC_Currencies;

      return osc_round($price * $tax_rate / 100, $osC_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }

    function displayTaxRateValue($value, $padding = null) {
      if (!is_numeric($padding)) {
        $padding = TAX_DECIMAL_PLACES;
      }

      if (strpos($value, '.') !== false) {
        while (true) {
          if (substr($value, -1) == '0') {
            $value = substr($value, 0, -1);
          } else {
            if (substr($value, -1) == '.') {
              $value = substr($value, 0, -1);
            }

            break;
          }
        }
      }

      if ($padding > 0) {
        if (($decimal_pos = strpos($value, '.')) !== false) {
          $decimals = strlen(substr($value, ($decimal_pos+1)));

          for ($i=$decimals; $i<$padding; $i++) {
            $value .= '0';
          }
        } else {
          $value .= '.';

          for ($i=0; $i<$padding; $i++) {
            $value .= '0';
          }
        }
      }

      return $value . '%';
    }
  }
?>
