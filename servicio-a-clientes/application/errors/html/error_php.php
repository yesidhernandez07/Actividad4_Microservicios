<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div style="border:1px solid #ccc;padding:15px;margin:10px;font-family:Arial;">
    <?php if (isset($heading)): ?>
        <h3><?php echo $heading; ?></h3>
    <?php endif; ?>

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
</div>