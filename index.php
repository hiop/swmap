<?php

header('Content-type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
  <head>
  <meta NAME="Description" CONTENT="spice and wolf map"> 
	<meta NAME="Keywords" CONTENT="spice, and, wolf, map, horo, holo, lawrence, craft"> 
	<meta NAME="Robots" CONTENT="ALL"> 
	<meta NAME="Revisit-After" CONTENT="1 Days"> 
	<meta NAME="Author" CONTENT="Hiop"> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Spice & Wolf Map</title>
	<link rel="stylesheet" media="all" type="text/css" href="./themes/main.css" />	
	<link rel="stylesheet" media="all" type="text/css" href="./themes/base.css" />	
	<script type="text/javascript" src="./js/jquery-1.9.1.min.js"></script>
	<script  type="text/javascript" src="./js/jquery-impromptu.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

    <script>
	$(document).ready(function(){
		//$.prompt('Кликайте по яблокам, чтобы получить информацию.');
		
		$("#panel").click(function(){
		
			$.prompt(
			'<div align="center">'+
			'<div><img width="100%" height="100%"  src="./img/logo.jpg"></div>'+
			'<div>Том №1 <img width="16" height="16"  src="./img/check.png"></div>'+
			'<div>Том №2 <img width="16" height="16"  src="./img/check.png"></div>'+
			'<div>Том №3 <img width="16" height="16"  src="./img/check.png"></div>'+
			'<div>Том №4 <img width="16" height="16"  src="./img/check.png"></div>'+
			'<div>Том №5 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №6 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №7 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №8 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №9 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №10 <img width="16" height="16"  src="./img/check.png"/> </div>'+
			'<div>Том №11 <img width="16" height="16"  src="./img/shesterenka.png"/> </div>'+
			'<div> ... </div>'+
			'<img width="205" height="200" src="./img/horo_spoiler.jpg"/>'+
			'</div>'
			);
		
		})

	
var GALL_PETERS_RANGE_X = 800;


var GALL_PETERS_RANGE_Y = 510;

function degreesToRadians(deg) {
  return deg * (Math.PI / 180);
}

function radiansToDegrees(rad) {
  return rad / (Math.PI / 180);
}

/**
 * @constructor
 * @implements {google.maps.Projection}
 */
function GallPetersProjection() {

  // Using the base map tile, denote the lat/lon of the equatorial origin.
  this.worldOrigin_ = new google.maps.Point(GALL_PETERS_RANGE_X * 400 / 800,
      GALL_PETERS_RANGE_Y / 2);

  // This projection has equidistant meridians, so each longitude degree is a linear
  // mapping.
  this.worldCoordinatePerLonDegree_ = GALL_PETERS_RANGE_X / 360;

  // This constant merely reflects that latitudes vary from +90 to -90 degrees.
  this.worldCoordinateLatRange = GALL_PETERS_RANGE_Y / 2;
};

GallPetersProjection.prototype.fromLatLngToPoint = function(latLng) {

  var origin = this.worldOrigin_;
  var x = origin.x + this.worldCoordinatePerLonDegree_ * latLng.lng();

  // Note that latitude is measured from the world coordinate origin
  // at the top left of the map.
  var latRadians = degreesToRadians(latLng.lat());
  var y = origin.y - this.worldCoordinateLatRange * Math.sin(latRadians);

  return new google.maps.Point(x, y);
};

GallPetersProjection.prototype.fromPointToLatLng = function(point, noWrap) {

  var y = point.y;
  var x = point.x;

  if (y < 0) {
    y = 0;
  }
  
  if (x < 0) {
    x = 0;
  }
  if (y >= GALL_PETERS_RANGE_Y) {
    y = GALL_PETERS_RANGE_Y;
  }
  
  if (x >= GALL_PETERS_RANGE_X) {
    x = GALL_PETERS_RANGE_X;
  }

  var origin = this.worldOrigin_;
  var lng = (x - origin.x) / this.worldCoordinatePerLonDegree_;
  var latRadians = Math.asin((origin.y - y) / this.worldCoordinateLatRange);
  var lat = radiansToDegrees(latRadians);
  return new google.maps.LatLng(lat, lng, noWrap);
};



function initialize() {

  var gallPetersMap;

  var gallPetersMapType = new google.maps.ImageMapType({
    getTileUrl: function(coord, zoom) {
      var numTiles = 1 << zoom;

      // Don't wrap tiles vertically.
     if (coord.x < 0 || coord.x >= numTiles) {
        return null;
      }

      // Wrap tiles horizontally.
      var x = ((coord.x % numTiles) + numTiles) % numTiles;

      // For simplicity, we use a tileset consisting of 1 tile at zoom level 0
      // and 4 tiles at zoom level 1. Note that we set the base URL to a
      // relative directory.
      var baseURL = './tiles/';
      baseURL += 'tile_' + zoom + '_' + x + '_' + coord.y + '.png';
      return baseURL;
    },
    tileSize: new google.maps.Size(209, 300),
    isPng: true,
    minZoom: 1,
    maxZoom: 3,
    name: 'Волчица и Пряности'
  });
  
  gallPetersMapType.projection = new GallPetersProjection();

  var mapOptions = {
    zoom: 1,
	scaleControl: 0,
	mapTypeControl: false,
	
	
    center: new google.maps.LatLng(25,-133),
    mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.TERRAIN, 'gallPetersMap']
    },
	 scaleControl: false,
	  streetViewControl: false
  };
  gallPetersMap = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  gallPetersMap.mapTypes.set('gallPetersMap', gallPetersMapType);
  gallPetersMap.setMapTypeId('gallPetersMap');
  


  google.maps.event.addListener(gallPetersMap, 'click', function(event) {
	
	if(MarkerName)
	{
	infowindow.close();
	MarkerName = 0;
	}

	
  });

  var image = {
    url: './img/horo.png',

    size: new google.maps.Size(25, 25)
	};
	
	  var image2 = {
    url: './img/horo2.png',

    size: new google.maps.Size(25, 25)
	};
  

  
  
  
  var marker = new google.maps.Marker({
	  
      position: new google.maps.LatLng(1.52, -119.76),
      map: gallPetersMap,
	  book: 1,
      title: 'д. Йорент',
	   action: '<ul><li>Начало. Лоуренс</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"><img width="200" height="300" src="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"></a>',
	  icon: image
	  });
	getMarkerInfo(marker);
	
	
	
	 var marker2 = new google.maps.Marker({
      position: new google.maps.LatLng(0.6459955142436608, -129.83125000000001),
      map: gallPetersMap,
      book: 1,
      title: 'д. Пасро',
	   action: '<ul><li>Встреча с Хоро</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"></img></a>',
	    icon: image
    });
	getMarkerInfo(marker2);
	
		 
	
		var marker3 = new google.maps.Marker({
      position: new google.maps.LatLng(4.42382575920884, -140.5125),
      map: gallPetersMap,
      book: 1,
      title: 'г. Паттио',
	  action: '<ul><li>Гильдия Медиоха</li> <li> Афёра с монетами</li> <li> Похищение Хоро</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_01_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker3);
	
	var marker4 = new google.maps.Marker({
      position: new google.maps.LatLng(9.509340921637084, -126.50625000000002),
      map: gallPetersMap,
      book: 2,
      title: 'г. Поросон',
	  action: '<ul><li>Разоблачение</li> <li>Перец</li> <li>Доспехи</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"></img></a>',
	  icon: image
	  
    });
	getMarkerInfo(marker4);
	
	
	var marker5 = new google.maps.Marker({
      position: new google.maps.LatLng(8.809340921637084, -143.41249999999998),
      map: gallPetersMap,
      book: 2,
      title: 'г. Рубинхейген',
	  action: '<ul><li>Монашка</li> <li>На грани разорения</li> <li>Гильдии Ремарио и Ровена</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker5);
	
	
	var marker6 = new google.maps.Marker({
      position: new google.maps.LatLng(15.903597718405647, -136.46249999999998),
      map: gallPetersMap,
      book: 2,
      title: 'д. Раммтора',
	  action: '<ul><li>Контрабанда</li> <li>Волки</li> <li>Лжец</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_02_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker6);

	
	var marker7 = new google.maps.Marker({
      position: new google.maps.LatLng(20.4072924293975, -149.17499999999995),
      map: gallPetersMap,
      book: 3,
      title: 'г. Кумерсон',
	  action: '<ul><li>Сведения о Йойтсу</li> <li>Праздник</li> <li>Пирит</li> </ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_03_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_03_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker7);
	
		var marker8 = new google.maps.Marker({
      position: new google.maps.LatLng(21.715233366740966, -125.08124999999995),
      map: gallPetersMap,
      book: 4,
      title: 'г. Энберл',
	  action: '<ul><li>Гильдия Риендо</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_04_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_04_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker8);
	
		var marker9 = new google.maps.Marker({
      position: new google.maps.LatLng(23.800180326439748, -122.61250000000001),
      map: gallPetersMap,
      book: 4,
      title: 'д. Терео',
	  action: '<ul><li>Мукомол и монашка</li><li>"Война"</li> <li>"Чудо"</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_04_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_04_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker9);
	
		var marker10 = new google.maps.Marker({
      position: new google.maps.LatLng(26.358626391301317, -123.88749999999999),
      map: gallPetersMap,
      book: "5, 6",
      title: 'г. Реноз',
	  action: '<ul><li>Совет Пятидесяти</li><li>Таинственный торговец</li><li>Сведения о Хоро</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_05_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_05_p000a.jpg"></img></a><a href="./Books/Spice_and_Wolf_Vol_06_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_06_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker10);
	
	
		var marker11 = new google.maps.Marker({
      position: new google.maps.LatLng(31.105950066774475, -132.99999999999995),
      map: gallPetersMap,
      book: "6",
      title: 'р. Ром',
	  action: '<ul><li>Лодочник</li><li>Школяр</li><li>Неожиданная остановка</li></ul>',
	  img: '</a><a href="./Books/Spice_and_Wolf_Vol_06_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_06_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker11);
	
	var marker12 = new google.maps.Marker({
      position: new google.maps.LatLng(4.42382575920884, -142.5125),
      map: gallPetersMap,
      book: 7,
      title: 'г. Паттио',
	  action: '<ul><li>Меняла</li><li>Трюк с одеждой</li><li>Яблоки</li></ul>',
	  img: '</a><a href="./Books/Spice_and_Wolf_Vol_07_p000a.png"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_07_p000a.jpg"></img></a>',
	  icon: image2
    });
	getMarkerInfo(marker12);
	
		var marker13 = new google.maps.Marker({
      position: new google.maps.LatLng(8.809340921637084, -145.41249999999998),
      map: gallPetersMap,
      book: 7,
      title: 'г. Рубинхейген',
	  action: '<ul><li>Часть истории глазами Хоро</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_07_p000a.png"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_07_p000a.jpg"></img></a>',
	  icon: image2
    });
	getMarkerInfo(marker13);
	
	
	var marker14 = new google.maps.Marker({
      position: new google.maps.LatLng(36.36300306814721, -136.6875),
      map: gallPetersMap,
      book: 8,
      title: 'г. Кербе',
	  action: '<ul><li>Старые знакомые</li><li>Кости</li><li>Тоговый дом Джин</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_08_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_08_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker14);
	
		var marker15 = new google.maps.Marker({
      position: new google.maps.LatLng(36.62453695936636, -134.39374999999995),
      map: gallPetersMap,
      book: 9,
      title: 'г. Кербе',
	  action: '<ul><li>Гость из моря</li><li>Посыльный</li><li>Коварный план</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_09_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_09_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker15);

	
	var marker16 = new google.maps.Marker({
      position: new google.maps.LatLng(36.35137065486503, -155.30624999999998),
      map: gallPetersMap,
      book: 10,
      title: 'Королевство Уинфилд',
	  action: '<ul><li>Король, альянс и монастырь</li><li>Старый пастух и молодой торговец</li><li>Бешенное стадо</li></ul>',
	  img: '<a href="./Books/Spice_and_Wolf_Vol_10_p000a.jpg"><img width="200px" height="300px" src="./Books/Spice_and_Wolf_Vol_10_p000a.jpg"></img></a>',
	  icon: image
    });
	getMarkerInfo(marker16);
	
	
	
  
  
   var lineSymbolF = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
  };
  var lineSymbolB = {
    path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW
  };
 

  
  pushLine(lineSymbolF,0.8707046720967363, -119.256250000000023,-0.4212962927607933, -129.71875);
  pushLine(lineSymbolF,-0.4212962927607933, -129.71875, 3.259753583520744, -140.96875);
  pushLine(lineSymbolF,3.259753583520744, -140.96875, 8.768991274066593, -126.50625000000002);
  pushLine(lineSymbolF,8.768991274066593, -126.50625000000002, 8.5866610899060305, -143.72500000000002);
  pushLine2(lineSymbolF,lineSymbolB,8.72804528078785, -143.60625, 14.825874988941138, -134.66250000000002);
  pushLine(lineSymbolF,8.72804528078785, -143.60625, 19.23307590204491, -148.83749999999998);
  pushLine(lineSymbolF,19.23307590204491, -148.83749999999998, 20.93771979888872, -125.71875);
  pushLine(lineSymbolF,20.93771979888872, -125.71875, 22.93619978306081, -122.85000000000002);
  pushLine(lineSymbolF,22.93619978306081, -122.85000000000002,25.430235047117716, -125.10000000000002);
  pushLine(lineSymbolF,25.430235047117716, -125.10000000000002,30.781447564203273, -124.70625000000001);
  pushLine(lineSymbolF,30.781447564203273, -124.70625000000001,31.305950066774475, -127.74374999999998);
  pushLine(lineSymbolF,31.305950066774475, -127.74374999999998,31.076124383782545, -132.97500000000002);
  pushLine(lineSymbolF,31.076124383782545, -132.97500000000002,35.84028920736527, -136.11249999999995);
  pushLine(lineSymbolF,35.84028920736527, -136.11249999999995, 36.10137065486503, -155.30624999999998);
  
 
  
  
   function pushLine(x1,a,b,c,d)
 {
 
   lineCoordinates = [
   new google.maps.LatLng(a,b),
      new google.maps.LatLng(c,d)
  ];

   line = new google.maps.Polyline({
    path: lineCoordinates,
    icons: [{
      icon: x1,
      offset: '100%'
    }],
	map: gallPetersMap
  });
  
  };
  
     function pushLine2(x1,x2,a,b,c,d)
	{
 
   lineCoordinates = [
   new google.maps.LatLng(a,b),
      new google.maps.LatLng(c,d)
  ];
			line = new google.maps.Polyline({
			path: lineCoordinates,
			icons: [{
			  icon: x1,
			  offset: '50%'
			},
			{
			  icon: x2,
			  offset: '100%'
			}],
			map: gallPetersMap
			})
	  
	
	};

  var MarkerName;



 
 function getMarkerInfo(marker)
 {
		 

	var markerF = marker;
    google.maps.event.addListener(markerF, 'click', function() {
	if(MarkerName)
	{
	infowindow.close();
	MarkerName = 0;
	}

	contentString = 
	  '<div style="line-height:1.35;overflow:hidden;white-space:nowrap; padding-left:20px; padding-top:5px;padding-bottom:5px;" >'+
	  '<div><b>Том</b> '+markerF.book+'</div>'+
	  '<div><b>Где</b>: '+markerF.title+'</div>'+
	  '<div><b>Описание</b>: '+markerF.action+'</div>'+
	  '<div align="center">'+markerF.img+'</div>'+
	  '</div>';

	
	 infowindow = new google.maps.InfoWindow({
      content: contentString
  });
		
	   infowindow.open(gallPetersMap,markerF);
	   MarkerName = 1;
	   
  });
 }
 

 
 google.maps.event.addListener(map,'zoom_changed', function()
			 { 
			   if (map.getZoom() < 3){ 
				  map.setZoom(3); 
			   } 
			});
 
};

google.maps.event.addDomListener(window, 'load', initialize);

});
    </script>
  </head>
  <body style="overflow: hidden;">
  
  <div id="panel">
	<div> [Кликни меня] </div>
    </div>
  <div id="dev">
	<div>Разработал <b>Hiop</b></div>
   </div>

    <div id="map-canvas"></div>
  </body>
</html>