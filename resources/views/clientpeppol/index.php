<?php
/**
 * @var array $clientPeppols
 */
ob_start();
?>
<h1>Client Peppol Records</h1>

<a href="<?= route('clientpeppol.add', ['client_id' => 1]) ?>" class="btn btn-primary">Add New Client Peppol</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Client ID</th>
            <th>Endpoint ID</th>
            <th>Buyer Reference</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientPeppols) || count($clientPeppols) === 0): ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientPeppols as $clientPeppol): ?>
                <tr>
                    <td><?= htmlspecialchars($clientPeppol->id) ?></td>
                    <td><?= htmlspecialchars($clientPeppol->client_id) ?></td>
                    <td><?= htmlspecialchars($clientPeppol->endpointid) ?></td>
                    <td><?= htmlspecialchars($clientPeppol->buyer_reference) ?></td>
                    <td>
                        <a href="<?= route('clientpeppol.view', ['id' => $clientPeppol->id]) ?>" class="btn btn-secondary">View</a>
                        <a href="<?= route('clientpeppol.edit', ['id' => $clientPeppol->id]) ?>" class="btn btn-primary">Edit</a>
                        <form method="POST" action="<?= route('clientpeppol.delete', ['id' => $clientPeppol->id]) ?>" style="display:inline;">
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
$title = 'Client Peppol - Index';
include __DIR__ . '/../layout.php';
?>
