<!DOCTYPE html>
<html>
<head>
  <title>Monatliche Ausgaben</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Monatliche Ausgaben</h1>
    <?php
    // Verbindung zur Datenbank herstellen
    $servername = "localhost";
    $username = "root";
    $password = "hatter233A!";
    $dbname = "ausgaben";
    
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo "Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage();
    }

    // Formulardaten verarbeiten und in die Datenbank einfügen oder aktualisieren
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $kategorie = $_POST["kategorie"];
      $betrag = $_POST["betrag"];

      if(isset($_POST["id"])) {
        $id = $_POST["id"];
        $sql = "UPDATE ausgaben SET kategorie = :kategorie, betrag = :betrag WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
      } else {
        $sql = "INSERT INTO ausgaben (kategorie, betrag) VALUES (:kategorie, :betrag)";
        $stmt = $conn->prepare($sql);
      }

      $stmt->bindParam(':kategorie', $kategorie);
      $stmt->bindParam(':betrag', $betrag);
      $stmt->execute();

      header("Location: ausgaben.php");
      exit();
    }

    // Eintrag aus der Datenbank löschen
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
      $id = $_GET["id"];

      $sql = "DELETE FROM ausgaben WHERE id = :id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      header("Location: ausgaben.php");
      exit();
    }

    // Daten aus der Datenbank abrufen und anzeigen
    $sql = "SELECT * FROM ausgaben";
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div id="formular">
      <form action="ausgaben.php" method="POST">
        <?php if(isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["id"])) {
          $id = $_GET["id"];
          $sql = "SELECT * FROM ausgaben WHERE id = :id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $kategorie = $row['kategorie'];
          $betrag = $row['betrag'];
          ?>
          <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php } ?>

        <div class="form-group">
          <label for="kategorie">Kategorie:</label>
          <input type="text" class="form-control" id="kategorie" name="kategorie" value="<?php echo isset($kategorie) ? $kategorie : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="betrag">Betrag:</label>
          <input type="number" class="form-control" id="betrag" name="betrag" step="0.01" value="<?php echo isset($betrag) ? $betrag : ''; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo isset($id) ? 'Aktualisieren' : 'Hinzufügen'; ?></button>
      </form>
    </div>

    <h2 class="mt-4">Monatliche Ausgaben</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Kategorie</th>
          <th>Betrag</th>
          <th class="no-print">Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($rows as $row) {
          $id = $row['id'];
          $kategorie = $row['kategorie'];
          $betrag = $row['betrag'];

          echo "<tr>";
          echo "<td>$kategorie</td>";
          echo "<td>€" . number_format($betrag, 2) . "</td>";
          echo "<td class='no-print'><a href='ausgaben.php?action=edit&id=$id'>Bearbeiten</a> | <a href='ausgaben.php?action=delete&id=$id'>Löschen</a></td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>

    <?php
    // Gesamtbetrag berechnen und anzeigen
    $sql = "SELECT SUM(betrag) AS total FROM ausgaben";
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];

    echo "<h3 class='mt-4'>Gesamtbetrag: €" . number_format($total, 2) . "</h3>";
    ?>

    <button class="btn btn-secondary no-print mt-4" onclick="window.print();">Drucken</button>
  </div>
</body>
</html>
