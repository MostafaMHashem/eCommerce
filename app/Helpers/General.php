<?php

use App\Models\Language;
use Illuminate\Support\Facades\Config;

function get_languages() {
    return Language::active() -> selection() -> get();
}

/**
 * return the default language from the app.php file from the locale
 */
function get_default_lang() {
    return Config::get('app.locale');
}

/**
 * save the images the user will upload
 * or we deal with uploading images
 * it's general function
 */
function uploadImage($folder, $image) {
    $image -> store('/', $folder);
    $fileName = $image -> hashname();
    $path = 'images/' . $folder . '/' . $fileName;
    return $path;
}
