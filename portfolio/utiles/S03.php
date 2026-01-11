<?php

// ---------------------------------------------------------
// 1. EXEMPLE DE CODE C (Pour la démo, on le met dans une variable)
// ---------------------------------------------------------
$codeC = <<<CODE
#include <stdio.h>

/**
 * @brief Calcule la somme de deux entiers.
 * * Cette fonction prend deux entiers en entrée et retourne
 * leur somme. Elle est très utile pour les additions basiques.
 * * @param a Le premier nombre entier.
 * @param b Le deuxième nombre entier.
 * @return La somme de a et b.
 */
int addition(int a, int b) {
    return a + b;
}

/**
 * @brief Affiche un message de bienvenue.
 * @param nom Le nom de la personne à saluer.
 */
void dire_bonjour(char* nom) {
    printf("Bonjour %s", nom);
}

// Un commentaire simple qui ne doit pas être inclus dans la doc.
int x = 0;
CODE;

// ---------------------------------------------------------
// 2. FONCTION DE PARSING
// ---------------------------------------------------------
function genererDocumentation($sourceCode) {
    $documentation = [];

    // Regex pour trouver les blocs /** ... */
    // Explication : 
    // \/\*\* -> Cherche le début "/**"
    // (.*?)   -> Capture tout le contenu de manière non-gourmande
    // \s*\*\/ -> Cherche la fin "*/"
    // s       -> Modifieur pour que le point (.) inclut les retours à la ligne
    preg_match_all('/\/\*\*(.*?)\*\/\s*(.*?;|.*?\))?/s', $sourceCode, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $rawComment = $match[1];
        $contextCode = isset($match[2]) ? trim($match[2]) : "Inconnu"; // Le code juste après le commentaire

        // Nettoyage du commentaire (enlève les * de début de ligne)
        $cleanComment = preg_replace('/^\s*\*\s?/m', '', $rawComment);
        
        // Extraction des tags
        $docItem = [
            'brief' => '',
            'params' => [],
            'return' => '',
            'function_signature' => htmlspecialchars(substr($contextCode, 0, 50) . (strlen($contextCode) > 50 ? '...' : ''))
        ];

        // Analyse ligne par ligne
        $lines = explode("\n", $cleanComment);
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strpos($line, '@brief') === 0) {
                $docItem['brief'] = substr($line, 7);
            } elseif (strpos($line, '@param') === 0) {
                $parts = explode(' ', substr($line, 7), 2);
                if (count($parts) == 2) {
                    $docItem['params'][] = ['name' => $parts[0], 'desc' => $parts[1]];
                }
            } elseif (strpos($line, '@return') === 0) {
                $docItem['return'] = substr($line, 8);
            } elseif (!empty($line) && empty($docItem['brief']) && $line[0] != '@') {
                // Si pas de @brief explicite, on prend la première ligne de texte
                $docItem['brief'] = $line;
            }
        }
        
        if (!empty($docItem['brief'])) {
            $documentation[] = $docItem;
        }
    }

    return $documentation;
}

// ---------------------------------------------------------
// 3. GÉNÉRATION HTML
// ---------------------------------------------------------
$docs = genererDocumentation($codeC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Documentation Technique</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        h1 { text-align: center; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .doc-block { margin-bottom: 40px; border-left: 5px solid #3498db; padding-left: 15px; }
        .func-sig { font-family: 'Courier New', monospace; background: #eee; padding: 5px; font-weight: bold; display: inline-block; border-radius: 4px; }
        .description { margin: 10px 0; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #555; width: 30%; }
        .tag-title { font-weight: bold; color: #e67e22; margin-top: 10px; display: block; }
    </style>
</head>
<body>

<div class="container">
    <h1>Documentation du Projet C</h1>
    <p>Généré automatiquement le <?php echo date('d/m/Y H:i'); ?></p>

    <?php if (empty($docs)): ?>
        <p>Aucune documentation trouvée.</p>
    <?php else: ?>
        <?php foreach ($docs as $doc): ?>
            <div class="doc-block">
                <div class="func-sig"><?php echo $doc['function_signature']; ?></div>
                
                <div class="description"><?php echo $doc['brief']; ?></div>

                <?php if (!empty($doc['params'])): ?>
                    <span class="tag-title">Paramètres :</span>
                    <table>
                        <?php foreach ($doc['params'] as $param): ?>
                            <tr>
                                <th><?php echo htmlspecialchars($param['name']); ?></th>
                                <td><?php echo htmlspecialchars($param['desc']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>

                <?php if (!empty($doc['return'])): ?>
                    <span class="tag-title">Retour :</span>
                    <p><?php echo htmlspecialchars($doc['return']); ?></p>
                <?php endif; ?>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>