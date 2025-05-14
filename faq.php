<?php
session_start();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        :root {
            --text: #ecf2f4;
            --background: #0a161a;
            --primary: #87c9e3;
            --secondary: #127094;
            --third: #666666;
            --accent: #29bff9;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--background);
            font-family: 'Roboto', sans-serif;
            color: var(--text);
        }

        main {
            max-width: 800px;
            margin: 60px auto;
            padding: 20px;
        }

        .faq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .faq-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .faq-header input[type="text"] {
            padding: 8px 12px;
            border-radius: 20px;
            border: none;
            width: 200px;
            font-size: 14px;
            background-color: #d3d3d3;
            outline: none;
        }

        .accordion {
            background-color: #d3d3d3;
            color: black;
            cursor: pointer;
            padding: 16px;
            margin-bottom: 16px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .accordion:after {
            content: "\25BC";
            font-size: 12px;
        }

        .accordion.active:after {
            content: "\25B2";
        }

        .panel {
            padding: 0 16px;
            background-color: #e0e0e0;
            display: none;
            overflow: hidden;
            border-radius: 0 0 10px 10px;
            margin-top: -12px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<main>
    <div class="faq-header">
        <h2>Category Title</h2>
        <input type="text" placeholder="🔍 Search...">
    </div>

    <button class="accordion">What is sign language?</button>
    <div class="panel">
        <p>Sign language is a visual language using hand gestures, facial expressions, and body language to communicate.</p>
    </div>

    <button class="accordion">Who can benefit from learning sign language?</button>
    <div class="panel">
        <p>Anyone can benefit, including those who are deaf, hard of hearing, or those who want to communicate inclusively.</p>
    </div>

    <button class="accordion">Is the content beginner-friendly?</button>
    <div class="panel">
        <p>Yes! All videos and articles are curated to be suitable for complete beginners.</p>
    </div>

    <button class="accordion">Do I need an account to access videos?</button>
    <div class="panel">
        <p>Some videos are available to guests, but full access requires an account.</p>
    </div>
</main>

<script>
    // Accordion script
    const acc = document.querySelectorAll(".accordion");
    acc.forEach(button => {
        button.addEventListener("click", function () {
            this.classList.toggle("active");
            const panel = this.nextElementSibling;
            panel.style.display = panel.style.display === "block" ? "none" : "block";
        });
    });
</script>

</body>
</html>
