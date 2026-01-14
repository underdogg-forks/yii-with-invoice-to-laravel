<?php
/**
 * @var array $unitPeppols
 */
ob_start();
?>
<h1>Unit Peppol Records</h1>

<a href="<?= route('unitpeppol.add', ['unit_id' => 1]) ?>" class="btn btn-primary">Add New Unit Peppol</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Unit ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($unitPeppols) || count($unitPeppols) === 0): ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($unitPeppols as $unitPeppol): ?>
                <tr>
                    <td><?= htmlspecialchars($unitPeppol->id) ?></td>
                    <td><?= htmlspecialchars($unitPeppol->unit_id) ?></td>
                    <td><?= htmlspecialchars($unitPeppol->code) ?></td>
                    <td><?= htmlspecialchars($unitPeppol->name) ?></td>
                    <td>
                        <a href="<?= route('unitpeppol.edit', ['id' => $unitPeppol->id]) ?>" class="btn btn-primary">Edit</a>
                        <form method="POST" action="<?= route('unitpeppol.delete', ['id' => $unitPeppol->id]) ?>" style="display:inline;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$title = 'Unit Peppol - Index';
include __DIR__ . '/../layout.php';
?>
