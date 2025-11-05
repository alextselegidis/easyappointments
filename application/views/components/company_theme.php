<?php
/**
 * @var string $company_color
 */
?>

<?php if (!empty($company_color) && $company_color !== DEFAULT_COMPANY_COLOR): ?>
<style type="text/css">
    :root {
        --company-color: <?= $company_color; ?>;
    }
</style>  
<?php endif; ?>