<?php
/**
 * TODO: Calculate proper light and dark Controllers
 * 
 * @var string $company_color
 */
?>

<?php if (!empty($company_color) && $company_color !== DEFAULT_COMPANY_COLOR): ?>
<style type="text/css">
    :root {
        --company-color: <?= $company_color; ?>;
        --company-color-light: #e6d8d8;
        --company-color-dark: #3f3e3eff;
    }
</style>  
<?php endif; ?>