<?php
include('../dbconn.php');

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    // Fetch data for the selected year

    $stmt_income = $dbconn->prepare("SELECT m.amount, m.category_id, c.category_name 
        FROM amil_income m
        JOIN category c ON m.category_id = c.category_id
        WHERE m.years = ? AND c.category_type = 'pendapatan'");
    $stmt_income->bind_param("i", $year);
    $stmt_income->execute();
    $result_income = $stmt_income->get_result();

    $stmt_expense = $dbconn->prepare("SELECT COALESCE(m.amount, 0) AS amount, m.category_id, c.category_name 
        FROM amil_expense m
        JOIN category c ON m.category_id = c.category_id
        WHERE m.years = ? AND c.category_type = 'perbelanjaan'");
    $stmt_expense->bind_param("i", $year);
    $stmt_expense->execute();
    $result_expense = $stmt_expense->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Amil <?php echo $year; ?></title>
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
<main>
    <a class="btn btn-secondary" href="../amil/A-amil.php" role="button">BACK</a>
    <div class="container">
        <form action="../amil/M-update.php" method="post">
            <input type="hidden" name="year" value="<?php echo $year; ?>">

            <!-- Pendapatan Section -->
            <h2 class="section-title">Pendapatan Amil</h2>
            <table class="edit-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_income->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <input type="text" name="pendapatan[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Perbelanjaan Section -->
            <h2 class="section-title">Perbelanjaan Amil</h2>
            <table class="edit-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_expense->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <input type="text" name="perbelanjaan[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>
</main>
</body>
</html>
