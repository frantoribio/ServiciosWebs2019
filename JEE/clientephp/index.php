<?php
    // Cargamos la libreria    
    require_once "nusoap.php";
    // Iniciamos el servicio
    $servicio = "http://localhost:8080/Servicio/Servicio?wsdl";
    $cliente  = new SoapClient($servicio);

    // Si quremos ver los métodos que tiene nuestro servicio
    //print_r($client->__getFunctions()); 
    //print_r($client->__getTypes()); 

    require_once "cabecera.php";
    
    // Atención estas funciones son importantes
    // La necesitamos para convertir un objeto en un array
    // Lo usaremos con los parámetros de entradam sobre todo si
    // Son del tipo complejo o usamos dos parámetros (tambien con 1 si quremos)
    function objectToArray($d)
	{
		if (is_object($d))
		{
			$d = get_object_vars($d);
		}
 
		if (is_array($d))
		{
			return array_map(__FUNCTION__, $d);
		}
		else
		{
			return $d;
		}
    }
    // convierte un array en object
    // Importante si devolvemos un objeto del tipo complejo o estructurado
    function array2Object($d)
	{
		if (is_array($d))
		{
			return (object) array_map(__FUNCTION__, $d);
		}
		else
		{
			return $d;
		}
    }
    // Saco un Alert JS
    function alerta($texto) {
        echo '<script type="text/javascript">alert("' . $texto . '")</script>';
    }


?>

<div class="jumbotron">
  <h1 class="display">Cliente WS - SOAP</h1>
  <p class="lead">Cliente que consume un servicio web a través de SOAP y WSDL</p>
</div>

<!-- Listado de alumnos -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Listado de Alumnos WS-SOAP</div>
                <div class="panel-body">
                <?php
                    // consumimos el servicio SOAP
                    // Esto es porque he decidido que sea json para no tratar con datos complejos
                    $std = new stdClass;
                    $std->nombre ="";
                    $params = objectToArray($std);
                    //$params = array("nombre"=>""); Podríamos hacerlo así, pero ya veremos que tenemos 
                    // problemas en datos complejos o al pasar dos parametros, vease insertar
                    // Os busco la manera más unifoerm
                    $alumnos = $cliente->listado($params);
                    $alumnos= json_decode($alumnos->return);
                    foreach($alumnos as $alumno){
                        echo "<li>".$alumno->id. ".- " . $alumno->nombre . ": ". $alumno->calificacion. "</li>";
                    }
                ?>
                </div>
            </div>
    </div>


<!-- Otros valores-->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Otra información WS-SOAP</div>
                <div class="panel-body">
                <?php
                        //$params = array("tipo"=>"aprobados");
                        $std = new stdClass;
                        $std->tipo ="aprobados";
                        $params = objectToArray($std);
                        $res = $cliente->resultado($params);
                        echo "<li>Aprobados: ". $res->return . "</li>";
                        //$params = array("tipo"=>"suspensos");
                        $std->tipo ="suspensos";
                        $params = objectToArray($std);
                        $res = $cliente->resultado($params);
                        echo "<li>Suspensos: ". $res->return . "</li>";
                        //$params = array("tipo"=>"media");
                        $std->tipo ="media";
                        $params = objectToArray($std);
                        $res = $cliente->resultado($params);
                        echo "<li>Media: ". round($res->return,2) . "</li>";
                ?>
                </div>
            </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Insertar alumno</div>
                <div class="panel-body">
                   <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                    <!-- Nombre-->
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" required name="nombre" class="form-control">
                    </div>
                    <!-- Calificación -->
                    <div class="form-group">
                        <label>Calificacion</label>
                        <input type="number" required name="calificacion" class="form-control" max='10' min='1' step="0.25">
                    </div>
                    <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-ok"></span>  Aceptar</button>
                    <button type="reset" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </form>
                    <hr>
                    <?php
                        if(isset($_POST['nombre']) && isset($_POST['calificacion'])){
                            $nombre = trim($_POST['nombre']);
                            $calificacion= trim($_POST['calificacion']);
                            // Recogemos los parámetros y llamamos al servicio
                            // La indisincrasia de PHP con SOAP en varios parámetros o datos complejos :)
                            $std = new stdClass;
                            $std->nombre = $nombre;
                            $std->calificacion = $calificacion;
                            $params = objectToArray($std);
                            $res = $cliente->insertar($params);
                            echo $res->return; // Lo sé, pero no se va a ver..
                            if($res->return>=1){
                                alerta("Registro insertado con éxito");
                            }
                            header("Refresh:0");
                        }
                    ?>
                </div>
            </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Consultar Alumno(s) SOAP</div>
                <div class="panel-body">
                   <form action="<?=$_SERVER['PHP_SELF'];?>" method="GET">
                    <!-- Nombre-->
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" required name="alumno" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-ok"></span>  Aceptar</button>
                    <button type="reset" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </form>
                    <hr>
                    <?php
                        if(isset($_GET['alumno']))
                        {
                            $alumno = trim($_GET['alumno']);
                            // consumimos el servicio SOAP
                            // Esto es porque he decidido que sea json para no tratar con datos complejos
                            //$params = array("nombre"=>$alumno);
                            $std = new stdClass;
                            $std->nombre = $alumno;
                            $params = objectToArray($std);
                            $alumnos = $cliente->listado($params);
                            $alumnos= json_decode($alumnos->return);
                            foreach($alumnos as $alumno){
                                echo "<li>".$alumno->id. ".- " . $alumno->nombre . ": ". $alumno->calificacion. "</li>";
                            }
                        }
                    ?>
                </div>
            </div>
    </div>
</div>
    </div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Información del tiempo</div>
                <div class="panel-body">
                <?php
                    // Usando la api de https://openweathermap.org/api
                    // Consumo otro servicio tipo REST
                    $apiKey = "3413976168791647268ca0fe9b56bea1";
                    $cityId = "2519240";
                    $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=es&units=metric&APPID=" . $apiKey;
                    $ch = curl_init();
                    
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_VERBOSE, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    
                    curl_close($ch);
                    $data = json_decode($response);
                    $currentTime = time();
                ?>
                <div class="report-container">
                    <h4>Información meterológica de <?php echo $data->name; ?></h4>
                        <div class="time">
                            <?php
                                date_default_timezone_set('Europe/Madrid');
                            ?>
                            <div><?php echo date("d-m-Y H:i:s", $currentTime); ?></div>
                            <div><?php echo ucwords($data->weather[0]->description); ?></div>
                        </div>
                        <div class="weather-forecast">
                        <img
                            src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png"
                            class="weather-icon" /> <?php echo $data->main->temp_max; ?>°C<span
                            class="min-temperature"><?php echo $data->main->temp_min; ?>°C</span>
                        </div>
                        <div class="time">
                            <div>Humedad: <?php echo $data->main->humidity; ?> %</div>
                            <div>Viento: <?php echo $data->wind->speed; ?> km/h</div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

</div>

<br><br>



<?php require_once "pie.php"; ?>