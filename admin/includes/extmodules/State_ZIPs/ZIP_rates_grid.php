<?php
/*
  $Id: tax_rates_grid $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.State_ZIPs.ZIPRatesGrid = function(config) {
  
  config = config || {};
  
  this.StateZIPId = null;
  this.StateZIPTitle = null;
  
  config.title = '<?php echo $osC_Language->get('section_ZIP_rates'); ?>';
  config.region = 'east';
  config.split = true;
  config.minWidth = 320;
  config.maxWidth = 500;
  config.width = 432;
  config.viewConfig = {
    emptyText: TocLanguage.gridNoRecords,
    forceFit: true
  };
     
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'State_ZIPs',
      action: 'list_ZIP_rates'        
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'ZIP_number'
    }, [
      'State_ID',
      'ZIP_number',
      'ZIP_name',
      'ZIP_tax',
      'ZIP_total_tax'
    ]),
    autoLoad: true
  });  
  
  config.rowActions = new Ext.ux.grid.RowActions({
    actions: [
     {iconCls: 'icon-edit-record', qtip: TocLanguage.tipEdit},
     {iconCls: 'icon-delete-record', qtip: TocLanguage.tipDelete}],
    widthIntercept: Ext.isSafari ? 4 : 2
  });
  config.rowActions.on('action', this.onRowAction, this);    
  config.plugins = config.rowActions;
     
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {header: '<?php echo $osC_Language->get('table_heading_ZIP_rate_zone'); ?>', dataIndex: 'ZIP_number'},
    {header: '<?php echo $osC_Language->get('table_heading_ZIP_rate_priority'); ?>', dataIndex: 'ZIP_name', width: 80, align: 'left'},
    {header: '<?php echo $osC_Language->get('table_heading_ZIP_rate'); ?>', dataIndex: 'ZIP_tax', width: 56, align: 'left'},
    {header: '<?php echo $osC_Language->get('table_heading_total_rate'); ?>', dataIndex: 'ZIP_total_tax', width: 66, align: 'left'},
    config.rowActions
  ]);
  
  config.tbar = [
    {
      text: TocLanguage.btnAdd,
      iconCls: 'add',
      handler: this.onAdd,
      scope: this
    },
    '-',
    {
      text: TocLanguage.btnDelete,
      iconCls: 'remove',
      handler: this.onBatchDelete,
      scope: this
    },
    '-',
    { text: TocLanguage.btnRefresh,
      iconCls: 'refresh',
      handler: this.onRefresh,
      scope: this
    }
  ];    

  Toc.State_ZIPs.ZIPRatesGrid.superclass.constructor.call(this, config);
};

Ext.extend(Toc.State_ZIPs.ZIPRatesGrid, Ext.grid.GridPanel, {

  iniGrid: function(record) {
    this.StateZIPId = record.get('State_ID');
    /*this.StateZIPTitle = record.get('ZIP_name');
    alert(this.StateZIPTitle);*/
    var store = this.getStore();
    
    store.baseParams['State_ZIP_id'] = this.StateZIPId;  								
    store.load();  
  },
  
  onAdd: function() {
      var dlg = this.owner.createZIPRatesDialog();
      
      dlg.on('saveSuccess', function(){
        this.onRefresh();
      }, this);

      dlg.show();
  },
  
  onEdit: function(record) {
    var ZIPnumber = record.get('ZIP_number');
    var dlg = this.owner.createZIPRatesDialog();
    dlg.setTitle(record.get('ZIP_name'));
    
    dlg.on('saveSuccess', function(){
      this.onRefresh();
    }, this);
    
    dlg.show(ZIPnumber);
  },
  
  onDelete: function(record) {
    var ZIPRatesId = record.get('ZIP_number');
                  
    Ext.Msg.confirm(
      TocLanguage.msgWarningTitle, 
      TocLanguage.msgDeleteConfirm, 
      function(btn) {
        if(btn == 'yes') {                                                                                                                                                                 
          Ext.Ajax.request({
            url: Toc.CONF.CONN_URL,
            params: { 
              module: 'State_ZIPs',
              action: 'delete_ZIP_rate',
              rateId: ZIPRatesId
            },
            callback: function(options, success, response) {
              var result = Ext.decode(response.responseText);
              
              if (result.success == true) {
                this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                this.onRefresh();
            } else {
                Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
              }
            },
            scope: this 
          });
        }
      }, this);                                                               
  },
      
  onBatchDelete: function() {
    var keys = this.getSelectionModel().selections.keys;
    
    if (keys.length > 0) {    
      var batch = keys.join(',');
    
      Ext.Msg.confirm(
        TocLanguage.msgWarningTitle,
        TocLanguage.msgDeleteConfirm,
        function(btn, text, s) {
          if(btn == 'yes') {                                                                                                                                                                 
            Ext.Ajax.request({
            url: Toc.CONF.CONN_URL,
            params: { 
              module: 'State_ZIPs',
              action: 'delete_ZIP_rates',
              batch: batch                                        
            },
            callback: function(options, success, response){
              var result = Ext.decode(response.responseText);
              
              if (result.success == true) {
                this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                this.onRefresh();
              }
              else {
                Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
              }
            },
            scope: this                     
            });                
          }                                              
        }, this); 
                  
    }
    else{
       Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  },
  
  onRefresh: function() {
    this.getStore().reload();
  },
  
  onRowAction:function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-delete-record':
      this.onDelete(record);
      break;
        
      case 'icon-edit-record':
      this.onEdit(record);
      break;
    }
  },
  
  reset: function() {
    this.setTitle('<?php echo $osC_Language->get('section_ZIP_rates'); ?>');
    this.StateZIPId = null;
    this.StateZIPTitle = null;
    this.getStore().removeAll();
  } 
});
