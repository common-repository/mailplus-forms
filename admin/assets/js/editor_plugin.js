tinymce.PluginManager.add('spotler', function (editor, url) {
    if (!editor) {
        return;
    }

    editor.addCommand('spotler', function () {
        showDialog();
    });

    editor.addButton('spotler', {
        title: 'Add Spotler Mail+ form',
        cmd: 'spotler',
        image: url.replace('/js', '/images/') + 'spotler-icon.png'
    });

    function showDialog() {
        let win = editor.windowManager.open({
            title: 'Add a Spotler Mail+ form',
            body: [{
                type: 'form',
                layout: 'flex',
                direction: 'column',

                name: 'spotler_form',
                defaults: {
                    type: 'formItem',
                    autoResize: "overflow",
                    flex: 1,
                    minWidth: 350
                },
                items: [
                    {
                        name: 'spotler_select',
                        label: 'Choose a Spotler Mail+ form to add',
                        type: 'listbox',
                        width: 250,
                        values: []
                    }
                ]
            }],
            onSubmit: onSubmitForm
        });

        let listbox = win.find('#spotler_select')[0];
        jQuery.getJSON(ajaxurl + '?action=spotler_get_forms', function (data) {
            listbox.menu = null;
            jQuery.each(data, function (index, item) {
                listbox.settings.values.push({text: item.name, value: item.id});
            });
        });

        function onSubmitForm() {
            const data = win.toJSON();

            let displayValue = '[spotler formid=' + data.spotler_select + ']';
            editor.selection.setContent(displayValue);
        }
    }
})();