
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="50; URL=index.php">
</head>
<body>
<link rel="stylesheet" type="text/css" href="styles.css" />
<!--<link rel="stylesheet" type="text/css" href="styles-mini.css" />-->
<?php
require_once ("get_data.php");
getConfigOrDie();

$today = date('Y-m-d', time());
$time = date('g:i A', time());
$date = date('D M j', time());
$allData = getData($YANPIWS['dataPath'] . $today);

$currentTempHtml = '';
$count = 1;
foreach ($YANPIWS['labels'] as $id => $label){
    $tempLine = getMostRecentTemp($id);
    $currentTempHtml .= getTempHtml($tempLine, $count++);
}
$sunset=  getSunsetHtml(getSunsetTime());
$sunrise  =  getSunriseHtml(getSunriseTime());

$forecast = getDarkSkyData();
$forecastHtml = getDailyForecastHtml($forecast->daily);
?>
<div class="YANPIWS"><a href="/stats.php">YANPIWS</a></div>
<div class="col">
    <div class="row"><?php echo $currentTempHtml ?></div>
    <div class="row ">
        <div class=" time"><?php echo $time ?></div>
        <div class="date"><?php echo $date ?></div>
    </div>
    <div class="row suntimes"><?php echo $sunrise ?><?php echo $sunset ?></div>
</div>
<div class="col rigthtCol">
    <?php echo $forecastHtml ?>
</div>

</body>
</html>