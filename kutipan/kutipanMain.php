<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Kutipan</title>
    <link rel="stylesheet" href="../css/mainview.css">
</head>
<body>
<?php include('../navigation.php'); ?>
<main>
    <div class="container">
        <h2>Kutipan Zakat</h2>
        <div class ="top-container">
            <a class="btn btn-primary" href="../kutipan/overview.php" role="button" title="Overview Data">OVERVIEW</a>
        </div>

        <div id="notification" class="notification"></div>
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
                include('../dbconn.php'); // Include database connection

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

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("kutipanModal");
    const modalBody = document.getElementById("modal-body");
    const modalTitle = modal.querySelector("h2");
    const closeModal = modal.querySelector(".close");

    document.querySelectorAll(".btn-secondary").forEach(button => {
    button.addEventListener("click", event => {
        event.preventDefault();

        const year = button.getAttribute("data-year");
        const type = button.getAttribute("data-type");

        let url;
        if (type === "bulanan") {
            url = "../kutipan/KB.php";
        } else if (type === "jenis") {
            url = "../kutipan/KJ.php";
        } else if (type === "sumber") { // New condition for Kutipan Sumber
            url = "../kutipan/KS.php";
        }

        // Update modal title
        modalTitle.textContent = type === "bulanan" 
            ? `Kutipan Bulanan (${year})`
            : type === "jenis" 
                ? `Jenis Kutipan (${year})`
                : `Kutipan Sumber (${year})`;

        // Fetch and display the data
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
