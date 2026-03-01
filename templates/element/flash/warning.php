<div class="alert alert-warning-custom" onclick="this.style.display='none'">
    <?= ($params['escape'] ?? true) ? h($message) : $message ?>
</div>