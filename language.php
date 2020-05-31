<?php

/**
 * Допускается одновременное использование нескольких хранилищ с разными источниками
 * Значения в массивах 'names' должны быть уникальными на уровне приложения
 */

return [
    // допустимые языки, первое значение будет значением по умолчанию
    'locales' => ['ru', 'en'],

    // имя набора фраз по умолчанию
    //'default' => 'errors',

    // допустимо создать свою фабрику, унаследовав \kradwhite\language\text\TextFactory
    //'factory' => \kradwhite\language\text\TextFactory::class,

    // хранилища текстов
    'texts' => [
        /* конфигурация для хранения фраз в файлах */
        [
            // тип хранилища для языка
            'type' => 'php',

            // имена набора фраз, в файловом хранилище имена файлов
            'names' => ['exceptions', 'messages'],

            // путь к директории, которая будет хранить файлы в фразами
            'directory' => __DIR__ . DIRECTORY_SEPARATOR . 'language'
        ],

        // конфигурация для хранения фраз в базе данных
        /*
        [
            // тип хранилища для языка
            'type' => 'database',

            // имена набора фраз, в бд хранилище значение в колонке name
            'names' => ['messages'],

            // имя таблицы с фразами
            'table' => 'kw_language',

            // ограничение длины фразы в бд
            'textLimit' => 256,

            // ограничение длины строки параметров в бд
            'paramsLimit' => 256,

            //
            'columns => [
                'locale' => 'locale',
                'name' => 'name',
                'id' => 'id',
                'text' => 'text',
                'params' => 'params'
            ],

            // имена колонок \kradwhite\language\text\TextRepository
            'repository' => \kradwhite\language\text\SqlTextRepository::class,

            // допускается создать свой репозиторий, реализовав интерфейс
            'connection' => [
                'driver' => 'pgsql',                                // или 'mysql'
                'host' => 'localhost',
                'user' => 'admin',
                'password' => 'admin',
                'dbName' => 'test',
                'port' => '5432'
            ]
        ]
        */
    ]
];