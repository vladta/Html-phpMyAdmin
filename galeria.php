<?php
require_once 'db.php';
include 'header.php';

$sql = "SELECT titulo, foto_path FROM sobre WHERE foto_path IS NOT NULL AND foto_path != '' ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$fotos = $stmt->fetchAll();
?>

<h2>Galeria de Fotos</h2>

<?php if ($fotos): ?>
    <p>Clique nas imagens para ampliar.</p>
    <?php foreach ($fotos as $foto): ?>
        <?php if (file_exists($foto['foto_path'])): ?>
            <a href="<?php echo htmlspecialchars($foto['foto_path']); ?>" target="_blank">
                <img src="<?php echo htmlspecialchars($foto['foto_path']); ?>" 
                     alt="<?php echo htmlspecialchars($foto['titulo']); ?>" 
                     title="<?php echo htmlspecialchars($foto['titulo']); ?>"
                     width="200" height="150">
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma foto cadastrada ainda. Visite a página "Sobre o Site" para adicionar fotos.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>