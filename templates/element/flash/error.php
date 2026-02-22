<div class="alert alert-error-custom" onclick="this.style.display='none'">
    <?= ($params['escape'] ?? true) ? h($message) : $message ?>
</div>