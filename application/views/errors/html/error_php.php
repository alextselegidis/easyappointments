<?php
/**
 * @var string $severity
 * @var string $message
 * @var string $filepath
 * @var string $line
 */
?>

<div style="
    border: 1px solid #dfdfdf; 
    margin: 0 0 10px 0; 
    padding: 15px;
    font-size: 14px;
">

    <h4>
        A PHP Error was encountered
    </h4>

    <h6>
        Severity
    </h6>

    <p>
        <?= $severity ?>
    </p>

    <h6>
        Message
    </h6>

    <p>
        <?= $message ?>
    </p>

    <h6>
        Filename
    </h6>

    <p>
        <?= $filepath ?>
    </p>

    <h6>
        Line Number
    </h6>

    <p>
        <?= $line ?>
    </p>
</div>
