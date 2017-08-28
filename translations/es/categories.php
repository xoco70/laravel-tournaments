<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tournament Categories
    |--------------------------------------------------------------------------
    */

    'junior'      => 'Junior',
    'junior_team' => 'Junior por Equipo',

    'man_first_force'    => 'Varonil Primera Fuerza',
    'man_second_force'   => 'Varonil Segunda Fuerza',
    'woman_first_force'  => 'Femenil Primera Fuerza',
    'woman_second_force' => 'Femenil Segunda Fuerza',

    'mixed_single'  => 'Mixto Individual',
    'men_single'    => 'Varonil Individual',
    'ladies_single' => 'Femenil Individual',
    'mixed_team'    => 'Mixto por Equipo',
    'men_team'      => 'Varonil por Equipo',
    'ladies_team'   => 'Femenil por Equipo',

    'master' => 'Master (> 50 años)',

    // Categories
    'category'                => 'Categoría|Categorías',
    'enchoQty'                => '¿Cuántos encho habrá?',
    'encho_infinite'          => '0 para infinito',
    'enchoDuration'           => '¿Cuánto dura cada encho?',
    'category_not_configured' => 'La categoría aún no esta configurada',
    'add_category'            => 'Agregar Categoria',
    'gender'                  => 'Género',
    'male'                    => 'Varonil',
    'female'                  => 'Femenil',
    'mixt'                    => 'Mixto',
    'ageCategory'             => 'Categoría de edad',
    'no_age_restriction'      => 'Sin límite de edad',
    'children'                => 'Niños',
    'students'                => 'Estudiantes',
    'adults'                  => 'Adultos',
    'masters'                 => 'Masters',
    'custom'                  => 'Personalizado',
    'years'                   => 'años',
    'age'                     => 'Edad',
    'min_age'                 => 'Edad mínima',
    'max_age'                 => 'Edad máxima',
    'min_grade'               => 'Grado mínimo',
    'max_grade'               => 'Grado máximo',
    'no_grade_restriction'    => 'Sin limite de grado',
    'add_and_close'           => 'Guardar',
    'add_and_new'             => 'Guardar y Nuevo ',
    'single'                  => 'Individual',
    'first_force'             => 'Primera Fuerza',
    'second_force'            => 'Segunda Fuerza',
    'cost'                    => 'Costo',

    // CategorySetting
    'categorySettings'     => 'Configuración de categorías',
    'treeType'             => 'Tipo de arbol',
    'playoff'           => 'Round Robin',
    'direct_elimination'   => 'Eliminación directa',
    'teamSize'             => 'Tamaño del equipo',
    'teamSizeReserve'      => 'Reservas',
    'fightingArea'         => 'Área|Áreas',
    'fightDuration'        => 'Duración',
    'hasPreliminary'       => 'Preliminarios',
    'preliminaryWinner'    => 'Ganadores x grupo',
    'hasEncho'             => '¿Encho?',
    'hasHantei'            => '¿Hantei?',
    'hanteiLimit'          => 'Limite para Hantei',
    'isTeam'               => 'Equipo',
    'alias'                => 'Alias',
    'preliminaryGroupSize' => 'Tamaño del grupo',
    'enchoGoldPoint'       => 'Punto de oro',
    'limitByEntity'        => 'Limite de competidores',

    // Tooltips
    'fightingAreaTooltip'         => 'Cuantas areas se usarán para la categoría',
    'fightDurationTooltip'        => 'El tiempo de cada combate',
    'costTooltip'                 => 'Cuanto se cobra para participar a esta categoría',
    'hasPreliminaryTooltip'       => '¿Uso de Round Robin para la primera fase?',
    'preliminaryWinnerTooltip'    => 'Cuantos competidores salen del round robin',
    'hasEnchoTooltip'             => '¿Se usará encho?',
    'enchoQtyTooltip'             => 'Hasta cuantos enchos habrá despues de un empate?',
    'enchoDurationTooltip'        => 'Cuanto dura cada encho',
    'hasHanteiTooltip'            => '¿Se usará hantei?',
    'hanteilimitTooltip'          => 'Hasta que nivel de la competencia desea que se aplique la regla de Hantei',
    'aliasTooltip'                => 'Eliga un nombre personalizado para la categoría ( Opcional )',
    'preliminaryGroupSizeTooltip' => 'Cantidad de competidores por grupo',
    'enchoGoldPointTooltip'       => 'Nivel de la competencia en donde el encho ya no tiene tiempo límite (0 para todos)',
    'limitByEntityTooltip'        => 'Cantidad de competidor que se pueden registrar',
    'rulesTooltip'                => 'Crea y se configura de forma automatica las categorías según el modelo de reglas elegido',

    'configure_categories'      => 'Configura las categorías',
    'configure_categories_text' => 'Selecciona una de las 2 opciones para configurar las categorias del torneo',

    'presettings'      => 'Preconfigurada',
    'presettings_text' => 'Elige el reglamento bajo el que tu torneo este regido. Las categorias se crearan y se configuraran de forma automatica',

    'manual' => 'Manual',
];
