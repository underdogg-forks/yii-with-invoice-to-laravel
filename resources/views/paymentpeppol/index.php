<?php
/**
 * @var array $paymentPeppols
 */
ob_start();
?>
<h1>Payment Peppol Records</h1>

<a href="/paymentpeppol/add/1" class="btn btn-primary">Add New Payment Peppol</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice ID</th>
            <th>Provider</th>
            <th>Auto Reference</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($paymentPeppols) || count($paymentPeppols) === 0): ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($paymentPeppols as $paymentPeppol): ?>
                <tr>
                    <td><?= htmlspecialchars($paymentPeppol->id) ?></td>
                    <td><?= htmlspecialchars($paymentPeppol->inv_id) ?></td>
                    <td><?= htmlspecialchars($paymentPeppol->provider) ?></td>
                    <td><?= htmlspecialchars($paymentPeppol->auto_reference) ?></td>
                    <td>
                        <a href="/paymentpeppol/edit/<?= $paymentPeppol->id ?>" class="btn btn-primary">Edit</a>
                        <form method="POST" action="/paymentpeppol/delete/<?= $paymentPeppol->id ?>" style="display:inline;">
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
$title = 'Payment Peppol - Index';
include __DIR__ . '/../layout.php';
?>
