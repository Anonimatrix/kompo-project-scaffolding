<?php

const GENERIC_PACKAGE_NAME = 'generic-package-name';

function convertToCamelCase($string)
{
    return str_replace(' ', '', implode(array_map(fn($w) => ucfirst($w), explode('-', $string))));
}

$genericNameCamelCase = convertToCamelCase(GENERIC_PACKAGE_NAME);

$packageName = $argv[1] ?? null;

if (!$packageName) {
    echo "Please provide a package name\n";
    exit;
}

$packageNameCamelCase = convertToCamelCase($packageName);

foreach (array_merge(glob('./**/*.*'), glob('./*.*')) as $file) {
    if ($file === './setPackageName.php') {
        continue;
    }

    $fileContent = file_get_contents($file);

    $fileContent = str_replace($genericNameCamelCase, $packageNameCamelCase, $fileContent);
    $fileContent = str_replace(GENERIC_PACKAGE_NAME, $packageName, $fileContent);

    $fileNameChanged = str_replace(GENERIC_PACKAGE_NAME, $packageName, $file);
    $fileNameChanged = str_replace($genericNameCamelCase, $packageNameCamelCase, $fileNameChanged);

    file_put_contents($fileNameChanged, $fileContent);

    if ($file !== $fileNameChanged) {
        unlink($file);
    }
}