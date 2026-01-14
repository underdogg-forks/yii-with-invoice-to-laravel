<?php
/**
 * @var object|null $paymentPeppol
 * @var int $inv_id
 * @var string $actionRoute
 */
ob_start();
?>
<h1><?= $paymentPeppol ? 'Edit' : 'Add' ?> Payment Peppol</h1>

<form method="POST" action="<?= $actionRoute ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="provider">Provider</label>
        <input type="text" id="provider" name="provider" maxlength="20" value="<?= htmlspecialchars($paymentPeppol->provider ?? '') ?>" required>
    </div>
    
    <?php if ($paymentPeppol): ?>
    <div class="form-group">
        <label>Auto Reference</label>
        <input type="text" value="<?= htmlspecialchars($paymentPeppol->auto_reference ?? '') ?>" readonly disabled>
        <small>Auto-generated timestamp</small>
    </div>
    <?php endif; ?>
    
    <button type="submit" class="btn btn-success">Save</button>
    <a href="/paymentpeppol" class="btn btn-secondary">Cancel</a>
</form>

<?php
$content = ob_get_clean();
$title = $paymentPeppol ? 'Edit Payment Peppol' : 'Add Payment Peppol';
include __DIR__ . '/../layout.php';
?>
