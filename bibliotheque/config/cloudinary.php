<?php

declare(strict_types=1);

/**
 * Configuration Cloudinary (upload des couvertures de livres).
 *
 * Si le service est activé et que les identifiants sont renseignés, les images
 * sont envoyées vers Cloudinary (API REST via cURL) et l'URL distante est
 * enregistrée en base.
 *
 * Sans identifiants (par défaut), les images sont simplement enregistrées
 * localement dans `public/uploads/` : l'application fonctionne sans compte.
 *
 * Pour obtenir vos identifiants : https://cloudinary.com/ (compte gratuit),
 * puis rubrique « Dashboard » du compte.
 */

return [
    'enabled'    => true,
    'cloud_name' => '',
    'api_key'    => '',
    'api_secret' => '',
];
