<?php
namespace App\Http\Controllers;

class HospitalsController extends Controller {


	public static function specialty($name, $county){

        $result = "";

        if($name=='Select specialty'){

            $result .= "You didn't enter a facility type";

        }else{

            $county = strtoupper($county);

            $key = config('custom_config.google_api_key');

            $table = config('custom_config.facilities_table');

            $url = "https://www.googleapis.com/fusiontables/v1/query?";

            if($county == "SELECT COUNTY"){
                $sql = "SELECT * FROM ".$table." where facility_full='$name'";

            }else{
                $sql = "SELECT * FROM ".$table." where facility_full='$name' AND County='$county'";

            }

            $options = array("sql"=>$sql, "key"=>$key, "sensor"=>"false");

            $url .= http_build_query($options,'','&');

            $page = file_get_contents($url);

            $data = json_decode($page, TRUE);

            $result = "";

            if(!array_key_exists("rows", $data)){
                $result .= "No hospitals found for those parameters.";
            }else{
                $rows = $data['rows'];

                foreach($rows as $facility){
                    $filtered_name = str_replace(')', '', str_replace('(', '', $facility['1']));
                    $result .= "<a href='https://maps.google.com/maps?q=".$facility['11']."+(".$filtered_name.")' target='_blank'>".$facility['1']."</a> - ".$facility['4']."<br />";
                }
            }

		}

        return $result;
	}

}