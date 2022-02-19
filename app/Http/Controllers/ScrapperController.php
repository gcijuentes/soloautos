<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DOMDocument;
use \DOMXPath;
use GuzzleHttp\Client;
use League\Csv\Writer;


use App\Models\City;
use App\Models\Car;

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

        for($i=0; $i<=10; $i++){
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

                $urlAviso = $xpath->evaluate('.//a[@class="title"]', $anuncio);
                if(isset($urlAviso[0]->textContent)){
                    $urlAviso = trim($urlAviso[0]->getattribute('href'));

                }else{
                    $urlAviso = '';
                }

                $imgOriginal = str_replace("thumbsli","images",$img);


                $data = array();
                if($urlAviso!= ''){
                    //   $data = getDetailYapo($urlAviso);
                }


                //creamos
                Car
                //break;
                //verifyBrand();


               //buscamos comuna por name
               $city = City::whereRaw('lower(comuna_nombre) = (?)',[$comuna])->get();

               if($city!=null){
                print_r(11111111111);
               }else{
                print_r(222222222);
               }


               print_r($city->id);
               exit(1);

                if(!isset($data['detalle'])){
                    echo '<br>';
                    echo 'No existe';
                    print_r($urlAviso);
                    $brand = "";
                    $description = "";
                }else{
                    echo '<br>';
                    echo 'existe';
                    $brand = $data['detalle']['brand'];
                    $description = $data['detalle']['destription'];
                    $images = $data['detalle']['images'];
                }

                $brand = strtolower($brand);





                // $idBrand = verifyBrand($brand);
                // $idComuna = verifyCity($comuna);
                // $sqlYapo = "INSERT INTO `yapo` ( `titulo`, `imagen`, `precio`, `anio`, `km`, `transmision`, `region`, `comuna`, `url`,`marca`,`detalle`,`images`,`id_comuna`)
                // VALUES ('$titulo', '$imgOriginal', '$precio', '$anio', '$km', '$transmision', '$region', '$comuna', '$urlAviso','$brand','$description','$images','$idComuna');";

                //fwrite($fp, $sqlYapo);

            }
        }


        return $data;
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
}
