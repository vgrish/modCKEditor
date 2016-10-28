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
				this.registerDrop(editor);
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
	},

	registerDrop: function (editor) {
		if (!editor.container || !editor.container.$) {
			return false;
		}

		var ddTarget = new Ext.Element(editor.container.$);
		var ddTargetEl = ddTarget.dom;

		var insert = {
			text: function (text) {
				var regex = /<br\s*[\/]?>/gi;
				editor.insertText(text.replace(regex, "\n"));
				editor.focus();
			},
			link: function (id, text) {
				if (text) {
					var element = '<a href="[[~' + id + ']]" title="' + text + '">' + text + '</a>';
					editor.insertHtml(element);
					editor.focus();
				}
			},
			file: function (path, type) {
				if (type) {
					var element = '<' + type + ' src="' + path + '" controls="">';
					editor.insertHtml(element);
					editor.focus();
				}
			}
		};

		var dropTarget = new Ext.dd.DropTarget(ddTargetEl, {
			ddGroup: 'modx-treedrop-dd',

			_notifyEnter: function (ddSource, e, data) {
				fakeDiv = Ext.DomHelper.insertAfter(ddTarget, {
					tag: 'div',
					style: 'position: absolute;top: 0;left: 0;right: 0;bottom: 0;'
				});
				ddTarget.frame();
				editor.focus();
			},

			notifyOut: function (ddSource, e, data) {
				fakeDiv && fakeDiv.remove();
				ddTarget.on('mouseover', onMouseOver);
			},

			notifyDrop: function (ddSource, e, data) {

				if (editor.mode != 'wysiwyg') {
					return false;
				}

				fakeDiv && fakeDiv.remove();
				var v = '';
				var win = false;
				switch (data.node.attributes.type) {
					case 'modResource':
						insert.link(data.node.attributes.pk, data.node.text.replace(/\s*<.*>.*<.*>/, ''));
						break;
					case 'snippet':
						win = true;
						break;
					case 'chunk':
						win = true;
						break;
					case 'tv':
						win = true;
						break;
					case 'file':
						var types = {
							'jpg': 'image',
							'jpeg': 'image',
							'png': 'image',
							'gif': 'image',
							'svg': 'image',
							'ogg': 'audio',
							'mp3': 'audio',
							'ogv': 'video',
							'webm': 'video',
							'mp4': 'video'
						};
						var ext = data.node.attributes.text.substring(data.node.attributes.text.lastIndexOf('.') + 1);

						if (types[ext]) {
							insert.file(data.node.attributes.url, types[ext]);
						} else {
							insert.text(data.node.attributes.url);
						}

						break;
					default:
						var dh = Ext.getCmp(data.node.attributes.type + '-drop-handler');
						if (dh) {
							return dh.handle(data, {
								ddTargetEl: ddTargetEl,
								cfg: cfg,
								iframe: true,
								iframeEl: ddTargetEl,
								onInsert: insert.text
							});
						}
						return false;
						break;
				}

				if (win) {
					MODx.loadInsertElement({
						pk: data.node.attributes.pk,
						classKey: data.node.attributes.classKey,
						name: data.node.attributes.name,
						output: v,
						ddTargetEl: ddTargetEl,
						cfg: {onInsert: insert.text},
						iframe: true,
						onInsert: insert.text
					});
				}

				return true;
			}
		});

		dropTarget.addToGroup('modx-treedrop-elements-dd');
		dropTarget.addToGroup('modx-treedrop-sources-dd');

		var onMouseOver = function (e) {
			if (Ext.dd.DragDropMgr.dragCurrent) {
				dropTarget._notifyEnter();
				ddTarget.un('mouseover', onMouseOver);
			}
		};
		ddTarget.on('mouseover', onMouseOver);

		this.on('destroy', function () {
			dropTarget.destroy();
		});
	}

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


MODx.unloadRTE = function (id) {
	var editor = CKEDITOR.instances[id];
	if (editor) {
		CKEDITOR.remove(editor);
		editor.destroy(true);
	}
};


