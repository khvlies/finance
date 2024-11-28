<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Kutipan</title>
    <link rel="stylesheet" href="css/mainview.css">
</head>
<body>
<?php include('navigation.php'); ?>
<main>
    <div class="container my-5">
        <h2>Kutipan Zakat</h2>
        <a class="btn btn-primary" href="overview.php" role="button">OVERVIEW</a>
        <br>
    <table class="table">
        <thead>
            <tr>
                <th>YEAR</th>
                <th>VIEW</th>
                <th>EDIT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('dbconn.php');

            $stmt = $dbconn->prepare("SELECT DISTINCT years FROM kutipan_bulanan ORDER BY years ASC");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $year = $row['years'];
                echo "<tr>
                    <td>{$year}</td>
                    <td>
                        <button class='btn btn-secondary' data-year='{$year}' data-type='bulanan'>Kutipan Bulanan</button>
                        <button class='btn btn-secondary' data-year='{$year}' data-type='jenis'>Jenis Kutipan</button>
                        <button class='btn btn-secondary' data-year='{$year}' data-type='sumber'>Kutipan Sumber</button>
                    </td>
                    <td>
                        <img src='images/edit.png' class='edit-image' alt='Edit' data-year='{$year}'>
                    </td>
                </tr>";
            }

            $stmt->close();
            ?>
        </tbody>
    </table>
    </div>
</main>
<div id="kutipanModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Data</h2>
        <div id="modal-body"></div>
    </div>
</div>
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-edit">&times;</span>
        <h2>Edit Data for <span id="edit-year"></span></h2>
        <form id="edit-form">
            <div id="edit-modal-body">
                <!-- Dynamic content from all tables will be loaded here -->
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editModal = document.getElementById("editModal");
    const editYearSpan = document.getElementById("edit-year");
    const editModalBody = document.getElementById("edit-modal-body");
    const closeEditModal = document.querySelector(".close-edit");

    document.querySelectorAll(".edit-image").forEach(button => {
        button.addEventListener("click", () => {
            const year = button.getAttribute("data-year");

            // Update modal title with the selected year
            editYearSpan.textContent = year;

            // Fetch data from the server for all tables
            fetch(`edit_all_data.php?year=${year}`)
                .then(response => response.text())
                .then(data => {
                    editModalBody.innerHTML = data; // Load fetched data into modal body
                    editModal.style.display = "block"; // Show the modal
                })
                .catch(error => console.error("Error fetching data:", error));
        });
    });

    closeEditModal.onclick = () => {
        editModal.style.display = "none";
    };

    window.onclick = event => {
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    };

    // Handle form submission
    document.getElementById("edit-form").addEventListener("submit", event => {
        event.preventDefault();

        const formData = new FormData(event.target);

        fetch("save_all_data.php", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Data updated successfully!");
                    editModal.style.display = "none";
                } else {
                    alert("Failed to update data!");
                }
            })
            .catch(error => console.error("Error saving data:", error));
    });
});

</script>
</body>
</html>
