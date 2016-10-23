Ext.ns('modckeditor.tools');


modckeditor.tools.getAssetsUrl = function () {
	var url = MODx.config['assets_url'];

	return url;
};


modckeditor.tools.getFileBrowseUrl = function () {
	var url = MODx.config['manager_url'] + 'index.php';
	var query = {
		a: MODx.action['browser'],
		source: MODx.config['modckeditor_source_default'] || MODx.config['default_media_source']
	};

	return url + '?' + Ext.urlEncode(query);
};


modckeditor.tools.getFileUploadUrl = function (type) {
	var url = modckeditor.config['connector_url'];
	var query = {
		action: 'mgr/browser/file/upload',
		path: '/',
		wctx: MODx.ctx,
		HTTP_MODAUTH: MODx.siteId,
		type: type || '',
		source: MODx.config['modckeditor_source_default'] || MODx.config['default_media_source'],
	};

	var id = modckeditor.tools.getResourceField('id');
	if (id) {
		query['path'] = '/' + id + '/'
	}

	return url + '?' + Ext.urlEncode(query);
};


modckeditor.tools.getUpdateButton = function () {
	var pageButtons = MODx.activePage ? MODx.activePage.buttons : {};

	for (var button in pageButtons) {
		var process = pageButtons[button].process;
		if (!process)
			continue;
		if (process.split('/').pop() == 'update') {
			return pageButtons[button];
		}
	}

	return null;
};


modckeditor.tools.getResourceField = function (field) {
	var config = MODx.activePage ? MODx.activePage.config : {};
	var record = config.record ? config.record : {};

	if (modckeditor != undefined) {
		record = modckeditor.config.resource || {};
	}

	if (!field) {
		return record;
	}

	return record[field];
};


modckeditor.tools.getEditorCompact = function (config) {
	var compact = false;
	var component = config.component;

	if (config.editorCompact) {
		compact = config.editorCompact[component];
	}

	return compact;
};


modckeditor.tools.inArray = function (needle, haystack) {
	for (key in haystack) {
		if (haystack[key] == needle) return true;
	}

	return false;
};


modckeditor.tools.empty = function (value) {
	return (typeof(value) == 'undefined' || value == 0 || value === null || value === false || (typeof(value) == 'string' && value.replace(/\s+/g, '') == '') || (typeof(value) == 'object' && value.length == 0));
};
