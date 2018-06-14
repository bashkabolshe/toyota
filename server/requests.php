<?php

//

header('Content-Type: application/json; charset=utf-8');
include 'connect_db.php';
$input = filter_input_array(INPUT_GET);
$str = "";
$action = $input['action'];
$email = $input['email'];
$msg = $input['msg'];

//print_r($input);
switch ($action) {
    case 'getJSON':

        $url = 'http://toyota-credit.360d.ru/cars/Models/all.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result1 = json_decode($result, true);
        foreach ($result1 as $key => $value) {
            $k[$key] = $value;
            foreach ($k[$key] as $key1 => $value1) {
                $grades[$key] = $value1['grades'];
                $grades2 = serialize($grades[$key]);
                insert($link, "cars", "car_name,car_id,sprite", "'$value1[title]','$value1[id]','$value1[sprite]'", "car_name='$value1[title]' AND car_id='$value1[id]' AND sprite='$value1[sprite]'"); //insert cars
                foreach ($grades[$key] as $key2 => $value2) {
                    $features = implode(", ", $value2['features']);
                    $colors[$key2] = $value2['colors'];
                    $colors2 = serialize($value2['colors']);
                    $technicalSpecification[$key2] = $value2['technicalSpecification'];
                    $technicalSpecification2 = serialize($value2['technicalSpecification']);
//                    $accessories2 = serialize($value2['accessories']);
//                    $options2 = serialize($value2['options']);
                    insert($link, "grades", "car_id,title,grade_id,engine_desc,wheeldrive,price,pricediscount,engine,transmission,body,features", "'$value1[id]','$value2[title]','$value2[id]','$value2[engine_desc]','$value2[wheeldrive]','$value2[price]','$value2[pricediscount]',
                                '$value2[engine]','$value2[transmission]','$value2[body]','$features'", "grade_id='$value2[id]'"); //insert grades
                    foreach ($colors[$key2] as $key3 => $value3) {
                        insert($link, "colors", "grade_id,color_id,rgb,code,title,type,price,swatch,image", "'$value2[id]','$value3[id]','$value3[rgb]','$value3[code]','$value3[title]','$value3[type]','$value3[price]','$value3[swatch]','$value3[image]'", "grade_id='$value2[id]' AND color_id='$value3[id]'"); //insert colors
                    }
                    foreach ($technicalSpecification[$key2] as $key4 => $value4) {
                        insert($link, "technicalSpecification", "title,grade_id,details,type", "'$value4[title]','$value2[id]','$value4[details]','$value4[type]'", "title='$value4[title]' AND grade_id='$value2[id]' AND details='$value4[details]' AND type='$value4[type]'"); //inserttechnicalSpecification
                    }
                }
            }
        }
        $answer = array("answer" => 'ok', 'result' => 'ok', 'rows' => $rows);
        echo json_encode($answer);
        break;
    case 'getCars':
        $str = "";
        $result = mysqli_query($link, $sql = 'select car_name, car_id from cars');
        while ($row = mysqli_fetch_array($result)) {
            $str .= '<option value="' . $row['car_id'] . '">' . $row['car_name'] . '</option>';
        }
        $answer = array("answer" => 'ok', 'result' => $str);
        echo json_encode($answer);
        break;
    case 'getGrades':
        $str = "";
        $car_id = mysqli_real_escape_string($link, $input['car_id']);
        $result = mysqli_query($link, $sql = "select title, grade_id,engine_desc from grades where car_id='" . $car_id . "'");
        while ($row = mysqli_fetch_array($result)) {
            $str .= '<option value = "' . $row['grade_id'] . '">' . $row['engine_desc'] . '</option>';
        }
        $answer = array("answer" => 'ok', 'result' => $str);
        echo json_encode($answer);
        break;
    case 'getGradesAll':
        $str = "";
        $grade_id = mysqli_real_escape_string($link, $input['grade_id']);
        $str .= '<table class="table gradesalltable"> <thead> <tr><th scope="col">Параметр</th><th scope="col">Подробности</th></tr></thead><tbody>';
        $result = mysqli_query($link, $sql = "select title, grade_id,engine_desc,wheeldrive,price,pricediscount,engine,transmission,body,features from grades where grade_id='" . $grade_id . "'");

        while ($row = mysqli_fetch_array($result)) {
            $str .= '<tr><td>title</td><td>' . $row[title] . '</td></tr>
                        <tr><td>engine_desc</td><td>' . $row[engine_desc] . '</td></tr> 
                        <tr><td>wheeldrive</td><td>' . $row[wheeldrive] . '</td></tr> 
                        <tr><td>price</td><td>' . $row[price] . '</td></tr> 
                        <tr><td>pricediscount</td><td>' . $row[pricediscount] . '</td></tr> 
                        <tr><td>engine</td><td>' . $row[engine] . '</td></tr> 
                        <tr><td>transmission</td><td>' . $row[transmission] . '</td></tr> 
                        <tr><td>body</td><td>' . $row[body] . '</td></tr> 
                        <tr><td>features</td><td><a href="#" id="togglefeat">Подробнее</a> <div class="features">' . str_replace(",", ",<br/>", $row[features]) . '</div></td></tr>
                        <tr><td>colors</td><td><a href="#" id="togglecolors">Подробнее</a> <div class="colorstablediv"></div></td></tr> 
                        <tr><td>technicalSpecification</td><td><a href="#" id="toggletechspec">Подробнее</a> <div class="techspectablediv"></div></td></tr> 
                    ';
        }
        $str .= '</tbody></table>';
        $answer = array("answer" => 'ok', 'result' => $str);
        echo json_encode($answer);
        break;
    case 'getColors':
        $str = "";
        $grade_id = mysqli_real_escape_string($link, $input['grade_id']);
        $str .= '<table class="table table-sm colorstable"> <thead> <tr><th scope="col">title</th><th scope="col">rgb</th><th scope="col">code</th><th scope="col">type</th><th scope="col">price</th></tr></thead><tbody>';
        $result = mysqli_query($link, $sql = "select title, grade_id,color_id,rgb,code,type,price,swatch,image from colors where grade_id='" . $grade_id . "'");

        while ($row = mysqli_fetch_array($result)) {
            $str .= '<tr><td>' . $row[title] . '</td><td style="background-color:#' . $row[rgb] . '">' . $row[rgb] . '</td><td>' . $row[code] . '</td><td>' . $row[type] . '</td><td>' . $row[price] . '</td></tr>
                                             ';
        }
        $str .= '</tbody></table>';
        $answer = array("answer" => 'ok', 'result' => $str);
        echo json_encode($answer);
        break;
    case 'getTechspec':
        $str = "";
        $grade_id = mysqli_real_escape_string($link, $input['grade_id']);
        $str .= '<table class="table table-sm techspectable"> <thead> <tr><th scope="col">title</th><th scope="col">details</th><th scope="col">type</th></tr></thead><tbody>';
        $result = mysqli_query($link, $sql = "select title, grade_id,details,type from technicalSpecification where grade_id='" . $grade_id . "'");

        while ($row = mysqli_fetch_array($result)) {
            $str .= '<tr><td>' . $row[title] . '</td><td>' . $row[details] . '</td><td>' . $row[type] . '</td></tr>
                                             ';
        }
        $str .= '</tbody></table>';
        $answer = array("answer" => 'ok', 'result' => $str);
        echo json_encode($answer);
        break;
    default : echo "Нет команды";
}


    