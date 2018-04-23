<?php
/*
  $Id: main.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  echo 'Ext.namespace("Toc.State_ZIPs");';  

  include('State_ZIPs_dialog.php');
  include('State_ZIPs_grid.php');
  include('ZIP_rates_dialog.php');
  include('ZIP_rates_grid.php');
  include('State_ZIPs_main_panel.php');
?>

Ext.override(TocDesktop.StateZIPsWindow, {
  createWindow : function() {
    var desktop = this.app.getDesktop();
    var win = desktop.getWindow('State_ZIPs-win');

    if (!win) {                               
      pnl = new Toc.State_ZIPs.StateZIPsMainPanel({owner: this});
      
      win = desktop.createWindow({
        id: 'State_ZIPs-win',
        title: '<?php echo $osC_Language->get('heading_title'); ?>',
        width: 800,
        height: 400,
        iconCls: 'icon-State_ZIPs-win',
        layout: 'fit',
        items: pnl
      });
    }   
    
    win.show();
  },
  
  createStateZIPsDialog: function() {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('State-ZIPs-dialog-win');    

    if (!dlg) {
      dlg = desktop.createWindow({}, Toc.State_ZIPs.StateZIPsDialog); 
                  
      dlg.on('saveSuccess', function(feedback) {
        this.app.showNotification({title: TocLanguage.msgSuccessTitle, html: feedback});
      }, this);
    }    
    
    return dlg;
  },
  
  createZIPRatesDialog : function() {
    var desktop = this.app.getDesktop();
   var dlg = desktop.getWindow('ZIP-rates-dialog-win');

    if (!dlg) {
       dlg = desktop.createWindow({},Toc.State_ZIPs.ZIPRatesDialog);
       
       dlg.on('saveSuccess', function(feedback) {
         this.app.showNotification({title: TocLanguage.msgSuccessTitle, html: feedback});
       }, this);
    }
    
    return dlg;
  }
});
