<!DOCTYPE html>
<html>
    <head>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <title>Steam Game Recommender</title>
        <link rel="icon" href=https://imgur.com/VzCPhCb.jpg> 
        <link rel="stylesheet" href="stylephp.css">
    </head>
    
<body>
    <a href="https://grupo22.cc3201.dcc.uchile.cl/GameRecommender.html">  Go Back </a>
    <p> </p>
    <?php

        echo "<table>";
        echo "<tr>
                <th> ID </th>
                <th> Name </th>
                <th> Price </th>
                <th> Required Age </th>
		        <th> Achievements </th>
		        <th> Description </th>
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

		$nombre=$_GET['name'];
		$nombre.='%';
		$appid=$_GET['appid'];

		if ($appid==''){
			$stmt = $pdo->prepare('SELECT J.appid, J.name, J.price, J.required_age, J.achievements, D.short_description
						FROM Juego J, Description_data D
						WHERE J.name LIKE :val1 AND D.appid=J.appid');
			$stmt->execute(['val1' => $nombre]);
		}
		else{
			$stmt = $pdo->prepare('SELECT J.appid, J.name, J.price, J.required_age, J.achievements, D.short_description
						FROM Juego J, Description_data D
						WHERE J.name LIKE :val1 AND J.appid=:val2 AND J.appid=D.appid');
			$stmt->execute(['val1' => $nombre, 'val2' => $appid]);
		}

            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

            foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
                echo $v;
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    ?>
</body>
</html>
