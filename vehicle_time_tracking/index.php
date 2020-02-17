<!DOCTYPE html>
<html>
<head>
	<title>Vehicle Time Tracking Test By Moshahed Update</title>
	<!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> -->
	<script src = "js/jquery-1.7.2.min.js"></script>
	<script src= "js/highstock.js"></script>
	<!-- <script src="https://code.highcharts.com/stock/modules/exporting.js"></script> -->
	<script src="http://code.highcharts.com/maps/modules/map.js"></script>
	<style>
	    .highcharts-legend-item {
	        display:none
	    }
	    .highcharts-credits {
	    	display:none;
	    }
	</style>
</head>
<body>
<?php
	$jsonString        = file_get_contents("data.json");
	$jsonAarray        = json_decode($jsonString);
	$lineArray         = array();
	$FuelReadingActual = '';
	$vehicleReadingOne = 0;
	$vehicleReadingTwo = 0;
	$KeyOne            = '';
	$KeyTwo            = '';

foreach ($jsonAarray as $key => $row) 
{
    if ($row->xIsAlarm == '1') 
    {
		$imgHeight               = '28';
		$imgWidth                = '15';
		$imageType               = 'url(img/imgRefuel.png)';
		$vehicleReading          = $row->xVehFuelReading;
		$KeyOne                  = $key + 1;
		$vehicleReadingOne       = $row->xVehFuelReading - $jsonAarray[$KeyOne]->xVehFuelReading; // current index - next index
		$FuelReadingActual       = $vehicleReadingOne;
	} elseif ($row->xIsAlarm == '2') {
		$imgHeight               = '28';
		$imgWidth                = '15';
		$imageType               = 'url(img/imgTheft.png)';
		$vehicleReading          = $row->xVehFuelReading;
		$KeyTwo                  = $key - 1;
		$vehicleReadingTwo       = $row->xVehFuelReading - $jsonAarray[$KeyTwo]->xVehFuelReading; // current index - previous index
		$FuelReadingActual       = $vehicleReadingTwo;
	} elseif ($row->xIsAlarm == '0') {
		$imgHeight               = '8';
		$imgWidth                = '8';
		$imageType               = 'url(img/equal-green.png)';
		$FuelReadingActual       = '';
		$vehicleReading          = '';
    }

	$FuelReadingActual = strval($FuelReadingActual); // int val to string 
	$toolTipString     = "Actual Reading: $FuelReadingActual,<br> Vehicle Name: $row->xVehName,<br> Time: $row->xVehTime<br> VehFuelReading: $row->xVehFuelReading<br>  Engine: $row->xEngin <br> SL: $row->xSL <br> Vehcle Time In Number: $row->xVehTimeInNumber<br> L_Lat: $row->xL_Lat <br>  L_Lon: $row->xL_Lon <br> Is Alarm: $row->xIsAlarm<br> Time Duration: $row->xTimeDuration <br> Travel Distance: $row->xTravelDistance";
	$json_array2       = "FuelReading=$row->xVehFuelReading&VehicleName=$row->xVehName&Engin=$row->xEngin&IsAlarm=$row->xIsAlarm&time=$row->xVehTime";
	$siteURL           = strval('https://vts.grameenphone.com/');

    $lineArray[] = array(
		'y'                    => (float) $row->xVehFuelReading,
		//'label'              => (float) $FuelReadingActual,
		'name'                 => $toolTipString,
		'marker'               => array(
		'symbol'               => $imageType,
		'width'                => $imgWidth,
		'height'               => $imgHeight,
		//'enabledThreshold'   => 5
		),
		//'label'              => $row->xVehTime,
		'VehTime'              => $row->xVehTime,
		'xVehName'             => $row->xVehName,
		'fuelActualReading'    => $FuelReadingActual,
		//'indexLabel'         => $row->xVehFuelReading,
		'xEngin'               => $row->xEngin,
		'xSL'                  => $row->xSL,
		'xVehTime'             => $row->xVehTime,
		'xVehTimeInNumber'     => $row->xVehTimeInNumber,
		'xL_Lat'               => $row->xL_Lat,
		'xL_Lon'               => $row->xL_Lon,
		'xIsAlarm'             => $row->xIsAlarm,
		'xVehFuelReading_Text' => $row->xVehFuelReading_Text,
		'xVehFuelReading'      => $row->xVehFuelReading,
		'xIsAlarm_Ref'         => $row->xIsAlarm_Ref,
		'xTimeDuration'        => $row->xTimeDuration,
		'xTravelDistance'      => $row->xTravelDistance,
		'siteurl'              => $siteURL,
		'siteurlValue'         => $json_array2,
    );
}

$lineArray2 = json_encode($lineArray);
$itemCount = count($lineArray);

// if( $itemCount >= 25 ) :
// 	$scrollbar = 'true';
// else :
// 	$scrollbar = 'false';
// endif;	

