<?php

function getUri()
{
    return parse_url($_SERVER['REQUEST_URI'])['path'];
}
