<?php
/**
 * @var object|null $unitPeppol
 * @var int $unit_id
 * @var string $actionRoute
 */
ob_start();
?>
<h1><?= $unitPeppol ? 'Edit' : 'Add' ?> Unit Peppol</h1>

<form method="POST" action="<?= $actionRoute ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="code">Code (max 3 characters)</label>
        <input type="text" id="code" name="code" maxlength="3" value="<?= htmlspecialchars($unitPeppol->code ?? '') ?>" required>
        <small>UN/CEFACT unit code</small>
    </div>
    
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" maxlength="120" value="<?= htmlspecialchars($unitPeppol->name ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($unitPeppol->description ?? '') ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-success">Save</button>
    <a href="/unitpeppol" class="btn btn-secondary">Cancel</a>
</form>

<?php
$content = ob_get_clean();
$title = $unitPeppol ? 'Edit Unit Peppol' : 'Add Unit Peppol';
include __DIR__ . '/../layout.php';
?>
