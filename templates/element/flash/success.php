<div class="alert alert-success-custom" onclick="this.style.display='none'">
    <?= ($params['escape'] ?? true) ? h($message) : $message ?>
</div>