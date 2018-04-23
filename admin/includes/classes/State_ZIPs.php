<?php
/*
  $Id: zone_groups.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_StateZIPs_Admin {
    function getData($id, $key = null) {
      global $osC_Database;

      $Qzones = $osC_Database->query('select * from :table_state_zips where state_zip_id = :state_zip_id');
      $Qzones->bindTable(':table_state_zips', TABLE_STATE_ZIPS);
      $Qzones->bindInt(':state_zip_id', $id);
      $Qzones->execute();
      
      $Qstate = $osC_Database->query('select * from :table_zones where zone_id = :state_zip_id');
      $Qstate->bindTable(':table_zones', TABLE_ZONES);
      $Qstate->bindInt(':state_zip_id', $Qzones->value('State_ID'));
      $Qstate->execute();
      
      $Qcountry = $osC_Database->query('select * from :table_countries where countries_id = :Country_ID');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':Country_ID', $Qzones->value('Country_ID'));
      $Qcountry->execute();
      
      $convData = array ( 'state_base_tax_label' => $Qzones->value('Tax') + 0 );
      
      $data = array_merge($Qzones->toArray(), $Qcountry->toArray(), $Qstate->toArray(), $convData);

      $Qzones->freeResult();
      $Qcountry->freeResult();

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    function save($id = null, $data) {
      global $osC_Database;

      error_log($id);
      if ( is_numeric($id) ) {
        $Qzone = $osC_Database->query('update :table_state_zips set State_ID = :State_ID, Country_ID = :Country_ID, Tax = :Tax where state_zip_id = :State_ZIP_id');
        $Qzone->bindInt(':State_ZIP_id', $id);
      } else {
        $Qzone = $osC_Database->query('insert into :table_state_zips (State_ID, Country_ID, Tax) values (:State_ID, :Country_ID, :Tax)');
      }
      $Qzone->bindTable(':table_state_zips', TABLE_STATE_ZIPS);
      $Qzone->bindInt(':State_ID', $data['State_ID']);
      $Qzone->bindInt(':Country_ID', $data['Country_ID']);
      $Qzone->bindFloat(':Tax', $data['Tax']);
      $Qzone->setLogging($_SESSION['module'], $id);
      $Qzone->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    function delete($id, $st) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();
      
      /* Checks to see if there are records in this group. */
      $getUsage = $osC_Database->query('select count(*) as nRates from :table_zip_rates where State_ID = :State_ID');
      $getUsage->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
      $getUsage->bindInt(':State_ID', $st);
      $getUsage->setLogging($_SESSION['module'], $id);
      $getUsage->execute();
      
      /* If there are records, delete them. */
      if ( $getUsage->value('nRates') > 0 ) {
      	$cRecords = $osC_Database->query('delete from :table_zip_rates where State_ID = :State_ID');
      	$cRecords->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
      	$cRecords->bindInt(':State_ID', $st);
      	$cRecords->setLogging($_SESSION['module'], $id);
      	$cRecords->execute();
      }

      /* Delete Group */
      $Qentry = $osC_Database->query('delete from :table_state_zips where state_zip_id = :state_zip_id');
      $Qentry->bindTable(':table_state_zips', TABLE_STATE_ZIPS);
      $Qentry->bindInt(':state_zip_id', $id);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {

      } else {
        $error = true;
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function getEntryData($id) {
      global $osC_Database, $osC_Language;

      $Qentries = $osC_Database->query('select * from :table_zip_rates WHERE ZIP = :ZIP_number LIMIT 1');
      $Qentries->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
      $Qentries->bindInt(':ZIP_number', $id);
      $Qentries->execute();
      
      $Qstate = $osC_Database->query('select * from :table_zones WHERE zone_id = :State_ID');
      $Qstate->bindTable(':table_zones', TABLE_ZONES);
      $Qstate->bindInt(':State_ID', $Qentries->value('State_ID'));
      $Qstate->execute();
      
      $tax = array( 'ZIP_rate' => $Qentries->value('Tax') );
      $descrip = array( 'ZIP_description' => $Qentries->value('Name') );

      $data = array_merge($Qentries->toArray(), $tax, $descrip, $Qstate->toArray());

      $Qentries->freeResult();

      return $data;
    }

    function saveEntry($id = null, $data) {
      global $osC_Database;

      if ( $data['BoolTest'] === true ) {
        $Qentry = $osC_Database->query('update :table_zip_rates set State_ID = :State_ID, ZIP = :ZIP, Name = :ZIP_description, Tax = :Tax where ZIP = :FIND');
        $Qentry->bindInt(':FIND', $id);
      } else {
        $Qentry = $osC_Database->query('insert into :table_zip_rates (State_ID, ZIP, Name, Tax) values (:State_ID, :ZIP, :ZIP_description, :Tax)');
      }
      $Qentry->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
      $Qentry->bindInt(':State_ID', $data['State_ID']);
      $Qentry->bindInt(':ZIP', $id);
      $Qentry->bindValue(':ZIP_description', $data['Name']);
      $Qentry->bindValue(':Tax', $data['Tax']);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      error_log( $osC_Database->getError() );
      return false;
    }

    function deleteEntry($id) {
      global $osC_Database;

      $Qentry = $osC_Database->query('delete from :table_zip_rates where ZIP = :ZIP');
      $Qentry->bindTable(':table_zip_rates', TABLE_ZIP_RATES);
      $Qentry->bindInt(':ZIP', $id);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
