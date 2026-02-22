<div class="alert alert-notice" onclick="this.style.display='none'">
    <?= ($params['escape'] ?? true) ? h($message) : $message ?>
</div>