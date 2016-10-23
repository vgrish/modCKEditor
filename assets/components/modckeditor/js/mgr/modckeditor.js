Ext.ns('modckeditor');


modckeditor.ckeditor = function (editorConfig) {
	Ext.apply(this.cfg, editorConfig, {});

	modckeditor.ckeditor.superclass.constructor.call(this);
};


Ext.extend(modckeditor.ckeditor, Ext.Component, {
	cfg: {
		selector: '#ta',
		component: 'content',
		editorCompact: {
			tvs: true,
			content: false
		},

		skin: 'moono',
	},
	editors: {},

	initComponent: function () {
		modckeditor.ckeditor.superclass.initComponent.call(this);

		Ext.onReady(this.render, this);
	},

	render: function () {
		Ext.apply(this.cfg, modckeditor.editorConfig, {});
		Ext.each(Ext.query(this.cfg.selector), function (t) {
			this.initialize(t.id, this.cfg);
		}, this);
	},


	initialize: function (uid, config) {
		var assetsUrl = modckeditor.tools.getAssetsUrl();

		/* add config */
		if (!config['filebrowserBrowseUrl']) {
			config['filebrowserBrowseUrl'] = modckeditor.tools.getFileBrowseUrl();
		}
		if (!config['filebrowserUploadUrl']) {
			config['filebrowserUploadUrl'] = modckeditor.tools.getFileUploadUrl('image');
		}

		if (config['addExternalPlugins']) {
			for (var name in config['addExternalPlugins']) {
				var script = config['addExternalPlugins'][name];
				if (script) {
					CKEDITOR.plugins.addExternal(name, assetsUrl + script, '');
				}
			}
		}

		if (config['addExternalSkin']) {
			for (var name in config['addExternalSkin']) {
				var skin = config['addExternalSkin'][name];
				if (skin && name == config.skin) {
					config.skin = skin + ',' + assetsUrl + skin;
				}
			}
		}

		if (config['addTemplates']) {
			var templates = config.templates ? config.templates.split(',') : [];
			var templatesFiles = [];

			for (var name in config['addTemplates']) {
				var template = config['addTemplates'][name];
				if (template && !modckeditor.tools.inArray(name, templates)) {
					templates.push(name);
					templatesFiles.push(assetsUrl + template);
				}
			}

			if (templatesFiles.length) {
				config.templates_files = templatesFiles;
				config.templates = templates.join(',');
			}
		}

		if (config['enableModTemplates']) {
			var templates = config.templates ? config.templates.split(',') : [];
			var name = 'modtemplate';
			if (!modckeditor.tools.inArray(name, templates)) {
				templates.push(name);
				config.templates = templates.join(',');
			}
		}

		/* compact mode */
		var editor = null;
		var compact = modckeditor.tools.getEditorCompact(config);
		if (compact) {
			editor = CKEDITOR.inline(uid, config);
		}
		else {
			editor = CKEDITOR.replace(uid, config);
		}

		if (!editor) {
			return false;
		}

		/* add save */
		editor.setKeystroke(CKEDITOR.CTRL + 83, '_save');
		editor.addCommand('_save', {
			exec: function (editor) {
				var updateButton = modckeditor.tools.getUpdateButton();
				if (updateButton) {
					MODx.activePage.ab.handleClick(updateButton);
				}
			}
		});

		/* add droppable */
		if (config['droppable']) {
			editor.on('uiReady', function () {
				/* TODO */

			}, this);
		}


		CKEDITOR.on("instanceReady", function () {

			if (modckeditor.editorConfig['enableModTemplates'] && !CKEDITOR['enableModTemplates']) {
				CKEDITOR['enableModTemplates'] = true;

				MODx.Ajax.request({
					url: modckeditor.config.connector_url,
					params: {
						action: 'mgr/template/getlist',
						component: config.component || '',
					},
					listeners: {
						success: {
							fn: function (r) {
								var templates = [];
								r.results.filter(function (row) {
									var template = [];

									template.title = row['templatename'];
									template.image = '';
									template.description = row['description'];
									template.html = row['content'];

									templates.push(template);
								});

								var loadTemplates = CKEDITOR.getTemplates('modtemplate');
								if (!loadTemplates || loadTemplates == 'undefined') {
									CKEDITOR.addTemplates('modtemplate', {
										templates: templates
									});
								}
							},
							scope: this
						}
					}
				});
			}

		});

		this.editors[uid] = editor;
	},

});


modckeditor.loadForTVs = function () {
	if (modckeditor.config == undefined) {
		return false;
	}
	new modckeditor.ckeditor({
		component: 'tvs',
		selector: '.modx-richtext',
		droppable: false
	});
};


MODx.loadRTE = function (id) {
	if (modckeditor.config == undefined) {
		return false;
	}
	new modckeditor.ckeditor({
		component: 'content',
		selector: '#' + id,
		droppable: true
	});
};


MODx.unloadRTE = function(id) {
	var editor = CKEDITOR.instances[id];
	if (editor) {
		CKEDITOR.remove(editor);
		editor.destroy(true);
	}
};


