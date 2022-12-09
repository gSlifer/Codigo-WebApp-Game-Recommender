<!DOCTYPE html>
<html>
    <head>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <title>Steam Game Recommender</title>
        <link rel="icon" href=https://imgur.com/VzCPhCb.jpg>
        <link rel="stylesheet" href="stylephp.css">
    </head>
    <style>
        td {
  		border:1px solid black;
		background-color: #4f6082;
	}

	table, th {
		background-color: #182132;
		border: 2px solid black;
	}
    body {
	    background-color: #242d3e;
        }
    a {
        background-color:white; 
        border-width:3px; 
        border-style:solid; 
        border-color:#1c1c1c;
        padding: 6px;
        border-radius: 6px;
    }
    tr{
        color: white;
    }
    td:nth-child(odd) {
        background-color: #3a4b6b;
    }
    td:hover {
        background-color: #5d739e;
    }
    </style>
<body>
    <a href="https://grupo22.cc3201.dcc.uchile.cl/GameRecommender.html">  Go Back </a>
    <p> </p>
    <?php
        echo "<table>";
        echo "<tr>
                <th> ID </th>
                <th> Nombre </th>
                <th> Precio </th>
                <th> Edad Requerida </th>
                <th> Developer </th>
                <th> Publisher </th>
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

            $name=$_GET['name'];
            $appid=$_GET['appid'];

            if ($appid==''){
                $idquery = "SELECT appid FROM Juego WHERE name = :val1 LIMIT 1";
                $idq = $pdo->prepare($idquery);
                $idq->execute(['val1'=>$name]);
                $appidq= $idq->setFetchMode(PDO::FETCH_ASSOC);
		foreach ($idq->fetchAll() as $k=>$v){
                    $appid = $v['appid'];
                }
            }

            $tquery = "SELECT tag FROM Representa WHERE appid=:val1 LIMIT 3";
            $tagt= $pdo->prepare($tquery);
            $tagt->execute(['val1'=>$appid]);
            $tagsresult= $tagt->setFetchMode(PDO::FETCH_ASSOC);

            $gametags1=[];
            foreach ($tagt->fetchAll() as $k=>$v){
                $gametags1[] = $v['tag'];
            }

            $tagsids = "SELECT DISTINCT appid FROM Representa WHERE tag LIKE '$gametags1[0]' AND appid IN(SELECT DISTINCT appid FROM Representa WHERE tag LIKE '$gametags1[1]' AND appid IN(SELECT DISTINCT appid FROM Representa WHERE tag LIKE '$gametags1[2]' ))";
            $pubids = "SELECT DISTINCT appid FROM Publica WHERE pub IN (SELECT pub FROM Publica WHERE appid= :appid)";
            $devids = "SELECT DISTINCT appid FROM Desarrolla WHERE dev IN (SELECT dev FROM Desarrolla WHERE appid= :appid)";
            $query = "SELECT J.appid, J.name, J.price, J.required_age, D.dev, P.pub FROM Juego J, Desarrolla D, Publica P WHERE (J.appid=D.appid AND J.appid=P.appid) AND (J.appid IN ($tagsids) OR J.appid IN ($pubids) OR J.appid IN ($devids))";

            $stmt = $pdo->prepare($query);

            $stmt->execute(['appid' => $appid]);


            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

            # Y se imprimen.
            foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
                echo $v;
            }
        }
        # Esta sección imprime un error en caso de haber uno.
        catch(PDOException $e){
            echo $e->getMessage();
        }
    ?>
</body>
</html>
