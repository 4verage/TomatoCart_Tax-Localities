<?php
/*
  $Id: tax_rates_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.State_ZIPs.ZIPRatesDialog = function(config) {

  config = config || {}; 
  
  config.id = 'ZIP-rate-dialog-win';
  config.title = '<?php echo $osC_Language->get('action_heading_new_ZIP_rate'); ?>';
  config.width = 500;
  config.modal = true;
  config.items = this.buildForm();
  
  config.buttons = [
    {
      text: TocLanguage.btnSave,
      handler: function(){
        this.submitForm();
      },
      scope:this
    }, 
    {
      text: TocLanguage.btnClose,
      handler: function() {
        this.close();
      },
      scope:this
    }
  ];

  this.addEvents({'saveSuccess': true});  
  
  Toc.State_ZIPs.ZIPRatesDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.State_ZIPs.ZIPRatesDialog, Ext.Window, {  

  show: function (StateZIPId) {
    this.StateZIPId = StateZIPId || null;
    
    this.frmZIPRate.form.reset();
    this.frmZIPRate.form.baseParams['State_ZIP_id'] = this.StateZIPId;

    if (StateZIPId > 0) {
      this.frmZIPRate.form.baseParams['ZIP_exists'] = true;
      this.frmZIPRate.load({
        url: Toc.CONF.CONN_URL,
        params: {
          module: 'State_ZIPs',
          action: 'load_ZIP_rate',
        },
        success: function(form, action) {
          this.cobZoneGroup.setValue(action.result.data.State_ID);
          this.cobZoneGroup.setRawValue(action.result.data.zone_name);
          Toc.State_ZIPs.ZIPRatesDialog.superclass.show.call(this);
        },
        failure: function() {
          Ext.Msg.alert(TocLanguage.msgErrTitle, TocLanguage.msgErrLoadData)
        },
        scope: this       
      });
    } else {   
      Toc.State_ZIPs.ZIPRatesDialog.superclass.show.call(this);
    }
  },
    
  buildForm: function() {
    var dsGeoZone = new Ext.data.Store({
      url: Toc.CONF.CONN_URL,
      baseParams: {
        module: 'State_ZIPs', 
        action: 'list_State_ZIPs'
      },
      reader: new Ext.data.JsonReader({
        root: Toc.CONF.JSON_READER_ROOT,
        fields: ['State_ID', 'State_ZIP_title']
      }),
      autoLoad: true                                                                                    
    });
    
    this.cobZoneGroup = new Ext.form.ComboBox({
      name: 'State_ZIPs',
      fieldLabel: '<?php echo $osC_Language->get('field_ZIP_rate_zone_group'); ?>', 
      store: dsGeoZone, 
      valueField: 'State_ID', 
      displayField: 'State_ZIP_title', 
      hiddenName: 'State_ID', 
      editable: false, 
      triggerAction: 'all', 
      allowBlank: false
    });
    
    this.frmZIPRate = new Ext.form.FormPanel({ 
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'State_ZIPs',
        action: 'save_ZIP_rate'
      }, 
      border: false,
      layoutConfig: {
        labelSeparator: ''
      },
      defaults: {
        anchor: '97%'
      },
      items: [
        this.cobZoneGroup,
        {xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_ZIP'); ?>', name: 'ZIP', width:300},
        {xtype: 'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_ZIP_rate'); ?>', name: 'ZIP_rate', decimalPrecision: 4, width:300},
        {xtype: 'textfield', fieldLabel: '<?php echo $osC_Language->get('field_ZIP_rate_description'); ?>', name: 'ZIP_description', width:300}
      ]
    });
    
    return this.frmZIPRate;
  },

  submitForm: function() {
    this.frmZIPRate.form.submit({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      success:function(form, action) {
        this.fireEvent('saveSuccess', action.result.feedback); 
        this.close();
      },
      failure: function(form, action) {
        if (action.failureType != 'client') {         
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);      
        }         
      },
      scope: this
    });   
  }
});