<?php
if (isset($_GET['document_id'])) {
    $document_id = $_GET['document_id'];

    // Fetch document details
    $query = "SELECT * FROM documents WHERE document_id = '$document_id'";
    $result = mysqli_query($conn, $query);
    $document = mysqli_fetch_assoc($result);

    if ($document) {
        // Print or PDF format
        echo "<h1>Print Document</h1>";
        echo "<div>" . $document['document_content'] . "</div>";
        echo '<script>window.print();</script>'; // Automatically trigger print dialog
    } else {
        echo "Document not found.";
    }
}
?>
