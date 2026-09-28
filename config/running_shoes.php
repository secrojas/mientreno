<?php

/*
|--------------------------------------------------------------------------
| Catálogo de zapatillas de running
|--------------------------------------------------------------------------
|
| Modelos más comunes para autocompletar el alta de zapatillas. Al elegir
| uno se precargan el uso recomendado y la vida útil orientativa (km).
| Los valores de vida útil son referencias generales: cada runner puede
| ajustarlos. Para sumar un modelo alcanza con agregar una fila.
|
| usage: daily | tempo | long_run | race | trail  (ver App\Enums\ShoeUsage)
|
*/

return [

    'models' => [
        // Nike
        ['brand' => 'Nike', 'model' => 'Pegasus 41', 'usage' => 'daily', 'max_km' => 800],
        ['brand' => 'Nike', 'model' => 'Vomero 18', 'usage' => 'long_run', 'max_km' => 750],
        ['brand' => 'Nike', 'model' => 'Invincible 3', 'usage' => 'long_run', 'max_km' => 700],
        ['brand' => 'Nike', 'model' => 'Structure 26', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Nike', 'model' => 'Zoom Fly 6', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'Nike', 'model' => 'Vaporfly 3', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Nike', 'model' => 'Alphafly 3', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Nike', 'model' => 'Pegasus Trail 5', 'usage' => 'trail', 'max_km' => 650],

        // Adidas
        ['brand' => 'Adidas', 'model' => 'Adizero SL2', 'usage' => 'daily', 'max_km' => 650],
        ['brand' => 'Adidas', 'model' => 'Adizero Boston 12', 'usage' => 'tempo', 'max_km' => 650],
        ['brand' => 'Adidas', 'model' => 'Adizero Adios Pro 4', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Adidas', 'model' => 'Supernova Rise', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Adidas', 'model' => 'Ultraboost Light', 'usage' => 'daily', 'max_km' => 700],
        ['brand' => 'Adidas', 'model' => 'Terrex Agravic Speed', 'usage' => 'trail', 'max_km' => 600],

        // ASICS
        ['brand' => 'ASICS', 'model' => 'Novablast 5', 'usage' => 'daily', 'max_km' => 700],
        ['brand' => 'ASICS', 'model' => 'Gel-Nimbus 27', 'usage' => 'long_run', 'max_km' => 800],
        ['brand' => 'ASICS', 'model' => 'Gel-Cumulus 27', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'ASICS', 'model' => 'Gel-Kayano 32', 'usage' => 'daily', 'max_km' => 800],
        ['brand' => 'ASICS', 'model' => 'GT-2000 13', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'ASICS', 'model' => 'Superblast 2', 'usage' => 'long_run', 'max_km' => 700],
        ['brand' => 'ASICS', 'model' => 'Magic Speed 4', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'ASICS', 'model' => 'Metaspeed Sky Paris', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'ASICS', 'model' => 'Gel-Trabuco 13', 'usage' => 'trail', 'max_km' => 650],

        // Brooks
        ['brand' => 'Brooks', 'model' => 'Ghost 16', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Brooks', 'model' => 'Glycerin 22', 'usage' => 'long_run', 'max_km' => 750],
        ['brand' => 'Brooks', 'model' => 'Adrenaline GTS 24', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Brooks', 'model' => 'Hyperion Max 2', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'Brooks', 'model' => 'Hyperion Elite 4', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Brooks', 'model' => 'Cascadia 18', 'usage' => 'trail', 'max_km' => 700],

        // Saucony
        ['brand' => 'Saucony', 'model' => 'Ride 18', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Saucony', 'model' => 'Triumph 22', 'usage' => 'long_run', 'max_km' => 750],
        ['brand' => 'Saucony', 'model' => 'Guide 18', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Saucony', 'model' => 'Kinvara 15', 'usage' => 'tempo', 'max_km' => 550],
        ['brand' => 'Saucony', 'model' => 'Endorphin Speed 4', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'Saucony', 'model' => 'Endorphin Pro 4', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Saucony', 'model' => 'Peregrine 15', 'usage' => 'trail', 'max_km' => 650],

        // Hoka
        ['brand' => 'Hoka', 'model' => 'Clifton 10', 'usage' => 'daily', 'max_km' => 700],
        ['brand' => 'Hoka', 'model' => 'Bondi 9', 'usage' => 'long_run', 'max_km' => 750],
        ['brand' => 'Hoka', 'model' => 'Mach 6', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'Hoka', 'model' => 'Arahi 7', 'usage' => 'daily', 'max_km' => 700],
        ['brand' => 'Hoka', 'model' => 'Rocket X 2', 'usage' => 'race', 'max_km' => 400],
        ['brand' => 'Hoka', 'model' => 'Speedgoat 6', 'usage' => 'trail', 'max_km' => 650],

        // New Balance
        ['brand' => 'New Balance', 'model' => 'Fresh Foam X 1080v14', 'usage' => 'long_run', 'max_km' => 750],
        ['brand' => 'New Balance', 'model' => 'Fresh Foam X 880v15', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'New Balance', 'model' => 'FuelCell Rebel v4', 'usage' => 'tempo', 'max_km' => 550],
        ['brand' => 'New Balance', 'model' => 'FuelCell SC Elite v4', 'usage' => 'race', 'max_km' => 400],

        // Puma
        ['brand' => 'Puma', 'model' => 'Velocity Nitro 3', 'usage' => 'daily', 'max_km' => 700],
        ['brand' => 'Puma', 'model' => 'Magnify Nitro 2', 'usage' => 'long_run', 'max_km' => 700],
        ['brand' => 'Puma', 'model' => 'Deviate Nitro 3', 'usage' => 'tempo', 'max_km' => 600],
        ['brand' => 'Puma', 'model' => 'Fast-R Nitro Elite 3', 'usage' => 'race', 'max_km' => 400],

        // On
        ['brand' => 'On', 'model' => 'Cloudmonster 2', 'usage' => 'daily', 'max_km' => 650],
        ['brand' => 'On', 'model' => 'Cloudsurfer 2', 'usage' => 'daily', 'max_km' => 650],
        ['brand' => 'On', 'model' => 'Cloudboom Strike', 'usage' => 'race', 'max_km' => 400],

        // Mizuno
        ['brand' => 'Mizuno', 'model' => 'Wave Rider 28', 'usage' => 'daily', 'max_km' => 750],
        ['brand' => 'Mizuno', 'model' => 'Neo Zen', 'usage' => 'daily', 'max_km' => 650],
        ['brand' => 'Mizuno', 'model' => 'Wave Rebellion Pro 3', 'usage' => 'race', 'max_km' => 400],

        // Salomon
        ['brand' => 'Salomon', 'model' => 'Speedcross 6', 'usage' => 'trail', 'max_km' => 650],
        ['brand' => 'Salomon', 'model' => 'S/Lab Ultra Glide', 'usage' => 'trail', 'max_km' => 650],
    ],

    /*
    | Colores sugeridos para la ilustración de la zapatilla.
    */
    'colors' => [
        '#2DE38E', '#FF3B5C', '#FF4FA3', '#60A5FA', '#F59E0B',
        '#A78BFA', '#F9FAFB', '#111827', '#EF4444', '#14B8A6',
    ],

];
