<?php

namespace App\Controllers;

//use App\Models\Contact;

//este controlador se extiende de Controller.php

class HomeController extends Controller{
    public function index(){
        /*
        $contacModel =new Contact();
        return $contacModel->update(6, [
            'name'=>'jose carlos 2',
            'email'=>'jc@gmail.com',
            'phone'=>'64565168'
        ]);
        */
        return $this->view('welcome');
    }

}