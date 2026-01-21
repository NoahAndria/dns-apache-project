<?php
// Traiter les demandes d'activation/désactivation de domaines Apache

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['domain'])) {
    $action = $_POST['action'];
    $domain = $_POST['domain'];
    
    // Valider l'action et le domaine pour éviter les injections
    if (!in_array($action, ['enable', 'disable'])) {
        header('Location: apache_domains.php?status=error&message=action_invalide');
        exit;
    }
    
    // Valider le nom du domaine (alphanumériques, tirets, points)
    if (!preg_match('/^[a-zA-Z0-9\.\-_]+$/', $domain)) {
        header('Location: apache_domains.php?status=error&message=domaine_invalide');
        exit;
    }
    
    try {
        if ($action === 'enable') {
            // Exécuter la commande a2ensite
            $command = sprintf('sudo a2ensite %s.conf 2>&1', escapeshellarg($domain));
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);
            
            $output_text = implode(' ', $output);
            error_log("DEBUG a2ensite: return_var=$return_var, output=$output_text");
            
            if ($return_var === 0 || strpos($output_text, 'already enabled') !== false) {
                // Recharger Apache
                exec('sudo systemctl reload apache2 2>&1', $reload_output, $reload_var);
                header('Location: apache_domains.php?status=enabled&message=domaine_active');
            } else {
                // Vérifier si c'est un fichier introuvable
                if (strpos($output_text, 'No such file or directory') !== false) {
                    header('Location: apache_domains.php?status=error&message=fichier_introuvable');
                } else {
                    header('Location: apache_domains.php?status=error&message=exception_' . urlencode($output_text));
                }
            }
        } else {
            // Exécuter la commande a2dissite
            $command = sprintf('sudo a2dissite %s.conf 2>&1', escapeshellarg($domain));
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);
            
            $output_text = implode(' ', $output);
            error_log("DEBUG a2dissite: return_var=$return_var, output=$output_text");
            
            if ($return_var === 0 || strpos($output_text, 'already disabled') !== false) {
                // Recharger Apache
                exec('sudo systemctl reload apache2 2>&1', $reload_output, $reload_var);
                header('Location: apache_domains.php');
            } else {
                // Vérifier si c'est un fichier introuvable
                if (strpos($output_text, 'No such file or directory') !== false) {
                    header('Location: apache_domains.php?status=error&message=fichier_introuvable');
                } else {
                    header('Location: apache_domains.php?status=error&message=exception_' . urlencode($output_text));
                }
            }
        }
    } catch (Exception $e) {
        header('Location: apache_domains.php?status=error&message=exception_' . urlencode($e->getMessage()));
    }
} else {
    header('Location: apache_domains.php?status=error&message=donnees_manquantes');
}
exit;
?>
