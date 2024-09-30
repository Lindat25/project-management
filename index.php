

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>

.logo {
    width: 250px; /* Adjust as needed */
    height: auto;
    vertical-align: middle;
    margin-right: 10px;
}

header #branding {
    display: flex;
    align-items: center;
}

@media(max-width: 768px) {
    header #branding {
        justify-content: center;
    }
}


@media(max-width: 480px) {
    header #branding h1 {
        display: none;
    }
    .logo {
        width: 40px; /* Smaller logo for mobile */
    }
}




        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #01a0d7;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            overflow: hidden;
            padding: 0 20px;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #008a9a 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        header li {
            display: inline;
            padding: 0 20px 0 20px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 10px;
        }
        .highlight, header .current a {
            color: #c2c8aa;
            font-weight: bold;
        }
        #showcase {
            min-height: 400px;
            background: url('https://source.unsplash.com/random/1600x900/?project,management') no-repeat center center/cover;
            text-align: center;
            color: #ffffff;
        }
        #showcase h1 {
            margin-top: 100px;
            font-size: 55px;
            margin-bottom: 10px;
        }
        #showcase p {
            font-size: 20px;
        }
        #boxes {
            margin-top: 20px;
        }
        #boxes .box {
            float: left;
            text-align: center;
            width: 30%;
            padding: 10px;
        }
        #boxes .box img {
            width: 90px;
        }
        .button {
            display: inline-block;
            font-size: 18px;
            color: #ffffff;
            background: #3fa9f4;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background: #333;
        }
        @media(max-width: 768px) {
            header #branding,
            header nav,
            header nav li,
            #boxes .box {
                float: none;
                text-align: center;
                width: 100%;
            }
            header {
                padding-bottom: 20px;
            }
            #showcase h1 {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
            <img src="images/logo.svg" alt="Project Management System Logo" class="logo">
            </div>
            <nav>
                <ul>
                    <li class="current"><a href="index.php">Home</a></li>
                    <li><a href="views/login.php">Login</a></li>
                    <li><a href="views/register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="showcase">
        <div class="container">
            <h1>Efficient Project Management</h1>
            <p>Streamline your workflow, collaborate effectively, and achieve your project goals with our comprehensive management system.</p>
        </div>
    </section>

    <section id="boxes">
        <div class="container">
            <div class="box">
                <img src="https://img.icons8.com/color/96/000000/project-management.png" alt="Project Management">
                <h3>Manage Projects</h3>
                <p>Organize and track your projects with ease.</p>
            </div>
            <div class="box">
                <img src="https://img.icons8.com/color/96/000000/collaboration.png" alt="Collaboration">
                <h3>Collaborate</h3>
                <p>Work together seamlessly with your team.</p>
            </div>
            <div class="box">
                <img src="https://img.icons8.com/color/96/000000/task.png" alt="Task Management">
                <h3>Track Tasks</h3>
                <p>Keep tabs on all your tasks and deadlines.</p>
            </div>
        </div>
    </section>

    <section id="cta" style="clear:both; text-align:center; padding: 20px 0;">
        <div class="container">
            <h2>Ready to get started?</h2>
            <a href="views/register.php" class="button">Register Now</a>
            <a href="views/login.php" class="button" style="margin-left: 10px;">Login</a>
        </div>
    </section>

    <footer style="background:#35424a; color:#ffffff; text-align:center; padding:20px 0;">
        <p>&copy; OD International. All rights reserved.</p>
    </footer>
</body>
</html>