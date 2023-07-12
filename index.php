<!DOCTYPE html>
<html>
<head>
  <title>Einkaufsliste</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    @media print {
      .no-print,
      #formular,
      .action-column {
        display: none;
      }
    }
    .table th,
    .table td {
      vertical-align: middle;
    }
    .action-column {
      white-space: nowrap;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="mt-4">Einkaufsliste</h1>
    <?php
    // Verbindung zur Datenbank herstellen
    $servername = "localhost";
    $username = "root";
    $password = "hatter233A!";
    $dbname = "einkaufsliste";
    
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo "Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage();
    }

    // Formulardaten verarbeiten und in die Datenbank einfügen oder aktualisieren
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $name = $_POST["name"];
      $quantity = $_POST["quantity"];
      $offer = isset($_POST["offer"]) ? 1 : 0;
      $price = $_POST["price"];
      $pfand = $_POST["pfand"];

      if(isset($_POST["id"])) {
        $id = $_POST["id"];
        $sql = "UPDATE einkaufsliste SET name = :name, quantity = :quantity, offer = :offer, price = :price, pfand = :pfand WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
      } else {
        $sql = "INSERT INTO einkaufsliste (name, quantity, offer, price, pfand) VALUES (:name, :quantity, :offer, :price, :pfand)";
        $stmt = $conn->prepare($sql);
      }

      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':quantity', $quantity);
      $stmt->bindParam(':offer', $offer);
      $stmt->bindParam(':price', $price);
      $stmt->bindParam(':pfand', $pfand);
      $stmt->execute();

      header("Location: index.php");
      exit();
    }

    // Eintrag aus der Datenbank löschen
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
      $id = $_GET["id"];

      $sql = "DELETE FROM einkaufsliste WHERE id = :id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      header("Location: index.php");
      exit();
    }

    // Daten aus der Datenbank abrufen und anzeigen
    $sql = "SELECT * FROM einkaufsliste";
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div id="formular">
      <form action="index.php" method="POST">
        <?php if(isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["id"])) {
          $id = $_GET["id"];
          $sql = "SELECT * FROM einkaufsliste WHERE id = :id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $name = $row['name'];
          $quantity = $row['quantity'];
          $offer = $row['offer'];
          $price = $row['price'];
          $pfand = $row['pfand'];
          ?>
          <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php } ?>

        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="quantity">Stückzahl:</label>
          <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo isset($quantity) ? $quantity : ''; ?>" required>
        </div>
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="offer" name="offer" <?php echo isset($offer) && $offer ? 'checked' : ''; ?>>
          <label class="form-check-label" for="offer">Im Angebot</label>
        </div>
        <div class="form-group">
          <label for="price">Preis:</label>
          <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo isset($price) ? $price : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="pfand">Pfand:</label>
          <select class="form-control" id="pfand" name="pfand" required>
            <option value="0">Nein</option>
            <option value="0.08" <?php echo isset($pfand) && $pfand == 0.08 ? 'selected' : ''; ?>>0,08€</option>
            <option value="0.15" <?php echo isset($pfand) && $pfand == 0.15 ? 'selected' : ''; ?>>0,15€</option>
            <option value="0.25" <?php echo isset($pfand) && $pfand == 0.25 ? 'selected' : ''; ?>>0,25€</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo isset($id) ? 'Aktualisieren' : 'Hinzufügen'; ?></button>
      </form>
    </div>

    <h2 class="mt-4">Meine Einkaufsliste</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Name</th>
          <th>Stückzahl</th>
          <th>Im Angebot</th>
          <th>Preis</th>
          <th>Pfand</th>
          <th>Gesamtpreis</th>
          <th class="action-column">Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($rows as $row) {
          $id = $row['id'];
          $name = $row['name'];
          $quantity = $row['quantity'];
          $offer = $row['offer'];
          $price = $row['price'];
          $pfand = $row['pfand'];
          $total = ($quantity * $price) + ($quantity * $pfand);

          echo "<tr>";
          echo "<td>$name</td>";
          echo "<td>$quantity</td>";
          echo "<td>" . ($offer ? 'Ja' : 'Nein') . "</td>";
          echo "<td>€" . number_format($price, 2) . "</td>";
          echo "<td>€" . number_format($pfand, 2) . "</td>";
          echo "<td>€" . number_format($total, 2) . "</td>";
          echo "<td class='action-column'><a href='index.php?action=edit&id=$id'>Bearbeiten</a> | <a href='index.php?action=delete&id=$id'>Löschen</a></td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>

    <?php
    // Gesamtpreis berechnen und anzeigen
    $sql = "SELECT SUM(quantity * price) + SUM(quantity * pfand) AS total FROM einkaufsliste";
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];

    echo "<h3 class='mt-4'>Gesamtpreis: €" . number_format($total, 2) . "</h3>";
    ?>

    <button class="btn btn-secondary no-print mt-4" onclick="window.print();">Drucken</button>
  </div>
</body>
</html>
