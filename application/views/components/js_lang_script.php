<script <?= vars('attributes') ?>>
    window.lang = (function () {
        const lang = <?= json_encode($this->lang->language) ?>;

        return (key) => {
            if (!key) {
                return lang;
            }

            return lang[key] || undefined;
        };
    })();
</script>

