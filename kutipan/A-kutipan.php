<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Kutipan</title>
    <link rel="stylesheet" href="../css/mainview.css">
</head>
<body>
<?php include('../A-navigation.php'); ?>
<main>
    <div class="container">
        <h2>Kutipan Zakat</h2>
        <div class ="top-container">
            <div>
                <a class="btn btn-primary" href="../kutipan/A-overview.php" role="button" title="Overview Data">OVERVIEW</a>
            </div>
            <div>
                <a href="../kutipan/add.php" title="Add Data">
                    <img src="../images/add.png" alt="Add Icon" style="width: 40px; height: auto;">
                </a>
            </div>
        </div>
        <!-- Notification Div -->
        <div id="notification" class="notification"></div>
        <script>
                // Display notification based on URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status');
                const type = urlParams.get('type');

                if (status) {
                    const notification = document.getElementById('notification');
                    if (status === 'success') {
                        notification.textContent = `Data for ${type.toUpperCase()} added successfully!`;
                        notification.classList.add('success');
                    } else if (status === 'error') {
                        notification.textContent = `Failed to add data for ${type.toUpperCase()}. Please try again.`;
                        notification.classList.add('error');
                    }

                    notification.style.display = 'block';

                    // Hide notification after 5 seconds
                    setTimeout(() => {
                        notification.style.display = 'none';
                        window.history.replaceState({}, document.title, window.location.pathname); // Remove query params
                    }, 5000);
                }
            </script>
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
                            <a href='../kutipan/edit.php?year={$year}'><img src='../images/edit.png' class='edit' alt='Edit Icon'></a>
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

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        alert("Data successfully updated!");
        window.location.href = "../kutipan/A-kutipan.php";
    </script>
<?php endif; ?>

</body>
</html>
