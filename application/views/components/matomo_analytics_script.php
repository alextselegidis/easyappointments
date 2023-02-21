<?php
/**
 * @var string $matomo_analytics_url
 */
?>

<?php if ( ! empty($matomo_analytics_url)): ?>

    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "<?= $matomo_analytics_url ?>";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>

    <noscript>
        <p><img src="<?= $matomo_analytics_url ?>matomo.php?idsite=1&amp;rec=1" style="border:0;" alt=""/></p>
    </noscript>

<?php endif ?>
