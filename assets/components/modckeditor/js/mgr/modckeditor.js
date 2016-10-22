Ext.ns('modckeditor');


modckeditor.ckeditor = function (config, editorConfig) {
	Ext.apply(this.cfg, editorConfig, {});

	modckeditor.ckeditor.superclass.constructor.call(this, config);
};


Ext.extend(modckeditor.ckeditor, Ext.Component, {
	cfg: {
		selector: '#ta',
		component: 'content',
		editorCompact: {
			tvs: true,
			content: false
		},
		document_base_url: MODx.config['base_url'],

		skin: 'moono',
		filebrowserBrowseUrl: modckeditor.tools.getFileBrowseUrl(),

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
			this.initialize(t.id, this.cfg);

			/*var element = Ext.get(t.id);
			 if (element) {
			 console.log(o);
			 }*/

		}, this);
	},


	initialize: function (uid, config) {

		/* compact mode */
		var compact = modckeditor.tools.getEditorCompact(config);
		if (compact) {
			this.editors[uid] = CKEDITOR.inline(uid, config);
		}
		else {
			this.editors[uid] = CKEDITOR.replace(uid, config);
		}

		if (!this.editors[uid]) {
			return false;
		}

		/* add save */
		this.editors[uid].setKeystroke(CKEDITOR.CTRL + 83, '_save');
		this.editors[uid].addCommand('_save', {
			exec: function (editor) {
				console.log('sdavee');

				var updateButton = modckeditor.tools.getUpdateButton();
				if (updateButton) {
					MODx.activePage.ab.handleClick(updateButton);
				}
			}
		});


		console.log(config);


	},

});


modckeditor.loadForTVs = function () {
	new modckeditor.ckeditor({
		allowDrop: false
	}, {
		component: 'tvs',
		selector: '.modx-richtext'
	});
};


MODx.loadRTE = function (id) {
	new modckeditor.ckeditor({
		allowDrop: false
	}, {
		component: 'content',
		selector: '#' + id
	});
};


Ext.onReady(function () {

});



