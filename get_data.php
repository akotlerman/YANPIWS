<?php
/**
 * Assuming a CSV of this structure:
 *  2017-03-22 23:11:43,211,72.5,34
 * Return a multi-dimensional array like this:
 *
 *Array(
 *    [211] => Array(
 *        [2017-03-22 23:08:31] => Array(
 *                    [0] => 2017-03-22 23:08:31
 *                    [1] => 211
 *                    [2] => 72.5
 *                    [3] => 34
 *               )
 *
 * @param string $file where CSV data is
 * @return array of formatted CSV data
 */

function getData($file){
    if (is_file($file) && is_readable($file)){
        $data = file($file);
        $goodData = array();
        foreach ($data as $line){
            $lineArray = explode(",", $line);
            $goodData[$lineArray[1]][$lineArray[0]] = $lineArray;
        }
        foreach (array_keys($goodData) as $id){
            asort($goodData[$id]);
        }

        return $goodData;
    } else {
        return array();
    }
}

function getMostRecentTemp($id, $date = null){
    global $YANPIWS;
    if ($date == null){
        $date = date('Y-m-d', time());
    }
    $allData = getData($YANPIWS['dataPath'] . $date);
    return array_pop($allData[$id]);
}

function getTempHtml($tempLine){
    global $YANPIWS;
    $key = $tempLine[1];
    if (isset($YANPIWS['labels'][$key])){
        $label = $YANPIWS['labels'][$key];
    } else {
        $label = "ID $key";
    }
    return "<div class='temp'><strong>{$tempLine[2]}°</strong> $label </div>\n";
}

function getSunriseTime(){
    global $YANPIWS;
    return date(
        'g:iA',
        date_sunrise(
            time(),
            SUNFUNCS_RET_TIMESTAMP,
            $YANPIWS['lat'],
            $YANPIWS['lon'],
            90,
            $YANPIWS['gmt_offset']
        )
    );
}
function getSunsetTime(){
    global $YANPIWS;
    return date(
        'g:iA',
        date_sunset(
            time(),
            SUNFUNCS_RET_TIMESTAMP,
            $YANPIWS['lat'],
            $YANPIWS['lon'],
            90,
            $YANPIWS['gmt_offset']
        )
    );
}

function getDarkSkyData(){
    global $YANPIWS;
    return json_decode(file_get_contents('https://api.darksky.net/forecast/' .
        $YANPIWS['darksky'] . '/' . $YANPIWS['lat'] . ',' . $YANPIWS['lon']));
}

function getDailyForecastHtml($daily){
    $html = '';
    $js = '';
    foreach ($daily->data as $day){
        $rand = rand(99999,999999999999);
        $today = substr(date('D',$day->time),0,1);
        $html .= "<div class='forecastday'>";
        $html .= $today;
        $html .= " <canvas id='$today.$rand' width='32' height='32'></canvas>";
        $html .= ' H ' . number_format($day->temperatureMax,0) .'°';
        $html .= ' L ' .number_format($day->temperatureMin,0).'°';
        $html .= ' ' .number_format($day->windSpeed,0) . 'mph';
        $html .= '</div>';

        $js .= "skycons.add('$today.$rand', '$day->icon');\n";
    }
    $html .= "
        <script src='skycons/skycons.js'></script>
        <script>
          var skycons = new Skycons({'color': 'white'});
          $js
        </script>
    ";
    return $html;
}