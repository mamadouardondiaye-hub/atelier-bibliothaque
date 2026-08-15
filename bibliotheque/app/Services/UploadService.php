<?php

declare(strict_types=1);

namespace App\Services;

use Exceptions\ValidationException;

/**
 * Gestion des images envoyées par le formulaire (couvertures de livres).
 *
 * - Par défaut : enregistrement local dans `public/uploads/`.
 * - Si Cloudinary est configuré (config/cloudinary.php) : upload distant via
 *   l'API REST Cloudinary (signature HMAC-SHA1, envoi multipart avec cURL).
 */
class UploadService
{
    private const MAX_SIZE = 2 * 1024 * 1024; // 2 Mo

    /** Extensions autorisées : extension => type MIME détecté */
    private const TYPES = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
    ];

    private array $cloudinary;
    private string $uploadDir;

    public function __construct()
    {
        $this->cloudinary = require dirname(__DIR__, 2) . '/config/cloudinary.php';
        $this->uploadDir = dirname(__DIR__, 2) . '/public/uploads';
    }

    public function isCloudinaryConfigured(): bool
    {
        return !empty($this->cloudinary['enabled'])
            && $this->cloudinary['cloud_name'] !== ''
            && $this->cloudinary['api_key'] !== ''
            && $this->cloudinary['api_secret'] !== '';
    }

    /**
     * Traite un fichier reçu via $_FILES.
     *
     * @param array<string, mixed> $file Élément $_FILES['couverture']
     * @return string|null Chemin ou URL de l'image, ou null si aucun fichier envoyé
     */
    public function upload(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $this->validate($file);

        return $this->isCloudinaryConfigured()
            ? $this->uploadToCloudinary((string) $file['tmp_name'])
            : $this->uploadLocally((string) $file['tmp_name']);
    }

    /** @param array<string, mixed> $file */
    private function validate(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException("L'envoi de l'image a échoué.");
        }

        if ((int) $file['size'] > self::MAX_SIZE) {
            throw new ValidationException("L'image est trop volumineuse (2 Mo maximum).");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo === false ? '' : finfo_file($finfo, (string) $file['tmp_name']);
        if ($finfo !== false) {
            finfo_close($finfo);
        }

        if (!in_array($mime, self::TYPES, true)) {
            throw new ValidationException('Le fichier doit être une image (JPG, PNG, GIF ou WebP).');
        }
    }

    private function uploadLocally(string $tmpName): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo === false ? '' : finfo_file($finfo, $tmpName);
        if ($finfo !== false) {
            finfo_close($finfo);
        }

        $extension = array_search($mime, self::TYPES, true);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }

        if (!move_uploaded_file($tmpName, $this->uploadDir . '/' . $filename)) {
            throw new ValidationException("Impossible d'enregistrer l'image sur le serveur.");
        }

        return '/uploads/' . $filename;
    }

    private function uploadToCloudinary(string $tmpName): string
    {
        $timestamp = time();
        $signature = sha1('timestamp=' . $timestamp . $this->cloudinary['api_secret']);

        $ch = curl_init(
            'https://api.cloudinary.com/v1_1/' . $this->cloudinary['cloud_name'] . '/image/upload'
        );
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => [
                'file'      => new \CURLFile($tmpName),
                'api_key'   => (string) $this->cloudinary['api_key'],
                'timestamp' => (string) $timestamp,
                'signature' => $signature,
            ],
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '' || !is_string($response)) {
            throw new ValidationException('Échec de la communication avec Cloudinary.');
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['secure_url'])) {
            throw new ValidationException("Cloudinary a refusé l'image.");
        }

        return (string) $data['secure_url'];
    }
}
