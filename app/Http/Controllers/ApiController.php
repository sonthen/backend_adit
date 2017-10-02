<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\user_email;
use App\user_phone;

class ApiController extends Controller
{
    function getKota(Request $request){
    	$propinsi_= str_replace(" ","%20",$request->propinsi);
    	
    	//$url='https://www.kioson.com/carimitra/getkota?propinsi='. $propinsi_ ;
		// $json = file_get_contents($url);
		// return $json;

        $client = new Client();
        $res = $client->request('GET', 'https://www.kioson.com/carimitra/getkota?propinsi='. $propinsi_ );
        
        return $res->getBody();
    }

    function getKecamatan(Request $request){
    	$propinsi_= str_replace(" ","%20",$request->propinsi);
        $kota_= str_replace(" ","%20",$request->kota);

        //$url='https://www.kioson.com/carimitra/getkecamatan?propinsi='. $propinsi_ .'&kota='.$kota_ ;
		// $json = file_get_contents($url);
		// return $json;

        $client = new Client();
        $res = $client->request('GET', 'https://www.kioson.com/carimitra/getkecamatan?propinsi='. $propinsi_ .'&kota='.$kota_);
        
        return $res->getBody();
    }

    function getKelurahan(Request $request){
    	$propinsi_= str_replace(" ","%20",$request->propinsi);

    	$kota_= str_replace(" ","%20",$request->kota);

    	$kec_ = str_replace(" ","%20",$request->kecamatan);

        $client = new Client();
        $res = $client->request('GET', 'https://www.kioson.com/carimitra/getkelurahan?propinsi='. $propinsi_ .'&kota='.$kota_ .'&kecamatan=' . $kec_);
        
        return $res->getBody();

        //$url='https://www.kioson.com/carimitra/getkelurahan?propinsi='. $propinsi_ .'&kota='.$kota_ .'&kecamatan=' . $kec_;
		// $json = file_get_contents($url);
		// return $json;
    }

    function getKios(Request $request){

        $data = array(
            'propinsi'=> $request->propinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan
        );  

	    $client = new Client();
	    $res = $client->request('POST', 'https://www.kioson.com/carimitra/tokojson', [
	        'form_params' => $data
	    ]);

	    $result= $res->getBody();
	    return $result;
    }
    
    function getSaham(){
             
        function bacaHTML($url){
        
            // inisialisasi CURL
            $data = curl_init();
        
            // setting CURL
            curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
        
            curl_setopt($data, CURLOPT_URL, $url);
        
            // menjalankan CURL untuk membaca isi file
            $hasil = curl_exec($data);
        
            curl_close($data);
        
            return $hasil;
        
        }
        
        //mengambil data dari url
        $bacaHTML = bacaHTML("http://ihsg-idx.com/saham/IDX:ASII/");
        return $bacaHTML;
        
        //membuat dom dokumen
        $dom = new DomDocument();
        
        
        //mengambil html dari url untuk di parse
        @$dom->loadHTML($bacaHTML);
        
        //nama class yang akan dicari
        $classname="price";
        
        
        //mencari class memakai dom query
        $finder = new DomXPath($dom);
        
        $spaner = $finder->query("//*[contains(@class, '$classname')]");
        
        //mengambil data dari class yang pertama
        $span = $spaner->item(0);
        
        //dari class pertama mengambil 2 elemen yaitu a yang menyimpan judul dan link dan span yang menyimpan tanggal
        // $link =  $span->getElementsByClassName('price');
        
        
        
        // return $span->nodeValue;      
        
    }

    function addEmail(Request $request){
        DB::beginTransaction();

        try{
            $this->validate($request, [
                'name' => 'required',
                'email'=> 'email|required'
            ]);
            $name = $request->input('name');
            $email = $request->input('email');

            $newUserEmail = new user_email;
            $newUserEmail->name = $name;
            $newUserEmail->email = $email;
            $newUserEmail->save(); 
            
            DB::commit();
            return response()->json(["message"=>"Add new user email success"]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()],500);
        }
    }

    function addPhone(Request $request){
        DB::beginTransaction();

        try{
            $this->validate($request, [
                'phone_number' => 'required|numeric'
            ]);
            $phone = $request->input('phone_number');

            $newUserPhone = new user_phone;
            $newUserPhone->phone_number = $phone;
            $newUserPhone->save(); 

            DB::commit();
            return response()->json(["message"=>"Add new user phone success"]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()],500);
        }
    }
}
