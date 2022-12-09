<!DOCTYPE html>
<html>
    <head>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <title>Steam Game Recommender</title>
        <link rel="icon" href=https://imgur.com/VzCPhCb.jpg> 
        <link rel="stylesheet" href="stylephp.css">
    </head>
    
<body>
    <a href="https://grupo22.cc3201.dcc.uchile.cl/GameRecommender.html"> Go Back </a>
    <p> </p>
    <?php

        echo "<table>";
        echo "<tr>
                <th> ID </th>
                <th> Name </th>
                <th> Price </th>
                <th> Release date </th>
                <th> Required age </th>
                <th> Achievements </th>
              </tr>";

        class TableRows extends RecursiveIteratorIterator {
            function __construct($it) {
                parent::__construct($it, self::LEAVES_ONLY);
            }
            function current() {
                return "<td>" . parent::current(). "</td>";
            }
            function beginChildren() {
                echo "<tr>";
            }
            function endChildren() {
                echo "</tr>" . "\n";
            }
        }

        try {

            $pdo = new PDO('pgsql:
                            host=localhost;
                            port=5432;
                            dbname=cc3201;
                            user=webuser;
                            password=riesca');

            #JUEGO
            $edad=$_GET['edad'];
            if ($edad == ''){
                $edad = '0';
            }
            $precio = $_GET['precio'];
            if ($precio == ''){
                $precio = '999999999999999';
            }

	    $tag1 = $_GET['tag1'];
	    $tag2 = $_GET['tag2'];
            $tag3 = $_GET['tag3'];
            $tag4 = $_GET['tag4'];
            $tag5 = $_GET['tag5'];

	    $cat1 = $_GET['categoria1'];
	    $cat2 = $_GET['categoria2'];
	    $cat3 = $_GET['categoria3'];

	    $len1 = $_GET['idioma1'];
	    $len2 = $_GET['idioma2'];
	    $len3 = $_GET['idioma3'];

	    $os1 = '%';
	    $os2 = '%';
	    if(isset($_GET['os1'])) {
	        $os1 = 'Windows';
	    }

	    if(isset($_GET['os2'])){
		$os2 = 'Mac';
	    }



            if ($tag1 == '%' AND $tag2 == '%' AND $tag3 == '%' AND $tag4 == '%' AND $tag5 == '%'){
                $tagsids = "SELECT DISTINCT appid FROM juego";
            }
            else{
                $tquery = "SELECT DISTINCT appid FROM Representa WHERE tag LIKE :val1 AND appid IN (SELECT DISTINCT appid FROM Representa WHERE tag LIKE :val2 AND appid IN (SELECT DISTINCT appid FROM Representa WHERE tag LIKE :val3 AND appid IN (SELECT DISTINCT appid FROM Representa WHERE tag LIKE :val4 AND appid IN (SELECT DISTINCT appid FROM Representa WHERE tag LIKE :val5))))";
	            $tagt = $pdo->prepare($tquery);
	            $tagt->execute(['val1'=>$tag1, 'val2'=>$tag2, 'val3'=>$tag3, 'val4'=>$tag4, 'val5'=>$tag5]);
                $tagsresult = $tagt->setFetchMode(PDO::FETCH_ASSOC);

                $tagstids = [];
                foreach ($tagt->fetchAll() as $k=>$v){
		            $tagstids[] = $v['appid'];
                }
                $tagsids = implode(",", $tagstids);
            }




            if ($cat1 == '%' AND $cat2 == '%' AND $cat3 == '%'){
                $catids = "SELECT DISTINCT appid FROM juego";
            }
            else{
                $cquery = "SELECT DISTINCT appid FROM Categoriza WHERE cat LIKE :val1 AND appid IN (SELECT DISTINCT appid FROM Categoriza WHERE cat LIKE :val2 AND appid IN (SELECT DISTINCT appid FROM Categoriza WHERE cat LIKE :val3))";
                $catt = $pdo->prepare($cquery);
                $catt->execute(['val1'=>$cat1, 'val2'=>$cat2, 'val3'=>$cat3]);
                $catresult = $catt->setFetchMode(PDO::FETCH_ASSOC);

                $cattids = [];
                foreach ($catt->fetchAll() as $k=>$v){
                    $cattids[] = $v['appid'];
                }
                $catids = implode(",", $cattids);
            }




            if ($len1 == '%' AND $len2 == '%' AND $len3 == '%'){
                $lenids = "SELECT DISTINCT appid FROM juego";
            }
            else{
                $lquery = "SELECT DISTINCT appid FROM Soporta WHERE len LIKE :val1 AND appid IN (SELECT DISTINCT appid FROM Soporta WHERE len LIKE :val2 AND appid IN (SELECT DISTINCT appid FROM Soporta WHERE len LIKE :val3))";
                $lent=$pdo->prepare($lquery);
                $lent->execute(['val1'=>$len1, 'val2'=>$len2, 'val3'=>$len3]);
                $lenresult = $lent->setFetchMode(PDO::FETCH_ASSOC);

                $lentids = [];
                foreach ($lent->fetchAll() as $k=>$v){
                    $lentids[] = $v['appid'];
                }
                $lenids = implode(",", $lentids);
            }


	   if ($os1 == '%' AND $os2 == '%'){
		$osids = "SELECT DISTINCT appid FROM juego";
	   }
	   else{

		$oquery = "(SELECT DISTINCT appid FROM Limita WHERE OS LIKE :val1 AND appid IN (SELECT DISTINCT appid FROM Limita WHERE OS LIKE :val2))";
		$ost=$pdo->prepare($oquery);
		$ost->execute(['val1'=>$os1, 'val2'=>$os2]);
		$ostresult = $ost->setFetchMode(PDO::FETCH_ASSOC);

                $ostids = [];
                foreach ($ost->fetchAll() as $k=>$v){
                    $ostids[] = $v['appid'];
                }
                $osids = implode(",", $ostids);

	   }



	    $query = "SELECT * FROM juego WHERE required_age >= $edad AND price <= $precio AND appid IN ($tagsids) AND appid IN ($catids) AND appid IN ($lenids) AND appid IN ($osids)";
            $stmt = $pdo->query($query);

            # Luego se obtienen los resultados.
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

            # Y se imprimen.
            foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
                echo $v;
            }
        }
        # Esta seccion imprime un error en caso de haber uno.
        catch(PDOException $e){
            echo $e->getMessage();
        }
    ?>
</body>
</html>
