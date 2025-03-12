<?php
$dblink = mysqli_connect("dbserver", "grupo01", "nooRohDe6v", "db_grupo01");
if (!$dblink) {
    die("La conexión falló: " . mysqli_connect_error());
} else {
    echo "Conexión establecida correctamente.<br>";
}

function getRandomUsers($results = 100) {
    $url = "https://randomuser.me/api/?results=$results";
    $json = file_get_contents($url);
    return json_decode($json, true);
}

function insertarUbicacion($dblink, $loc) {
    $streetNum  = (int)$loc['street']['number'];
    $streetName = mysqli_real_escape_string($dblink, $loc['street']['name']);
    $city       = mysqli_real_escape_string($dblink, $loc['city']);
    $state      = mysqli_real_escape_string($dblink, $loc['state']);
    $country    = mysqli_real_escape_string($dblink, $loc['country']);
    $postcode   = mysqli_real_escape_string($dblink, $loc['postcode']);
    $lat        = mysqli_real_escape_string($dblink, $loc['coordinates']['latitude']);
    $lng        = mysqli_real_escape_string($dblink, $loc['coordinates']['longitude']);
    
    $sql = "INSERT INTO Ubicaciones 
            (street_num, street_name, city, state, country, postcode, lat, lng)
            VALUES ($streetNum, '$streetName', '$city', '$state', '$country', '$postcode', '$lat', '$lng')";
    
    if (!mysqli_query($dblink, $sql)) {
        echo "Error Ubicaciones: " . mysqli_error($dblink) . "<br>";
        return 0;
    } else {
        $id = mysqli_insert_id($dblink);
        echo "OK Ubicacion: $id ($streetName, $city)<br>";
        return $id;
    }
}

function insertarLogin($dblink, $login) {
    $username = mysqli_real_escape_string($dblink, $login['username']);
    $password = mysqli_real_escape_string($dblink, $login['password']);
    $salt     = mysqli_real_escape_string($dblink, $login['salt']);
    $md5      = mysqli_real_escape_string($dblink, $login['md5']);
    $sha1     = mysqli_real_escape_string($dblink, $login['sha1']);
    $sha256   = mysqli_real_escape_string($dblink, $login['sha256']);

    $sql = "INSERT INTO Logins (username, password, salt, md5, sha1, sha256)
            VALUES ('$username', '$password', '$salt', '$md5', '$sha1', '$sha256')";
    
    if (!mysqli_query($dblink, $sql)) {
        echo "Error Logins: " . mysqli_error($dblink) . "<br>";
        return 0;
    } else {
        $id = mysqli_insert_id($dblink);
        echo "OK Login: $id ($username)<br>";
        return $id;
    }
}

function insertarFechas($dblink, $dob, $registered) {

    $dobOriginal = $dob['date'];
    $regOriginal = $registered['date']; 

    $dobParsed = strtotime($dobOriginal); 
    $regParsed = strtotime($regOriginal);

    $dobDate = date('Y-m-d H:i:s', $dobParsed);
    $regDate = date('Y-m-d H:i:s', $regParsed);

    $dobAge = (int)$dob['age'];
    $regAge = (int)$registered['age'];

    $sql = "INSERT INTO Fechas (dob_date, dob_age, reg_date, reg_age)
            VALUES ('$dobDate', $dobAge, '$regDate', $regAge)";
    
    if (!mysqli_query($dblink, $sql)) {
        echo "Error Fechas: " . mysqli_error($dblink) . "<br>";
        return 0;
    } else {
        $id = mysqli_insert_id($dblink);
        echo "OK Fechas: $id (DOB: $dobDate, REG: $regDate)<br>";
        return $id;
    }
}

function insertarFoto($dblink, $picture) {
    $large   = mysqli_real_escape_string($dblink, $picture['large']);
    $medium  = mysqli_real_escape_string($dblink, $picture['medium']);
    $thumb   = mysqli_real_escape_string($dblink, $picture['thumbnail']);

    $sql = "INSERT INTO Fotos (large, medium, thumbnail)
            VALUES ('$large', '$medium', '$thumb')";

    if (!mysqli_query($dblink, $sql)) {
        echo "Error Fotos: " . mysqli_error($dblink) . "<br>";
        return 0;
    } else {
        $id = mysqli_insert_id($dblink);
        echo "OK Foto: $id<br>";
        return $id;
    }
}

function insertarUsuario($dblink, $user, $idUbic, $idLog, $idFec, $idFot) {
    $title = mysqli_real_escape_string($dblink, $user['name']['title']);
    $first = mysqli_real_escape_string($dblink, $user['name']['first']);
    $last  = mysqli_real_escape_string($dblink, $user['name']['last']);
    $email = mysqli_real_escape_string($dblink, $user['email']);
    $gender= mysqli_real_escape_string($dblink, $user['gender']);
    $phone = mysqli_real_escape_string($dblink, $user['phone']);
    $cell  = mysqli_real_escape_string($dblink, $user['cell']);
    $nat   = mysqli_real_escape_string($dblink, $user['nat']);

    // Importante: Comprueba que $idUbic, $idLog, $idFec y $idFot no sean 0
    // Si son 0, es que alguna inserción anterior falló
    $sql = "INSERT INTO Usuarios
       (nombre_titulo, nombre_first, nombre_last, email, gender, phone, cell, nat,
        id_ubicacion, id_login, id_fecha, id_foto)
    VALUES ('$title', '$first', '$last', '$email', '$gender', '$phone', '$cell', '$nat',
            $idUbic, $idLog, $idFec, $idFot)";

    if (!mysqli_query($dblink, $sql)) {
        echo "Error Usuarios: " . mysqli_error($dblink) . "<br>";
    } else {
        $id = mysqli_insert_id($dblink);
        echo "OK Usuario: $id ($email)<br>";
    }
}

// 4. Proceso principal
$randomUsers = getRandomUsers(100);  
if (isset($randomUsers['results'])) {
    echo "Cantidad de usuarios obtenidos: " . count($randomUsers['results']) . "<br><br>";
    foreach ($randomUsers['results'] as $user) {
        $idUbic = insertarUbicacion($dblink, $user['location']);
        $idLog  = insertarLogin($dblink, $user['login']);
        $idFec  = insertarFechas($dblink, $user['dob'], $user['registered']);
        $idFot  = insertarFoto($dblink, $user['picture']);
        insertarUsuario($dblink, $user, $idUbic, $idLog, $idFec, $idFot);

        echo "<hr>";
    }
} else {
    echo "Error: No se pudo obtener 'results' de la API.<br>";
}

echo "Finalizada la inserción de usuarios aleatorios desde RandomUser.";
mysqli_close($dblink);
?>
