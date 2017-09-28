Ext.formbuilder.elementTypeStore = Ext.data.Store({
  storeId: 'ElementTypes',
  fields: ['display', 'value'],
  proxy: {
    type: 'memory',
    reader: {
      type: 'json'
    },
    writer: {
      type: 'json'
    }
  },
  data: drupalSettings.xml_form_builder.element_types
});
