<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<head>
    <script type="module" src="{{asset('assets/js/app.js') }}"></script>
</head>
<!-- other code -->

<script>
    console.log(window.Echo)
    window.Echo.channel('amazon_price')
        .listen('AmazonPriceNotify', (e) => console.log('price now is: ' + e.message));
</script>
</body>
</html>




