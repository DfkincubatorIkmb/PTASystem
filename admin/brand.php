<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('Y-m-d H:i:s');

if (isset($_POST['submit'])) {
    $categoryid = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $brandname = htmlspecialchars($_POST['brandname'], ENT_QUOTES, 'UTF-8');
    $stmt = mysqli_prepare($con, "INSERT INTO brandname (categoryid, brandname, creationDate) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $categoryid, $brandname, $currentTime);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['msg'] = "Jenama Berjaya Dicipta !!";
    } else {
        $_SESSION['msg'] = "Ralat: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
}

if (isset($_GET['del'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    if (mysqli_query($con, "DELETE FROM brandname WHERE id = '$id'")) {
        $_SESSION['delmsg'] = "Jenama Berjaya Dipadam !!";
    } else {
        $_SESSION['delmsg'] = "Ralat: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-CARE Admin | Manage Brand</title>
    <link rel="icon" href="logopta.png" type="image/png">
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
</head>
<body>
<?php include('include/header.php');?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php');?>
            <div class="span9">
                <div class="content">
                    <div class="module">
                        <div class="module-head">
                            <h3>Manage Brand</h3>
                        </div>
                        <div class="module-body">
                            <?php if (isset($_POST['submit'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Berjaya!</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                                    <?php $_SESSION['msg'] = ""; ?>
                                </div>
                            <?php } ?>

                            <?php if (isset($_GET['del'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Ralat!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php $_SESSION['delmsg'] = ""; ?>
                                </div>
                            <?php } ?>

                            <br />

                            <form class="form-horizontal row-fluid" method="post">
                                <div class="control-group">
                                    <label class="control-label" for="category">Kategori</label>
                                    <div class="controls">
                                        <select name="category" class="span8 tip" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php
                                            $query = mysqli_query($con, "SELECT * FROM category");
                                            while ($row = mysqli_fetch_array($query)) {
                                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['categoryName']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="brandname">Nama Jenama</label>
                                    <div class="controls">
                                        <input type="text" placeholder="Masukkan Nama Jenama" name="brandname" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submit" class="btn">Tambah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="module">
                        <div class="module-head">
                            <h3>Manage Brand</h3>
                        </div>
                        <div class="module-body table">
                            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kategori</th>
                                        <th>Jenama</th>
                                        <th>Tarikh Penciptaan</th>
                                        <th>Tarikh Dikemas Kini</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    $query = mysqli_query($con, "SELECT brandname.id, category.categoryName, brandname.brandname, brandname.creationDate, brandname.updationDate 
                                                                 FROM brandname 
                                                                 JOIN category ON category.id = brandname.categoryid");
                                    while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlentities($row['categoryName']); ?></td>
                                            <td><?php echo htmlentities($row['brandname']); ?></td>
                                            <td><?php echo date('d-m-Y h:i:s A', strtotime($row['creationDate'])); ?></td>
                                            <td><?php echo $row['updationDate'] ? date('d-m-Y h:i:s A', strtotime($row['updationDate'])) : '-'; ?></td>
                                            <td>
                                                <a href="edit-brandname.php?id=<?php echo $row['id']; ?>"><i class="icon-edit"></i></a>
                                                <a href="subcategory.php?id=<?php echo $row['id']; ?>&del=delete" onclick="return confirm('Adakah anda pasti mahu memadam?')"><i class="icon-remove-sign"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php');?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="scripts/flot/jquery.flot.js"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass("btn-group datatable-pagination");
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
    });
</script>
</body>
</html>