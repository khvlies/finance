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
        <h2>Kutipan Jenis Zakat</h2>
        <a class="btn btn-primary" href="overview.php" role="button">OVERVIEW</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>YEAR</th>
                    <th>VIEW</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('dbconn.php'); // Include database connection

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
                        </td>
                    </tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
<div id="jenisKutipanModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Data</h2>
        <div id="modal-body"></div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("jenisKutipanModal");
    const modalBody = document.getElementById("modal-body");
    const modalTitle = modal.querySelector("h2");
    const closeModal = modal.querySelector(".close");

    document.querySelectorAll(".btn-secondary").forEach(button => {
        button.addEventListener("click", event => {
            event.preventDefault();

            const year = button.getAttribute("data-year");
            const type = button.getAttribute("data-type");
            const url = type === "bulanan" ? "KB.php" : "KJ.php";

            // Set the modal title to display the year
            modalTitle.textContent = type === "bulanan" ? `${year}` : `${year}`;

            fetch(`${url}?year=${year}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                    modal.style.display = "block";
                })
                .catch(error => console.error("Error fetching data:", error));
        });
    });

    closeModal.onclick = () => {
        modal.style.display = "none";
    };

    window.onclick = event => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});
</script>
</body>
</html>
