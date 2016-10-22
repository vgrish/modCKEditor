Ext.ns('modckeditor');

modckeditor.ckeditor = function (config, editorConfig) {
	Ext.apply(this.cfg, editorConfig, {});

	modckeditor.ckeditor.superclass.constructor.call(this, config);
};


Ext.extend(modckeditor.ckeditor, Ext.Component, {
	cfg: {
		selector: '#ta',
		document_base_url: MODx.config.base_url,
	},
	allowDrop: false,

	initComponent: function () {
		modckeditor.ckeditor.superclass.initComponent.call(this);

		Ext.onReady(this.render, this);
	},

	editors: {},

	render: function () {
		var $this = this;
		Ext.apply(this.cfg, modckeditor.editorConfig, {});

		Ext.each(Ext.query(this.cfg.selector), function (t) {
			var uid = t.id;

			this.editors[uid] = CKEDITOR.replace(uid, {});

			/*var element = Ext.get(t.id);
			if (element) {
				console.log(o);
			}*/

		}, this);
	}

});


modckeditor.loadForTVs = function () {
	new modckeditor.ckeditor({
		allowDrop: false
	}, {
		selector: '.modx-richtext'
	});
};


MODx.loadRTE = function (id) {
	new modckeditor.ckeditor({
		allowDrop: false
	}, {
		selector: '#' + id
	});
};