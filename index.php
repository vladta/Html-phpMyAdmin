<?php
require_once 'db.php';
include 'header.php';

$sql = "SELECT * FROM sobre ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->query($sql);
$ultimo = $stmt->fetch();
?>

<h2>Bem-vindo à meu Site!</h2>

<?php if ($ultimo): ?>
    <h3><?php echo htmlspecialchars($ultimo['titulo']); ?></h3>
    <p><?php echo nl2br(htmlspecialchars($ultimo['descricao'])); ?></p>
    
    <?php if ($ultimo['foto_path'] && file_exists($ultimo['foto_path'])): ?>
        <img src="<?php echo htmlspecialchars($ultimo['foto_path']); ?>" alt="Foto da oficina" width="400">
    <?php endif; ?>
    
    <p><small>Publicado em: <?php echo date('d/m/Y H:i', strtotime($ultimo['created_at'])); ?></small></p>
<?php else: ?>
    <p>Nenhum conteúdo cadastrado ainda. Visite a página "Sobre o Site" para adicionar conteúdo.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>