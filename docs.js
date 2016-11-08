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

            if (location.hash) {
                const hash = location.hash.slice(1).split('/');
                $selectVersion.val(hash[0]);
                $.get(`docs/${hash[0]}/${hash[1]}`).done(markdown => $dynamicContent.html(marked(markdown)));
            } else {
                $selectVersion.trigger('change');
            }
        });
    },

    addEvents() {
        $selectVersion.on('change', event => {
            $.get(`docs/${event.target.value}/readme.md`).done(markdown => $dynamicContent.html(marked(markdown)));
            this.setHash('#' + event.target.value + '/readme.md');
        });

        $dynamicContent.on('click', 'a', event => {
            const $link = $(event.target);
            const version = $selectVersion.val();
            const file = $link.attr('href');

            if (!file.includes('.md')) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            $.get(`docs/${version}/${file}`).done(markdown => $dynamicContent.html(marked(markdown)));
            this.setHash(`#${version}/${file}`);
        })
    },

    setHash(hash) {
        if (history.pushState) {
            history.pushState(null, null, hash);
        } else {
            location.hash = hash;
        }
    }
};