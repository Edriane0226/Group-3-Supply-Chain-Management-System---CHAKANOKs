<?php

namespace App\Controllers;

class Home extends BaseController
{   
    //Unnecessary Because there is login form loader at Auth.php
     public function index(): string
     {
         return view('pages/Central');
     }
}
