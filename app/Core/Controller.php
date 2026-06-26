<?php

namespace App\Core;

class Controller
{
    protected function render($view, $data = []): void
    {
        extract($data);
        ob_start();
         require_once __DIR__ . "/../Views/layout.php";
        require_once __DIR__ . "/../Views/{$view}.php";
        $content = ob_get_clean();
       
    }

}
