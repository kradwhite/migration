<?php

return [
	'driver-create-error' => 'Ошибка создания драйвера. %s',
    'wrong-config-file' => "Неверный формат данных в файле конфигурации '%s'",
    'environment-not-found' => 'Не указана environment',
    'config-file-already-exist' => "Файл конфигурации '%s' уже существует",
    'config-file-create-error' => "Ошибка создания файла конфигурации '%s'",
    'config-file-environment-not-found' => "Environment '%s' не найден в конфиг файле",
    'table-name-not-found' => 'Не указано имя таблицы с миграциями',
    'migration-path-not-found' => 'Не указан путь до каталога с миграциями',
    'migration-file-not-found' => "Файл '%s' с миграцией не найден",
    'driver-not-found' => "Не указан драйвер внутри '%s' environment",
    'work-dir-wrong' => 'Ошибка получения рабочего каталога. Возможно не хватает доступа на чтение у одно из каталогов пути',
    'migration-already-exist' => "Миграция с именем '%s' уже существует",
    'migration-file-create-error' => "Ошибка создания файла '%s'",
    'migration-file-chmod-error' => "Ошибка установки доступов 0664 на файл '%s'",
    'migration-class-not-found' => "Не найден класс '%s' миграции",
    'migration-not-is-a-migration' => "Миграция '%s' должна быть унаследована от 'kradwhite\migration\Migration::class",
    'migration-dir-not-found' => "Каталог с миграциями '%s' не найден",
    'migration-dir-not-dir' => "Файл с имененм '%s' не является каталогом",
    'migration-dir-scan-error' => "Ошибка получения файлов из каталога '%s'",
];
