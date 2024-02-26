<?php
/**
 * @var string $google_analytics_code
 */
?>

<?php if (substr($google_analytics_code ?? '', 0, 2) === 'UA'): ?>
    <script>
        (function (i, s, o, g, r, a, m) {
            i["GoogleAnalyticsObject"] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, "script", "//www.google-analytics.com/analytics.js", "ga");
        ga("create", "<?= e($google_analytics_code) ?>", "auto");
        ga("send", "pageview");
    </script>
<?php endif; ?>

<?php if (substr($google_analytics_code ?? '', 0, 2) === 'G-'): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=' . $google_analytics_code . '"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag("js", new Date());
        gtag("config", "<?= e($google_analytics_code) ?>");
    </script>
<?php endif; ?>

