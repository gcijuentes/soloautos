<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DOMDocument;
use \DOMXPath;
use GuzzleHttp\Client;
use League\Csv\Writer;


use App\Models\City;
use App\Models\Car;
use App\Models\Brand;
use App\Models\Ad;
use App\Models\Image;

class ScrapperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


            return view('yaposcrapp');
    }


        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scrapYapo(Request $request)
    {

            $url_yapo = $request->input('url_yapo');

            if($request->input('url_yapo') != null){

                $dataYapo[] = $this->extractDataFromYapo($url_yapo);

            }


            return view('yaposcrapp_response',['url_yapo'=>$url_yapo]);
    }


        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function extractDataFromYapo($url_yapo){

        for($i=0; $i<=1; $i++){
            //$i += 1;
            $url_request = $url_yapo.'&o='.$i;

            $client = new Client();
            $user_agent = array(
                'desktop' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
                'mobile' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
            );
            $r = $client->request('GET', $url_request, array(
                'headers' => array(
                    'User-Agent' => $user_agent['desktop']
                )
            ));

            $html = (string) $r->getBody();
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $xpath = new DOMXPath($doc);
            $anuncios = $xpath->evaluate('//table[@id="hl"]//tr');


            foreach($anuncios as $anuncio){

                $urlAviso = $xpath->evaluate('.//a[@class="title"]', $anuncio);
                if(isset($urlAviso[0]->textContent)){
                    $urlAviso = trim($urlAviso[0]->getattribute('href'));

                }else{
                    $urlAviso = '';
                    continue;
                }

                if($urlAviso==''){
                    continue;
                }



               $titulo = $xpath->evaluate('.//a[@class="title"]', $anuncio);
                if(isset($titulo[0]->textContent)){
                    $titulo = trim($titulo[0]->textContent);

                }else{
                    $titulo = null;
                }

                $precio = $xpath->evaluate('.//span[@class="price"]', $anuncio);
                if(isset($precio[0]->textContent)){
                    $precio = trim($precio[0]->textContent);
                }else{
                    $precio = null;
                }

                $anio = $xpath->evaluate('.//div[@class="icons"]//i[@class="fal fa-calendar-alt icons__element-icon"]/following-sibling::span', $anuncio);
                if(isset($anio[0]->textContent)){
                    $anio = trim($anio[0]->textContent);
                }else{
                    $anio = null;
                }

                $km = $xpath->evaluate('.//div[@class="icons"]//i[@class="fal fa-tachometer icons__element-icon"]/following-sibling::span', $anuncio);
                if(isset($km[0]->textContent)){
                    $km = trim($km[0]->textContent);
                }else{
                    $km = null;
                }

                $transmision = $xpath->evaluate('.//div[@class="icons"]//i[@class="fal fa-cogs icons__element-icon"]/following-sibling::span', $anuncio);
                if(isset($transmision[0]->textContent)){
                    $transmision = trim($transmision[0]->textContent);
                }else{
                    $transmision = null;
                }

                $img = $xpath->evaluate('.//td[@class="listing_thumbs_image"]//div[@class="link_image"]//img', $anuncio);
                if(isset($img) && isset($img[0])){
                    $img = $img[0]->getattribute('src');
                }else{
                    $img = null;
                }

                $region = $xpath->evaluate('.//td[@class="clean_links"]//span[@class="region"]', $anuncio);
                if(isset($region[0])){
                    $region = trim($region[0]->textContent);
                }else{
                    $region = null;
                }

                $comuna = $xpath->evaluate('.//td[@class="clean_links"]//span[@class="commune"]', $anuncio);
                if(isset($comuna[0])){
                    $comuna = trim($comuna[0]->textContent);
                }else{
                    $comuna = null;
                }


                //$imgOriginal = str_replace("thumbsli","images",$img);


                $data = array();
                if($urlAviso!= ''){
                       $data = $this->getDetailYapo($urlAviso);
                }


                //buscamos comuna por name
                $city = City::whereRaw('lower(comuna_nombre) = (?)',[$comuna])->get();


                //creamos
                $precio = (int) filter_var($precio, FILTER_SANITIZE_NUMBER_INT);
                $kmInt = (int) filter_var($km, FILTER_SANITIZE_NUMBER_INT);

                $newCar = new Car();

                $newCar->precio = $precio;
                $newCar->anio = $anio;
                $newCar->kilometraje = $kmInt;
                $newCar->transmision = $transmision;
                $newCar->url = $urlAviso;
                if(count($city)>0){
                    $newCar->id_ciudad =  $city[0]->id;

                }

                $newCar->id_tipo_vehiculo = 1;// 1 = auto


                if(!isset($data['detalle'])){
                    $brand = "";
                    $description = "";

                }else{
                    $newCar->detalle = $data['detalle']['destription'];
                    $newCar->id_marca = $data['detalle']['id_brand'];

                    $newCar->combustible = $data['detalle']['combustible'];


                    $newCar->save();

                    if($data['detalle']['images']!=null){

                        $imagenes = explode(",", $data['detalle']['images']);

                        foreach ($imagenes as $imagen) {
                            if($imagen != ''){
                                $newImage = new Image();
                                $newImage->ruta = $imagen;
                                $newImage->id_vehiculo = $newCar->id;
                                $newImage->save();
                            }
                        }

                    }

                        //creamos Ad
                    $newAd =  new Ad();
                    $newAd->titulo = $data['detalle']['title'];
                    $newAd->id_vehiculo = $newCar->id;
                    $newAd->save();
                }


            }
        }


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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


    function getDetailYapo($urlYapo = ''){

            if($urlYapo!=''){

                    $url_request = $urlYapo;
                    $client = new Client();
                    $user_agent = array(
                        'desktop' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
                        'mobile' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
                    );
                    $r = $client->request('GET', $url_request, array(
                        'headers' => array(
                            'User-Agent' => $user_agent['desktop']
                        )
                    ));

                    $html = (string) $r->getBody();
                    libxml_use_internal_errors(true);
                    $doc = new DOMDocument();
                    $doc->loadHTML($html);
                    $xpath = new DOMXPath($doc);

                    $anunciosDetailsBox = $xpath->evaluate('//div[@class="details"]');

                    // Tabla Detalles
                    foreach($anunciosDetailsBox as $details){

                        $modelField = $xpath->evaluate('//div[@class="details"]//h5', $details);
                        if(isset($modelField[0]->textContent)){
                            $modelField = strtolower(trim($modelField[0]->textContent));
                        }else{
                            $modelField = null;
                        }

                        $data['detalle']['title']=$modelField;
                        $brandName = $this->searchBrand($modelField,$urlYapo);
                        $brand = Brand::whereRaw('lower(nombre_marca) = (?)',[$brandName])->get();

                        if(count($brand)>0){
                            $data['detalle']['id_brand']=$brand[0]->id;
                        }


                        /*
                        $priceFinal = $xpath->evaluate('.//div[@class="price price-final"]//strong', $details);
                        if(isset($priceFinal[0]->textContent)){
                            $priceFinal = trim($priceFinal[0]->textContent);
                        }else{
                            $priceFinal = null;
                        }*/

                        $destription = $xpath->evaluate('//div[@class="description"]//p', $details);
                        if(isset($destription[0]->textContent)){
                            $destription = trim($destription[0]->textContent);

                        }else{
                            $destription = null;
                        }
                        $data['detalle']['destription']=$destription;

                        $combustible = $xpath->evaluate('//tr[4]/td[1]/text()[1]', $details);
                        if(isset($combustible[0]->textContent)){
                            $combustible = trim($combustible[0]->textContent);

                        }else{
                            $combustible = null;
                        }
                        $data['detalle']['combustible']=$combustible;



                    }

                    $galleryBox = $xpath->evaluate('//img[@class="thumb_image"]');
                    $images = "";
                    foreach($galleryBox as $thumbsli){

                        if($thumbsli->getattribute('src')!=null){
                            $img =  str_replace("thumbs","images",$thumbsli->getattribute('src'));
                            $images = $images.$img.",";

                        }

                    }
                    $data['detalle']['images'] = $images;


            }else{
                http_response_code(400);
            }
            return $data;

    }


function searchBrand($marcaYapo,$urlYapo){

    $marcas[]="acadian";
$marcas[]="acura";
$marcas[]="alfa romeo";
$marcas[]="american motors";
$marcas[]="aro";
$marcas[]="asia motors";
$marcas[]="aston martin";
$marcas[]="audi";
$marcas[]="austin";
$marcas[]="autorrad";
$marcas[]="baic";
$marcas[]="beiging";
$marcas[]="bentley";
$marcas[]="bmw";
$marcas[]="brilliance";
$marcas[]="buick";
$marcas[]="byd";
$marcas[]="cadillac";
$marcas[]="caterham";
$marcas[]="changan";
$marcas[]="changhe";
$marcas[]="chery";
$marcas[]="chevrolet";
$marcas[]="chrysler";
$marcas[]="citroen";
$marcas[]="commer";
$marcas[]="dacia";
$marcas[]="daewoo";
$marcas[]="daihatsu";
$marcas[]="datsun";
$marcas[]="dfsk";
$marcas[]="dodge";
$marcas[]="dongfeng";
$marcas[]="ds automobiles";
$marcas[]="f.s.o.";
$marcas[]="faw";
$marcas[]="ferrari";
$marcas[]="fiat";
$marcas[]="ford";
$marcas[]="foton";
$marcas[]="g.m.c.";
$marcas[]="gac gonow";
$marcas[]="geely";
$marcas[]="great wall";
$marcas[]="hafei";
$marcas[]="haima";
$marcas[]="haval";
$marcas[]="hillman";
$marcas[]="honda";
$marcas[]="hyundai";
$marcas[]="infiniti";
$marcas[]="international";
$marcas[]="isuzu";
$marcas[]="jac";
$marcas[]="jaguar";
$marcas[]="jeep";
$marcas[]="jmc";
$marcas[]="karma";
$marcas[]="kenbo";
$marcas[]="kia motors";
$marcas[]="kyc";
$marcas[]="lada";
$marcas[]="lamborghini";
$marcas[]="lancia";
$marcas[]="land rover";
$marcas[]="landwind";
$marcas[]="lexus";
$marcas[]="lifan";
$marcas[]="lincoln";
$marcas[]="lotus";
$marcas[]="mahindra";
$marcas[]="maserati";
$marcas[]="maxus";
$marcas[]="mazda";
$marcas[]="mclaren";
$marcas[]="mercedes benz";
$marcas[]="mercury";
$marcas[]="mg";
$marcas[]="mini";
$marcas[]="mitsubishi";
$marcas[]="morgan";
$marcas[]="morris";
$marcas[]="nissan";
$marcas[]="nsu";
$marcas[]="oldsmobile";
$marcas[]="opel";
$marcas[]="peugeot";
$marcas[]="plymouth";
$marcas[]="polski fiat";
$marcas[]="pontiac";
$marcas[]="porsche";
$marcas[]="proton";
$marcas[]="puma";
$marcas[]="ram";
$marcas[]="renault";
$marcas[]="rolls royce";
$marcas[]="rover";
$marcas[]="saab";
$marcas[]="saehan";
$marcas[]="samsung";
$marcas[]="seat";
$marcas[]="sg";
$marcas[]="simca";
$marcas[]="skoda";
$marcas[]="sma";
$marcas[]="ssangyong";
$marcas[]="subaru";
$marcas[]="suzuki";
$marcas[]="tata";
$marcas[]="toyota";
$marcas[]="volkswagen";
$marcas[]="volvo";
$marcas[]="willys";
$marcas[]="yugo";
$marcas[]="zastava";
$marcas[]="zna";
$marcas[]="zotye";
$marcas[]="zx";


    foreach ($marcas as  $marca) {
        if (strpos($marcaYapo,$marca) !== false) {
            return $marca;
            break;
        }
    }

    print_r("Marca no encontrada<br>");
    print_r($marcaYapo);

    print_r("<br>");
    print_r( $urlYapo);
    print_r("<br>");
    exit();

}
}
