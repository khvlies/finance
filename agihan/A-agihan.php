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
<?php include('../A-navigation.php'); ?>
<main>
    <div class="container my-5">
        <h2>Agihan Zakat</h2>
        <div class= "top-container">
            <div>
                <a class="btn btn-primary" href="../agihan/G-overview.php" role="button" title="Overview Data">OVERVIEW</a>
            </div>
            <div>
                <a href="../agihan/G-add.php" title="Add Data">
                    <img src="../images/add.png" alt="Add Icon" style="width: 40px; height: auto;">
                </a>
            </div>
        </div>
        <!-- Notification Div -->
        <div id="notification" class="notification"></div>

        <!--<script>
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
            </script> -->
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
                            <a href='../agihan/G-edit.php?year={$year}'><img src='../images/edit.png' class='edit' alt='Edit Icon'></a>
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
    const notification = document.getElementById("notification");

    // Handle status notifications
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const type = urlParams.get('type');

    if (status) {
        notification.textContent = status === 'success'
            ? `Data for ${type.toUpperCase()} added successfully!`
            : `Failed to add data for ${type.toUpperCase()}. Please try again.`;

        notification.className = `notification ${status}`;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 5000);
    }

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

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        alert("Data successfully updated!");
        window.location.href = "../agihan/A-agihan.php";
    </script>
<?php endif; ?>

</body>
</html>
