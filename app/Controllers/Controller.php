<?php

namespace App\Controllers;
class Controller{
    public function view($route, $data=[]){
        //destructurar el array
        extract($data);
        

        $route = str_replace('.','/',$route);
        
        if(file_exists("../resources/views/{$route}.php")){
            ob_start();
            include "../resources/views/{$route}.php";
            $content = ob_get_clean();
            return $content;
        }else{
            return "el archivo no existe";
        }
    }
    public function redirect($route){
        header("Location:{$route}");
    }
}