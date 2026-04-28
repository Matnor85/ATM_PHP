<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="main.css">
        <title>Document</title>
    </head>
    <body>
         <!-- Svart overlay för fade-ut -->
        <div class="page-overlay" id="overlay"></div>
 
        <!-- <header>
            <h1>Bankomaten</h1>
        </header> -->
        <main>
           <?php
            // Ladda autoload och starta session
            require __DIR__ . '/../templates/home.php';
            ?>
        </main>
    </body>
</html>