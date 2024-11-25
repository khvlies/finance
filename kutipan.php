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
        <a class="btn btn-primary" href="#.php" role="button">OVERVIEW</a>
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
                    // Database credentials
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "finstatdb2";

                    // Create connection
                    $connection = new mysqli($servername, $username, $password, $database);

                    // Check connection
                    if ($connection->connect_error) {
                        die("Connection failed: " . $connection->connect_error);
                    }

                    // SQL Query with Prepared Statement
                    $stmt = $connection->prepare("SELECT DISTINCT years FROM kutipan_bulanan ORDER BY years ASC");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while($row = $result->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['years']}</td>
                        <td>
                            <a class='btn btn-secondary' href='#.php'>Kutipan Bulanan</a>
                            <a class='btn btn-secondary' href='#.php'>Jenis Kutipan</a>
                        </td>
                    </tr>";
                    }

                    // Close connection
                    $stmt->close();
                    $connection->close();
                    ?>
                </tbody>
        </table>
    </div>
    </main>
        <div id="jenisKutipanModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Jenis Kutipan</h2>
            <p>Details about Jenis Kutipan...</p>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("jenisKutipanModal");
            const btns = document.querySelectorAll(".btn-secondary");
            const span = modal.querySelector(".close");

            btns.forEach((btn) => {
                btn.addEventListener("click", (event) => {
                    event.preventDefault();
                    modal.style.display = "block";
                });
            });

            span.onclick = () => {
                modal.style.display = "none";
            };

            window.onclick = (event) => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            };
        });
        </script>

</body>
</html>

