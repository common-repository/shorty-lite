(function () {
    tinymce.create('tinymce.plugins.shorty', {
        init: function (editor, url) {
            editor.addCommand('srty_shorty_tinymce_popup', function () {
                editor.windowManager.open({
                    title: 'Insert Shorty Link',
                    file: ajaxurl + '?action=srty_shorty_tinymce_form', // file that contains HTML for our modal window
                    width: 600 + parseInt(editor.getLang('button.delta_width', 0)), // size of our window
                    height: 500 + parseInt(editor.getLang('button.delta_height', 0)), // size of our window
                    inline: 1
                }, {
                    plugin_url: url
                });
            });
            editor.addButton('srty_shorty_tinymce_button', {
                title: 'Insert Shorty Link',
                cmd: 'srty_shorty_tinymce_popup',
                image: url + '/../images/favicon-20x20.png'
            });
        },
        getInfo: function () {
            return {
                longname: 'srty_shorty_tinymce',
                author: 'Kreydle Sdn Bhd',
                authorurl: 'http://www.kreydle.com',
                infourl: 'http://www.kreydle.com',
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });

    // Register plugin
    // first parameter is the button ID and must match ID elsewhere
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('srty_shorty_tinymce', tinymce.plugins.shorty);
})();
