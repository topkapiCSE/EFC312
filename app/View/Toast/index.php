<!-- Coding By CodingNepal - youtube.com/codingnepal -->
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Toast Notification | CodingNepal</title>
    <link rel="stylesheet" href="<?php echo VIEW_URL;?>Toast/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome CDN link for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="<?php echo VIEW_URL;?>Toast/script.js" defer></script>
</head>
<body>
<ul class="notifications"></ul>
</body>
<script>
    document.onreadystatechange = () => {
        if (document.readyState === 'complete') {
            createToast2(<?php echo "'$type','$message'";?>);
        }
    };
</script>
</html>
