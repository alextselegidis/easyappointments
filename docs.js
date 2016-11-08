'use strict';

import marked from 'marked';

const $selectVersion = $('.select-version');
const $dynamicContent = $('.dynamic-content');

module.exports = {
    initialize() {
        if (!$('#docs').length) {
            return;
        }

        this.addEvents();

        $.getJSON('docs/versions.json').done(arhive => {
            $selectVersion.empty();
            arhive.forEach(entry => {
                $selectVersion.append(new Option(`v${entry.version}`, entry.version, entry.default, entry.default));
            });
        });

        $selectVersion.trigger('change');
    },

    addEvents() {
        $selectVersion.on('change', event => {
            $.get(`docs/${event.target.value}/readme.md`).done(markdown => $dynamicContent.html(marked(markdown)));
        });

        $dynamicContent.on('click', 'a', event => {
            const $link = $(event.target);

            if (!$link.attr('href').includes('.md')) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const file = $link.attr('href'); // remove hasttag

            $.get(`docs/${$selectVersion.val()}/${file}`).done(markdown => $dynamicContent.html(marked(markdown)));
        })
    }
};