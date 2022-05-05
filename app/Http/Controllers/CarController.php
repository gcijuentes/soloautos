<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Car;
use App\Models\Ad;
use Illuminate\Http\Request;

class CarController extends ControllerBase
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //$avisosList = Ad::with('car','car.ciudad','car.ciudad.region','car.images');

        $vehiculoList = Car::with('ciudad','ciudad.provinces','ciudad.provinces.region','images','ad');


        if($request->has('brand')){
             //$request->query('tipoPlan')
            $vehiculoList->where('id_marca','=',$request->input('brand'));
            //$avisosList->where('id_marca','=',$request->input('brand'));
        }

        if($request->has('searchString')){
            // $request->query('tipoPlan')
            $vehiculoList->join('aviso', 'vehiculo.id', '=', 'id_vehiculo');
            $vehiculoList->where('aviso.titulo','LIKE', '%'. $request->input('searchString') .'%');
            //$vehiculoList->get(['.*']);
        }


        //$vehiculoList2 =  DB::table('vehiculo')->get(['*.*']);

        //$vehiculoList->with('images');

       // $vehiculoList2 = $vehiculoList2->paginate(1500);
        $vehiculoList = $vehiculoList->paginate(1500,['vehiculo.*']);

        $meta['current_page'] = 1;
        $meta['last_page'] = 1;
        $meta['per_page'] = 10;


        return $this->sendResponse($vehiculoList, 'OK',$meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {




    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $car = Car::with('ciudad','ciudad.provinces','ciudad.provinces.region','images','ad')->where('id',$id)->get();

        if($car->isEmpty()){
           return $this->sendError("Car not found",null,404);
        }
        return $this->sendResponse($car, 'OK');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
