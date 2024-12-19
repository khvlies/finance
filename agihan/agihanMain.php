<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Agihan</title>
    <link rel="stylesheet" href="../css/mainview.css">
</head>
<body>
<?php include('../navigation.php'); ?>
<main>
    <div class="container">
        <h2>Agihan Zakat</h2>
        <div class ="top-container">
            <a class="btn btn-primary" href="../agihan/overview.php" role="button" title="Overview Data">OVERVIEW</a>
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
                include('../dbconn.php');

                $stmt = $dbconn->prepare("SELECT DISTINCT years FROM agihan_category ORDER BY years ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $year = $row['years'];
                    echo "<tr>
                        <td>{$year}</td>
                        <td>
                            <button class='btn btn-secondary' data-year='{$year}' data-type='asnaf'>Agihan Asnaf</button>
                            <button class='btn btn-secondary' data-year='{$year}' data-type='agihan'>Agihan Zakat</button>
                        </td>
                        
                    </tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
<div id="agihanModal" class="modal" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-content">
        <span class="close" aria-label="Close modal">&times;</span>
        <h2 id="modalTitle"></h2>
        <div id="modal-body">Loading...</div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("agihanModal");
    const modalBody = document.getElementById("modal-body");
    const modalTitle = modal.querySelector("h2");
    const closeModal = modal.querySelector(".close");
    

    document.querySelectorAll(".btn-secondary").forEach(button => {
        button.addEventListener("click", event => {
        event.preventDefault();

        const year = button.getAttribute("data-year");
        const type = button.getAttribute("data-type");

        let url = type === "asnaf" ? "../agihan/AA.php" : "../agihan/AZ.php";

        modalTitle.textContent = type === "asnaf"
            ? `Agihan Asnaf (${year})`
            : `Agihan Zakat (${year})`;

        modalBody.textContent = "Loading...";
        modal.style.display = "block";

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
