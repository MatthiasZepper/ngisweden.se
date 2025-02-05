<html>
<head>
    <title>View available icons</title>
    <style type="text/css">
        body {
            font-family: sans-serif;
        }
        h1 {
            clear:both;
            padding: 3rem 0 0.5rem;
        }
        div {
            padding: 5px;
            margin: 10px;
            display: block;
            float: left;
            cursor: pointer;
        }
        div:hover {
            background-color: #ededed;
        }
        div img {
            opacity: 0.7;
            width: 40px;
            height: 40px;
        }
        div:hover img {
            opacity: 1;
        }
        span {
            display: none;
            font-size: 10px;
        }
        div:focus span {
            display: block;
        }

    </style>
</head>
<body>
<h1>Search</h1>
<input type="text" placeholder="Search term" id="icon_search" disabled>
<?php
$dirs = ['fontawesome-svgs/solid', 'fontawesome-svgs/regular', 'fontawesome-svgs/brands'];
foreach($dirs as $dir){
    echo '<h1>'.$dir.'</h1>';
    $files = scandir($dir);
    foreach($files as $file){
        $pi = pathinfo($file);
        if($pi['extension'] == 'svg'){
            echo '<div tabindex="0">
                <img src="'.$dir.'/'.$file.'">
                <span>includes/icons/'.$dir.'/'.$file.'</span>
            </div>';
        }
    }
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script type="text/javascript">
$(function(){
    $('#icon_search').attr('disabled', false);
    $('#icon_search').on('change keyup', function(e){
        var search_term = this.value;
        $('div').each(function(){
            if($(this).find('span').text().includes(search_term)){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
</body>
</html>
