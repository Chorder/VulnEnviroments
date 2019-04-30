<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>不！可！描！述！</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <center><h1>半人马座阿尔法星 高等智慧生物训练场</h1></center>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="index.php" class="list-group-item">Home</a>
                    <a href="setup.php" class="list-group-item">Setup</a>
                </div>
                <div class="list-group">
                    <a href="brute-force.php" class="list-group-item">Brute Force</a>
                    <a href="command-execution.php" class="list-group-item">Command Execution</a>
                    <a href="csrf.php" class="list-group-item">CSRF</a>
                    <a href="file-inclusion.php" class="list-group-item">File Inclusion</a>
                    <a href="error-sql-injection.php" class="list-group-item">Error SQL Injection</a>
                    <a href="blind-sql-injection.php" class="list-group-item">Blind SQL injection</a>
                    <!--<a href="file-upload.php" class="list-group-item">File Upload</a>-->
                    <a href="reflected-xss.php" class="list-group-item">Reflected XSS</a>
                    <a href="stored-xss.php" class="list-group-item">Stored XSS</a>
                </div>
                <div class="list-group">
                    <a href="phpinfo.php" class="list-group-item">PHP Info</a>
                    <a href="#" class="list-group-item">这里还没想好放什么</a>
                </div>
            </div>
            <div class="col-md-9">
                <?php echo $page_data; ?>
            </div>
        </div>
    </div>
    <script>
	<?php echo $page_script; ?>
    </script>
</body>
</html>
