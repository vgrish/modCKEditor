Ext.ns('modckeditor');


modckeditor.ckeditor = function (editorConfig) {
	Ext.apply(this.cfg, editorConfig, {});
	modckeditor.ckeditor.superclass.constructor.call(this);
};


Ext.extend(modckeditor.ckeditor, Ext.Component, {
	cfg: {
		selector: '#ta',
		editorCompact: {
			'ta': false,
			'modx-richtext': true,
		},

		skin: 'moono',
	},

	initComponent: function () {
		modckeditor.ckeditor.superclass.initComponent.call(this);
		Ext.onReady(this.render, this);
	},

	render: function () {
		Ext.apply(this.cfg, modckeditor.config, modckeditor.editorConfig, {});
		Ext.each(Ext.query(this.cfg.selector), function (el) {
			this.initialize(el, this.cfg);
		}, this);
	},

	setConfig: function (config) {

		if (!config['filebrowserBrowseUrl']) {
			config['filebrowserBrowseUrl'] = modckeditor.tools.getFileBrowserUrl();
		}
		if (!config['filebrowserUploadUrl']) {
			config['filebrowserUploadUrl'] = modckeditor.tools.getPluginActionUrl('filebrowser', 'upload');
		}

		config['componentName'] = modckeditor.tools.getComponentNameBySelector(config['selector']);

		/* compact mode */
		if (modckeditor.tools.keyExists(config['componentName'], config['editorCompact'])) {
			config['editorCompact'] = config['editorCompact'][config['componentName']];
		}
		else {
			config['editorCompact'] = true;
		}

		return config;
	},

	initialize: function (el, config) {
		var uid = el.id;

		var editor = CKEDITOR.instances[uid] || null;
		if (editor) {
			return false;
		}

		config = this.setConfig(config);

		if (!config['height']) {
			config['height'] = parseInt(el.offsetHeight) || 200;
		}

		if (config['addExternalPlugins']) {
			for (var name in config['addExternalPlugins']) {
				var script = config['addExternalPlugins'][name];
				if (script) {
					CKEDITOR.plugins.addExternal(name, config['assetsUrl'] + script, '');
				}
			}
		}

		if (config['addExternalSkin']) {
			for (var name in config['addExternalSkin']) {
				var skin = config['addExternalSkin'][name];
				if (skin && name == config.skin) {
					config.skin = skin + ',' + config['assetsUrl'] + skin;
				}
			}
		}

		/* compact mode */
		if (config['editorCompact']) {
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

		/* fix contenteditable margin */
		CKEDITOR.addCss('body.cke_editable.cke_show_borders  { margin: 10px; }');

		/*  */
		CKEDITOR.on("instanceReady", function (ev) {

			/* add load class */
			ev.editor.element.$.classList.add("modckeditor-load");
		});

	},

	registerDrop: function (editor) {
		if (!editor.container || !editor.container.$) {
			return false;
		}

		var ddTarget = new Ext.Element(editor.container.$),
			ddTargetEl = ddTarget.dom;

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
					var element = '<' + type + ' src="/' + path + '" controls="">';
					editor.insertHtml(element + "\n");
					editor.focus();
				}
			},
			devtags: function (text) {
				text = "<pre><devtags>\n" + text + "\n</devtags></pre>\n";
				editor.insertHtml(text);
				editor.focus();
			},
			block: function (text) {
				editor.insertHtml(text + "\n");
				editor.focus();
			},
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
				var v = '',
					win = false,
					block = false;

				if (editor.mode != 'wysiwyg') {
					return false;
				}

				console.log(data);

				fakeDiv && fakeDiv.remove();

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
					case 'block':
						block = true;
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
						cfg: {onInsert: insert.devtags},
						iframe: true,
						onInsert: insert.devtags
					});
				} else if (block && MODx.loadInsertBlock) {

					/* TODO block */
					console.log('block');
					console.log(data.node);

					MODx.loadInsertBlock({
						pk: data.node.attributes.pk,
						classKey: data.node.attributes.classKey,
						name: data.node.attributes.name,
						output: v,
						ddTargetEl: ddTargetEl,
						cfg: {onInsert: insert.block},
						iframe: true,
						onInsert: insert.block
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


modckeditor.loadEditorForFields = function (fields) {
	if (modckeditor.config == undefined) {
		return false;
	}

	modckeditor.config['additional_editor_fields'] = fields || modckeditor.config['additional_editor_fields'] || [];
	modckeditor.config['additional_editor_fields'].filter(function (field) {

		new modckeditor.ckeditor({
			selector: '#' + field,
			droppable: false
		});

		new modckeditor.ckeditor({
			selector: '.' + field,
			droppable: false
		});

	});

};


MODx.loadRTE = function (id) {
	if (modckeditor.config == undefined) {
		return false;
	}
	new modckeditor.ckeditor({
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
