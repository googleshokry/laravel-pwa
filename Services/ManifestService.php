<?php
/**
 * Created by PhpStorm.
 * User: Eng Shokry
 * Date: 22/08/2018
 * Time: 19:13
 */

namespace LaravelPWA\Services;


class ManifestService
{
    public function generate()
    {
        $basicManifest =  [
            'name' => config('laravelpwa.manifest.name'),
            'short_name' => config('laravelpwa.manifest.short_name'),
            'start_url' => asset(config('laravelpwa.manifest.start_url')),
            'display' => config('laravelpwa.manifest.display'),
            'theme_color' => config('laravelpwa.manifest.theme_color'),
            'background_color' => config('laravelpwa.manifest.background_color'),
        ];

        foreach (config('laravelpwa.manifest.icons') as $size => $file) {
            $fileInfo = pathinfo($file);
            $basicManifest['icons'][] = [
                'src' => $file,
                'type' => 'image/' . $fileInfo['extension'],
                'sizes' => $size
            ];
        }
        return $basicManifest;
    }

}