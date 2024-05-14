<script>
    window.lang = (function () {
        const lang = <?= json_encode(html_vars('language')) ?>;

        return (key) => {
            if (!key) {
                return lang;
            }

            if (!lang[key]) {
                console.error(`Cannot find translation for requested key: "${key}"`);
                return key;
            }

            return lang[key];
        };
    })();
</script>

