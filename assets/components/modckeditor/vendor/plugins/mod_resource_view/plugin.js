CKEDITOR.plugins.add('mod_resource_view', {
	lang: 'en,ru',
	hidpi: true,
	icons: 'mod_resource_view',
	init: function (editor) {

		editor.ui.addButton && editor.ui.addButton('mod_resource_view', {
			label: editor.lang.mod_resource_view.resource_view,
			command: 'mod_resource_view',
			toolbar: 'about,100'
		});
		editor.addCommand('mod_resource_view', {
			modes: {wysiwyg: 1, source: 1},
			canUndo: false,
			readOnly: 1,
			exec: function (editor) {

				var hash = modckeditor.tools.Hash.get();
				if (!hash['id']) {
					return;
				}

				var url = String.format('/mod_resource_view?id={0}', hash['id']);


				console.log(url);

				window.open(url);

				return true;
			}
		});
	}
});