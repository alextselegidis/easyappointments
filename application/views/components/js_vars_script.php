<script>
    window.vars = (function () {
        const vars = <?= json_encode(script_vars()) ?>;

        return (key) => {
            if (!key) {
                return vars;
            }

            return vars[key] || undefined;
        };
    })();
</script>

