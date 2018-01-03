// @codingStandardsIgnoreFile
Ext.formbuilder.elementStore = Ext.create('Ext.data.TreeStore', {
  model: 'Element',
  root: drupalSettings.xml_form_builder.elements
});
