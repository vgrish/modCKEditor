var modckeditor = function (config) {
	config = config || {};
	modckeditor.superclass.constructor.call(this, config);
};
Ext.extend(modckeditor, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('modckeditor', modckeditor);

modckeditor = new modckeditor();