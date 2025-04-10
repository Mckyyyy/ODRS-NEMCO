<?php
if (isset($_GET['document_id'])) {
    $document_id = $_GET['document_id'];

    // Fetch document details
    $query = "SELECT * FROM documents WHERE document_id = '$document_id'";
    $result = mysqli_query($conn, $query);
    $document = mysqli_fetch_assoc($result);

    if ($document) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update document content
            $updated_content = $_POST['document_content'];
            $update_query = "UPDATE documents SET document_content = '$updated_content' WHERE document_id = '$document_id'";
            mysqli_query($conn, $update_query);

            echo "Document updated successfully.";
        }

        // Display editable document content
        echo '<form method="POST">';
        echo '<textarea name="document_content" rows="10" class="form-control">' . $document['document_content'] . '</textarea>';
        echo '<br><button type="submit" class="btn btn-success">Save Changes</button>';
        echo '</form>';
    } else {
        echo "Document not found.";
    }
}
?>
