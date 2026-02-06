<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
/**
 * @var Throwable $exception
 * @var string $message
 */
?>

<div style="
    border: 1px solid #dfdfdf; 
    margin: 0 0 10px 0; 
    padding: 15px;
    font-size: 14px;
">

    <h4>
        An uncaught Exception was encountered
    </h4>

    <h6>
        Type
    </h6>

    <p>
        <?= get_class($exception) ?>
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
        <?= $exception->getFile() ?>
    </p>

    <h6>
        Line Number
    </h6>

    <p>
        <?= $exception->getLine() ?>
    </p>

    <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === true): ?>

        <h6>
            Backtrace
        </h6>

        <?php foreach ($exception->getTrace() as $error): ?>

            <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

                <p style="margin-left:10px">
                    File: <?= $error['file'] ?><br>
                    Line: <?= $error['line'] ?><br>
                    Function: <?= $error['function'] ?>
                </p>
            <?php endif; ?>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
