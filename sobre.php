<?php
// sobre.php
require_once 'db.php';
include 'header.php';

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['criar']) || isset($_POST['atualizar'])) {
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $id = $_POST['id'] ?? null;
        
        if (empty($titulo) || empty($descricao)) {
            $erro = 'Título e descrição são obrigatórios!';
        } else {
            $foto_path = null;
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $nome_arquivo = $_FILES['foto']['name'];
                $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
                
                if (in_array($extensao, $extensoes_permitidas)) {
                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0755, true);
                    }
                    
                    $novo_nome = uniqid() . '.' . $extensao;
                    $caminho_upload = 'uploads/' . $novo_nome;
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_upload)) {
                        $foto_path = $caminho_upload;
                    } else {
                        $erro = 'Erro ao fazer upload da imagem.';
                    }
                } else {
                    $erro = 'Tipo de arquivo não permitido. Use: jpg, jpeg, png, gif, webp';
                }
            }
            
            if (empty($erro)) {
                try {
                    if ($id) {
                        if ($foto_path) {
                            $stmt = $pdo->prepare("SELECT foto_path FROM sobre WHERE id = ?");
                            $stmt->execute([$id]);
                            $registro = $stmt->fetch();
                            if ($registro['foto_path'] && file_exists($registro['foto_path'])) {
                                unlink($registro['foto_path']);
                            }
                            
                            $sql = "UPDATE sobre SET titulo = ?, descricao = ?, foto_path = ? WHERE id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([$titulo, $descricao, $foto_path, $id]);
                        } else {
                            $sql = "UPDATE sobre SET titulo = ?, descricao = ? WHERE id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([$titulo, $descricao, $id]);
                        }
                        $mensagem = 'Registro atualizado com sucesso!';
                    } else {
                        $sql = "INSERT INTO sobre (titulo, descricao, foto_path) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$titulo, $descricao, $foto_path]);
                        $mensagem = 'Registro criado com sucesso!';
                    }
                } catch (PDOException $e) {
                    $erro = 'Erro ao salvar: ' . $e->getMessage();
                }
            }
        }
    }
}

if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    try {
        $stmt = $pdo->prepare("SELECT foto_path FROM sobre WHERE id = ?");
        $stmt->execute([$id]);
        $registro = $stmt->fetch();
        
        if ($registro) {
            if ($registro['foto_path'] && file_exists($registro['foto_path'])) {
                unlink($registro['foto_path']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM sobre WHERE id = ?");
            $stmt->execute([$id]);
            $mensagem = 'Registro excluído com sucesso!';
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao excluir: ' . $e->getMessage();
    }
}

$editando = null;
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM sobre WHERE id = ?");
    $stmt->execute([$id]);
    $editando = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM sobre ORDER BY created_at DESC");
$registros = $stmt->fetchAll();
?>

<h2>Sobre o Site - Gerenciamento</h2>

<?php if ($mensagem): ?>
    <p><strong><?php echo $mensagem; ?></strong></p>
<?php endif; ?>

<?php if ($erro): ?>
    <p><strong>Erro: <?php echo $erro; ?></strong></p>
<?php endif; ?>

<h3><?php echo $editando ? 'Editar Registro' : 'Novo Registro'; ?></h3>
<form method="POST" enctype="multipart/form-data">
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?php echo $editando['id']; ?>">
    <?php endif; ?>
    
    <p>
        <label>Título: *<br>
            <input type="text" name="titulo" value="<?php echo $editando ? htmlspecialchars($editando['titulo']) : ''; ?>" size="50" maxlength="150" required>
        </label>
    </p>
    
    <p>
        <label>Descrição: *<br>
            <textarea name="descricao" rows="6" cols="50" required><?php echo $editando ? htmlspecialchars($editando['descricao']) : ''; ?></textarea>
        </label>
    </p>
    
    <p>
        <label>Foto:<br>
            <input type="file" name="foto" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
        </label>
        <?php if ($editando && $editando['foto_path'] && file_exists($editando['foto_path'])): ?>
            <br>Foto atual: <img src="<?php echo htmlspecialchars($editando['foto_path']); ?>" width="100">
        <?php endif; ?>
    </p>
    
    <p>
        <?php if ($editando): ?>
            <button type="submit" name="atualizar">Atualizar</button>
            <a href="sobre.php">Cancelar</a>
        <?php else: ?>
            <button type="submit" name="criar">Criar</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Registros Cadastrados</h3>
<?php if ($registros): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>Foto</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($registros as $reg): ?>
            <tr>
                <td><?php echo $reg['id']; ?></td>
                <td><?php echo htmlspecialchars($reg['titulo']); ?></td>
                <td><?php echo htmlspecialchars(substr($reg['descricao'], 0, 50)) . '...'; ?></td>
                <td>
                    <?php if ($reg['foto_path'] && file_exists($reg['foto_path'])): ?>
                        <img src="<?php echo htmlspecialchars($reg['foto_path']); ?>" width="50">
                    <?php else: ?>
                        Sem foto
                    <?php endif; ?>
                </td>
                <td><?php echo date('d/m/Y', strtotime($reg['created_at'])); ?></td>
                <td>
                    <a href="?editar=<?php echo $reg['id']; ?>">Editar</a> |
                    <a href="?excluir=<?php echo $reg['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nenhum registro cadastrado.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>