<?php

function basePath($path): string
{
    return BASE_PATH . $path;
}

function notEmpty($value): bool
{
    return !empty($value);
}
