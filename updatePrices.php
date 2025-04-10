<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new prices from the POST request
    $newPrices = $_POST['prices'] ?? [];

    // File paths
    $pricesFileJS = 'index_request.php'; // JavaScript file
    $pricesFilePHP = 'process_form.php'; // PHP file

    // Helper function to parse existing prices from a file
    function parsePrices($content, $type) {
        $prices = [];
        if ($type === 'js') {
            preg_match('/const prices = {(.*?)};/s', $content, $matches);
            if (isset($matches[1])) {
                $lines = explode("\n", trim($matches[1]));
                foreach ($lines as $line) {
                    if (preg_match('/(\w+): (\d+),?/', trim($line), $price)) {
                        $prices[$price[1]] = (int)$price[2];
                    }
                }
            }
        } elseif ($type === 'php') {
            preg_match('/\$prices = array\((.*?)\);/s', $content, $matches);
            if (isset($matches[1])) {
                $lines = explode("\n", trim($matches[1]));
                foreach ($lines as $line) {
                    if (preg_match('/\'(\w+)\' => (\d+),?/', trim($line), $price)) {
                        $prices[$price[1]] = (int)$price[2];
                    }
                }
            }
        }
        return $prices;
    }

    // Read existing content from both files
    $fileContentJS = file_get_contents($pricesFileJS);
    $fileContentPHP = file_get_contents($pricesFilePHP);

    // Parse existing prices
    $existingPricesJS = parsePrices($fileContentJS, 'js');
    $existingPricesPHP = parsePrices($fileContentPHP, 'php');

    // Update prices: keep existing prices for keys not in $newPrices
    $updatedPricesJS = $existingPricesJS;
    $updatedPricesPHP = $existingPricesPHP;

    foreach ($newPrices as $key => $value) {
        $value = (int)$value; // Ensure numeric value
        if ($value > 0) { // Update only if a valid value is provided
            $updatedPricesJS[$key] = $value;
            $updatedPricesPHP[$key] = $value;
        }
    }

    // Generate updated JS prices content
    $newJSPricesContent = "const prices = {\n";
    foreach ($updatedPricesJS as $key => $value) {
        $newJSPricesContent .= "    $key: $value,\n";
    }
    $newJSPricesContent .= "};";
    $fileContentJS = preg_replace('/const prices = {.*?};/s', $newJSPricesContent, $fileContentJS);

    // Generate updated PHP prices content
    $newPHPPricesContent = "\$prices = array(\n";
    foreach ($updatedPricesPHP as $key => $value) {
        $newPHPPricesContent .= "    '$key' => $value,\n";
    }
    $newPHPPricesContent .= ");";
    $fileContentPHP = preg_replace('/\$prices = array\(.*?\);/s', $newPHPPricesContent, $fileContentPHP);

    // Write updates back to the files
    if (file_put_contents($pricesFileJS, $fileContentJS) === false) {
        echo 'Error: Unable to update the JS file.';
        exit;
    }
    if (file_put_contents($pricesFilePHP, $fileContentPHP) === false) {
        echo 'Error: Unable to update the PHP file.';
        exit;
    }

  // Database connection details
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "records_db1";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check for connection errors
  if ($conn->connect_error) {
      echo 'Error: Unable to connect to the database.';
      exit;
  }

  // Loop through each document price received
  foreach ($newPrices as $key => $value) {
      $value = (int)$value; // Ensure value is numeric

      if ($value > 0) { // Update only if the value is valid (greater than 0)
          $key = $conn->real_escape_string($key);

          // Update the specific document's price in the database
          $sql = "UPDATE document_prices SET price = '$value' WHERE document_type = '$key'";
          if (!$conn->query($sql)) {
              echo 'Error: Unable to update the price for ' . $key . '.';
              $conn->close();
              exit;
          }
      }
  }

  $conn->close();

  echo 'Prices updated successfully for the selected documents.';
}
?>
