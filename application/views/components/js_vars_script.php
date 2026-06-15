<script>
    window.varsStore = <?= json_encode(script_vars()) ?>;
    window.vars = (function (store) {
        return (key) => {
            if (!key) {
                return store;
            }

            return store[key];
        };
    })(window.varsStore);
</script>

