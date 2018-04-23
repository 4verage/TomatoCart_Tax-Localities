<?php
/*
  $Id: tax_classes_main_panel.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.State_ZIPs.StateZIPsMainPanel = function(config) {

  config = config || {};
  
  config.layout = 'border';
  
  config.grdStateZIPs = new Toc.State_ZIPs.StateZIPsGrid({owner : config.owner});
  config.grdZIPRates = new Toc.State_ZIPs.ZIPRatesGrid({owner: config.owner}); 
  
  config.grdStateZIPs.on('selectchange', this.onGrdStateZIPsSelectChange, this);
  config.grdStateZIPs.getStore().on('load', this.onGrdStateZIPsLoad, this);
  config.grdZIPRates.getStore().on('load', this.onGrdZIPRatesLoad, this);
      
  config.items = [config.grdStateZIPs, config.grdZIPRates];  
  
  Toc.State_ZIPs.StateZIPsMainPanel.superclass.constructor.call(this, config);    
};

Ext.extend(Toc.State_ZIPs.StateZIPsMainPanel, Ext.Panel, {   

  onGrdStateZIPsLoad: function() {
    if (this.grdStateZIPs.getStore().getCount() > 0) {
        this.grdStateZIPs.getSelectionModel().selectFirstRow();
        record = this.grdStateZIPs.getStore().getAt(0);
        
        this.onGrdStateZIPsSelectChange(record);
    } else {
      this.grdZIPRates.reset();
    }
  },
  
  onGrdStateZIPsSelectChange: function(record) {
    this.grdZIPRates.setTitle('<?php echo $osC_Language->get('heading_title'); ?>: '+ record.get('State_ZIP_title'));
    this.grdZIPRates.iniGrid(record);
  },

  onGrdZIPRatesLoad: function() {
    record = this.grdStateZIPs.getSelectionModel().getSelected() || null;
    if (record) {
      record.set('State_entry_count', this.grdZIPRates.getStore().getCount());
      this.grdStateZIPs.getStore().commitChanges();
    }
  }
});