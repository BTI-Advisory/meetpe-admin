<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
class FileService
{
     /**
     * Upload un fichier vers un dossier spécifique sur AWS S3.
     *
     * @param UploadedFile $file Le fichier à uploader.
     * @param string $folder Le dossier cible dans le bucket S3.
     * @return string|null L'URL du fichier ou null en cas d'échec.
     */
    public static function uploadToS3( $file, string $folder, ?string $fileName = null)
    {
        try {

             // Si on reçoit une string binaire (ex: PDF généré)
            if (is_string($file)) {
                $fileName = $fileName ?? (time() . '.pdf');
                $path = "{$folder}/{$fileName}";

                //pour les factures il faut qu'ils soient privé et controle d'accès
                Storage::disk('s3')->put($path, $file, 'public');

                return Storage::disk('s3')->url($path);
            }



            // Générer un nom de fichier unique
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "{$folder}/{$fileName}";
    
            // Vérifier si le fichier est une image
            if (str_starts_with($file->getMimeType(), 'image/')) {
                // Ouvrir l'image avec intervention/image
                $manager = new ImageManager(new GdDriver());
                // Compression de l'image à 50% de qualité
                $image = $manager->read($file)->encode(new JpegEncoder(quality: 50));
                // Uploader sur S3 depuis la mémoire
                Storage::disk('s3')->put($path, (string) $image, 'public');
            } else {
                // Uploader normalement si ce n'est pas une image
                Storage::disk('s3')->put($path, file_get_contents($file), 'public');
            }
    
            // Retourner l'URL complète
            return Storage::disk('s3')->url($path);
    
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'upload du fichier : " . $e->getMessage());
            return null;
        }
    }
    
}
