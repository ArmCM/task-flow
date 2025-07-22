<?php

function getUri()
{
    return parse_url($_SERVER['REQUEST_URI'])['path'];
}

function getRequestMethod(): string
{
    return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
}
