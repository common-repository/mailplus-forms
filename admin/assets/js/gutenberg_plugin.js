(function (blocks, components, i18n, element) {
    let el = element.createElement;
    let registerBlockType = blocks.registerBlockType;
    let SelectControl = components.SelectControl;
    let options = [{label: 'Select a form', value: 0}];

    jQuery.getJSON(ajaxurl + '?action=spotler_get_forms', function (data) {
        jQuery.each(data, function (index, item) {
            options.push({label: item.name, value: item.id});
        });
    });

    registerBlockType('mailplus-forms/form', {
        title: 'Spotler Mail+ Forms',
        icon: el(
            'svg',
            { width: 24, height: 24},
            el(
                'image',
                {
                    href: '/wp-content/plugins/mailplus-forms/admin/assets/images/spotler.png',
                    width: 24,
                    height: 24
                }
            )
        ),
        category: 'common',
        attributes: {
            selectedOption: {
                type: 'string',
                default: 0
            },
        },
        edit: function (props) {
            let onSelectChange = function (value) {
                props.setAttributes({ selectedOption: value });
            };

            return el(
                'div',
                { className: props.className },
                el(SelectControl, {
                    label: 'Spotler Mail+ form:',
                    value: props.attributes.selectedOption,
                    options: options,
                    onChange: onSelectChange,
                })
            );
        },
        save: function (props) {
            return '[spotler formid="'+props.attributes.selectedOption+'"]';
        },
    });
})(
    window.wp.blocks,
    window.wp.components,
    window.wp.i18n,
    window.wp.element
);