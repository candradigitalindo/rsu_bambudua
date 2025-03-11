<?php

namespace App\Http\Controllers;

use App\Repositories\PasienRepository;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public $pasienRepository;
    public function __construct(PasienRepository $pasienRepository)
    {
        $this->pasienRepository = $pasienRepository;
    }
    
}
