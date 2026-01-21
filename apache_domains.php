<?php
include 'sidebar.php';

// Récupérer la liste des fichiers de configuration Apache
$sites_available_dir = '/etc/apache2/sites-available/';
$sites_enabled_dir = '/etc/apache2/sites-enabled/';

$domains = [];

if (is_dir($sites_available_dir)) {
    $files = scandir($sites_available_dir);
    
    // Pour chaque fichier du répertoire
    foreach ($files as $file) {
        // Ignorer . et .. , garder seulement les .conf
        if ($file === '.' || $file === '..' || strpos($file, '.conf') === false) {
            continue;
        }
        
        // Extraire le nom du domaine (enlever .conf)
        // Exemple: "example.com.conf" → "example.com"
        $domain_name = str_replace('.conf', '', $file);
        
        // Vérifier si le domaine est activé (s'il existe dans sites-enabled)
        $is_enabled = file_exists($sites_enabled_dir . $file);
        
        // Ajouter le domaine à la liste
        $domains[] = [
            'name' => $domain_name,
            'file' => $file,
            'enabled' => $is_enabled
        ];
    }
}

// Trier les domaines par nom alphabétiquement
// usort() = trier un tableau
// strcmp() = comparer deux textes alphabétiquement
usort($domains, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Afficher les messages de statut
$message = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message_type = isset($_GET['message']) ? $_GET['message'] : '';
    
    // Messages de succès
    if ($status === 'enabled') {
        $message = '<p style="color: green; font-weight: bold;">✓ Domaine activé avec succès!</p>';
    } elseif ($status === 'disabled') {
        $message = '<p style="color: green; font-weight: bold;">✓ Domaine désactivé avec succès!</p>';
    } 
    // Messages d'erreur
    elseif ($status === 'error') {
        $error_messages = [
            'action_invalide' => 'Erreur: Action invalide (enable/disable attendu)',
            'domaine_invalide' => 'Erreur: Nom de domaine invalide (caractères non autorisés)',
            'fichier_introuvable' => 'Erreur: Fichier de configuration introuvable',
            'activation_echouee' => 'Erreur: Activation du domaine échouée',
            'desactivation_echouee' => 'Erreur: Désactivation du domaine échouée',
            'donnees_manquantes' => 'Erreur: Données manquantes (action ou domaine)',
        ];
        
        // Vérifier d'abord si c'est une exception
        if (strpos($message_type, 'exception_') === 0) {
            $exception_msg = str_replace('exception_', '', $message_type);
            $error_text = 'Erreur technique: ' . urldecode($exception_msg);
        } else {
            // Sinon, chercher dans les messages d'erreur connus
            $error_text = isset($error_messages[$message_type]) 
                ? $error_messages[$message_type] 
                : 'Erreur: Une erreur est survenue lors de l\'opération';
        }
        
        $message = '<p style="color: red; font-weight: bold;">✗ ' . htmlspecialchars($error_text) . '</p>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion des domaines Apache</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gestion des domaines Apache</h1>
    <h2>test be</h2>
    <?php echo $message; ?>
    
    <?php if (empty($domains)): ?>
        <p>Aucun domaine trouvé dans /etc/apache2/sites-available/</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Domaine</th>
                    <th>Fichier de configuration</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($domains as $domain): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($domain['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($domain['file']); ?></td>
                        <td>
                            <?php echo $domain['enabled'] ? 'Activé' : 'Désactivé'; ?>
                        </td>
                        <td>
                            <?php if ($domain['enabled']): ?>
                                <form method="POST" action="process_apache_domains.php" style="display:inline;">
                                    <input type="hidden" name="action" value="disable">
                                    <input type="hidden" name="domain" value="<?php echo htmlspecialchars($domain['name']); ?>">
                                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce domaine?');">Désactiver</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="process_apache_domains.php" style="display:inline;">
                                    <input type="hidden" name="action" value="enable">
                                    <input type="hidden" name="domain" value="<?php echo htmlspecialchars($domain['name']); ?>">
                                    <button type="submit">Activer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
