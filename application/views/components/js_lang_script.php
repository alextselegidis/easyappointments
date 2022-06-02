<script>
    window.lang = (function () {
        const lang = <?= json_encode($this->lang->language) ?>;

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

