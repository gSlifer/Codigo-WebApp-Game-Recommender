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
                <th> Categories </th>
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


		$nombre='%';

			$stmt = $pdo->prepare('SELECT DISTINCT cat
						FROM Categoriza 
						WHERE cat LIKE :val1 ORDER BY cat');
			$stmt->execute(['val1' => $nombre]);
		
		
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