?>
<script type="text/javascript">

    $(function () {
        $('#container').highcharts({
            chart: {
            	//backgroundColor: 'red',
            	animation: false,
                panning: true,
                pinchType: 'x',
  
            },
            title: {
		        text: 'Vehicle Time Tracking',
		        align: 'center',
		        // x: 70
		    },
            xAxis: {
                categories: [],
                min: 0,
                max: <?php echo $itemCount >= 25 ?  25 :  $itemCount ; ?>,

            },
            navigator: {
                enabled: false
            },
            scrollbar: {
                enabled: <?php echo $itemCount >= 25 ?  'true' :  'false'; ?>,
                liveRedraw: false
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                pointFormat: false
            },

            yAxis: {
                allowDecimals: false,
                title: {
                            text: 'Fuel Reading',
                        },
            },

            /*
	            turboThreshold: numberSince 2.2.0
				When a series contains a data array that is longer than this, only one dimensional arrays of numbers, or two dimensional arrays with x and y values are allowed. Also, only the first point is tested, and the rest are assumed to be the same format. This saves expensive data checking and indexing in long series. Set it to 0 disable.
				Defaults to 1000.
            */
            series: [{
                    name: '',
                    cursor: 'pointer',
                    allowPointSelect: true,
                    marker: {
                        symbol: 'square'
                    },
                    turboThreshold : <?php echo $itemCount <= 1000 ?  1000 :  $itemCount ; ?>,
                    data: <?php echo $lineArray2; ?>,
                    dataLabels : {
                        enabled: true,
                        useHTML: true,
		                formatter: function() {
						    return this.point.xIsAlarm > 0 ? '<p>&nbsp;</p><div style="margin-top:-55px">'+ this.point.fuelActualReading +'</div>' : '';
						}
                    },
                    
                }
            ],
            exporting: { enabled: true,width: 1200 },

             
            plotOptions: {
		        series : {
		            cursor: 'pointer',
		            point: {
		                events: {
		                    click: function () {
								var width          = 400;
								var height         = 400;
								var left           = parseInt((screen.availWidth/2) - (width/2));
								var top            = parseInt((screen.availHeight/2) - (height/2));
								var window_box     = '<!DOCTYPE html><html><head><title>Vehicle Time Tracking</title></head><style> #customers {  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;  border-collapse: collapse; width: 100%; } #customers td, #customers th {   border: 1px solid #ddd;  padding: 8px; } #customers tr:nth-child(even){background-color: #f2f2f2;}  #customers tr:hover {background-color: #ddd;}  #customers th {  padding-top: 12px; padding-bottom: 12px; text-align: left; background-color: #4CAF50; color: white;  } </style><body>';
								var windowFeatures = "width=" + width + ",height=" + height + ",status,resizable,left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
								window_box        += '<table id="customers"><tr><td align="center" style="font-size:20px" colspan="2"><b>Vehicle Time Tracking</b></td></tr><tr><th>Name</th> <th>Value</th> </tr><tr><td>VehName</td><td>'+ event.point.xVehName +'</td></tr><tr><td>Engin</td><td>'+ event.point.xEngin  +'</td> </tr><tr><td>VehTime</td><td>'+ event.point.xVehTime  +'</td></tr><tr><td>Fuel Reading</td><td>'+ event.point.xVehFuelReading+'</td></tr><tr><td>Vehcle Time In Number</td><td>'+ event.point.xVehTimeInNumber+'</td></tr><tr><td>Actual Reading</td><td>'+ event.point.fuelActualReading+'</td></tr><tr><td>Alarm</td><td>';
								window_box        += event.point.xIsAlarm == 1 ? '<img src="img/Refuel.png" width="40" height="40" >' : '';
								window_box        += event.point.xIsAlarm == 2 ? '<img src="img/Theft.png" width="40" height="40" >' : '';
								window_box        += '</td></tr></table></body></html>';
								myWindow           = window.open('', "subWind", windowFeatures);
		                    	myWindow.document.write(window_box);
		                    }
		                }
		            }
		        }
		    }
        });
    });

</script>



<table border="1" style="width: 70%;height: 20%" align="center">
	<tr><td><div id="container" style="height: 450px; width: 100%"></div></td></tr>
</table>
<table border="1" style="width: 30%;margin-top: 50px;text-align: center" align="center">
	<tr><td  align="center" colspan="2">Summery</td></tr>
	  <tr>
	    <th style="text-align: center;" >Name</th> 
	    <th style="text-align: center;" >Value</th>
	  </tr>
	  <tr>
	  	<td><img style="" src="img/imgRefuel.png" height="35" width="18" /> &nbsp;Refuel</td>
	  	<td>30</td>
	  </tr>
	  <tr>
	  	<td><img src="img/imgTheft.png"  height="35" width="18"  /> &nbsp;Theft</td>
	  	<td>25</td>
	  </tr>
	  	  <tr>
	  	<td><img src="img/equal-green.png" height="10" width="10" /> Others</td>
	  	<td>25</td>
	  </tr>
</table>


<!-- <div class="container">
	<div class="row">&nbsp;</div>
	<div class="row">
		<div class="col-md-10">
			<div id="container" style="height: 500px; width: 100%"></div>
		</div>
		<div class="col-md-2">
			<div style="float:left; margin-left: 40px">
		    	<div style="text-align: left; margin-top: 90px">
		            <p><img style="" src="img/imgRefuel.png" height="35" width="18" /> Refuel</p>
		            <p><img src="img/imgTheft.png"  height="35" width="18"  /> Theft</p>
		            <p >&nbsp;<img src="img/equal-green.png" height="10" width="10" /> Others</p>
		        </div>
		    </div>
		</div>
	</div>
</div> -->
</body>
</html>