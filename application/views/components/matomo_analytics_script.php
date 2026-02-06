<?php
/**
 * @var string $matomo_analytics_url
 * @var string $matomo_analytics_site_id
 */
?>

<?php if (!empty($matomo_analytics_url)): ?>

    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "<?= e($matomo_analytics_url) ?>";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '<?= e($matomo_analytics_site_id) ?>']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>

    <noscript>
        <p><img src="<?= e($matomo_analytics_url) ?>matomo.php?idsite=<?= e(
    $matomo_analytics_site_id,
) ?>&amp;rec=1" style="border:0;" alt=""/></p>
    </noscript>

<?php endif; ?>
