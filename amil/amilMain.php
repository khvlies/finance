<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Amil</title>
    <link rel="stylesheet" href="../css/mainview.css">
</head>
<body>
<?php include('../navigation.php'); ?>
<main>
    <div class="container my-5">
        <h2>Kewangan Amil</h2>
        <div class="top-container">
            <div>
                <a class="btn btn-primary" href="../amil/overview.php" role="button" title="Overview Data">OVERVIEW</a>
            </div>
        </div>
        
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

                $stmt = $dbconn->prepare("SELECT DISTINCT years FROM amil_income ORDER BY years ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $year = $row['years'];
                    echo "<tr>
                        <td>{$year}</td>
                        <td>
                            <button class='btn btn-secondary' data-year='{$year}' data-type='pendapatan'>Pendapatan</button>
                            <button class='btn btn-secondary' data-year='{$year}' data-type='perbelanjaan'>Perbelanjaan</button>
                        </td>
                        
                    </tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
<div id="amilModal" class="modal" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-content">
        <span class="close" aria-label="Close modal">&times;</span>
        <h2 id="modalTitle"></h2>
        <div id="modal-body">Loading...</div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("amilModal");
    const modalBody = document.getElementById("modal-body");
    const modalTitle = modal.querySelector("h2");
    const closeModal = modal.querySelector(".close");

    document.querySelectorAll(".btn-secondary").forEach(button => {
        button.addEventListener("click", event => {
        event.preventDefault();

        const year = button.getAttribute("data-year");
        const type = button.getAttribute("data-type");

        let url = type === "pendapatan" ? "../amil/PD.php" : "../amil/PB.php";

        modalTitle.textContent = type === "pendapatan"
            ? `Pendapatan Amil (${year})`
            : `Perbelanjaan Amil (${year})`;

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
