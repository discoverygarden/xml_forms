// @codingStandardsIgnoreFile
Ext.formbuilder.propertiesStore = Ext.create('Ext.data.Store', {
  storeId: 'PropertiesStore',
  model: 'Properties',
  autoLoad: true,
  autoSync: true,
  proxy: {
    type: 'memory',
    data: drupalSettings.xml_form_builder.properties,
    reader: {
      type: 'json'
    }
  }
});
Ext.formbuilder.propertiesStore.sync();
