Ext.ns('modckeditor.tools');


modckeditor.tools.getComponentNameBySelector = function (selector) {

	return selector.replace(/^[.#]/g, '');
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


modckeditor.tools.inArray = function (needle, haystack) {
	for (key in haystack) {
		if (haystack[key] == needle) return true;
	}

	return false;
};


modckeditor.tools.keyExists = function (key, array) {
	if (array instanceof Array) {
		return array.indexOf(key) < 0;
	}
	else if (typeof(array) == 'object') {
		return key in array;
	}
};


modckeditor.tools.empty = function (value) {
	return (typeof(value) == 'undefined' || value == 0 || value === null || value === false || (typeof(value) == 'string' && value.replace(/\s+/g, '') == '') || (typeof(value) == 'object' && value.length == 0));
};


modckeditor.tools.Hash = {
	get: function () {
		var vars = {},
			hash, splitter, hashes;
		if (!this.oldbrowser()) {
			var pos = window.location.href.indexOf('?');
			hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
			splitter = '&';
		} else {
			hashes = decodeURIComponent(window.location.hash.substr(1));
			splitter = '/';
		}

		if (hashes.length == 0) {
			return vars;
		} else {
			hashes = hashes.split(splitter);
		}

		for (var i in hashes) {
			if (hashes.hasOwnProperty(i)) {
				hash = hashes[i].split('=');
				if (typeof hash[1] == 'undefined') {
					vars['anchor'] = hash[0];
				} else {
					vars[hash[0]] = hash[1];
				}
			}
		}
		return vars;
	},

	set: function (vars) {
		var hash = '';
		for (var i in vars) {
			if (vars.hasOwnProperty(i)) {
				hash += '&' + i + '=' + vars[i];
			}
		}

		if (!this.oldbrowser()) {
			if (hash.length != 0) {
				hash = '?' + hash.substr(1);
			}
			window.history.pushState(hash, '', document.location.pathname + hash);
		} else {
			window.location.hash = hash.substr(1);
		}
	},

	add: function (key, val) {
		var hash = this.get();
		hash[key] = val;
		this.set(hash);
	},

	remove: function (key) {
		var hash = this.get();
		delete hash[key];
		this.set(hash);
	},

	clear: function () {
		this.set({});
	},

	oldbrowser: function () {
		return !(window.history && history.pushState);
	}
};
