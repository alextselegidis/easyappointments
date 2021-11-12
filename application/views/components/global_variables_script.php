<?php
/**
 * @var string $attributes
 * @var array $global_variables
 */
?>
<script <?= $attributes ?>>
    const GlobalVariables = <?= json_encode($global_variables ?? []) ?>
</script>

