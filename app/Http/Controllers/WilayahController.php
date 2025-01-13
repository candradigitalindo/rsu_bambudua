<?php

namespace App\Http\Controllers;

use App\Interfaces\WilayahInterface;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public $wilayahInterface;
    public function __construct(WilayahInterface $wilayahInterface)
    {
        $this->wilayahInterface = $wilayahInterface;
    }

    public function saveWilayah()
    {
        return $this->wilayahInterface->saveWilayah();
    }

    public function getProvinces()
    {
        return $this->wilayahInterface->getProvinces();
    }

    public function getCity($code)
    {
        return response()->json($this->wilayahInterface->getCity($code));
    }
}
