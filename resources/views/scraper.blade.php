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
    <div style="background-color: yellow">
        <span id="price-time-noon">Noon Loading noon_price and noon_time...</span>
    </div>
    <br>
    <div style="background-color: red">
        <span id="price-time-amazon">Amazon Loading price and time...</span>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const updatePriceAndTime = () => {
            fetch('/test/noon')
                .then(response => response.json())
                .then(data => {
                    const noon_price = data.price;
                    const noon_time = data.time;
                    const priceTimeElement = document.querySelector('#price-time-noon');
                    console.log(priceTimeElement);
                    priceTimeElement.textContent = `${noon_time} - ${noon_price}`;
                })
                .catch(error => console.error(error));
        };

        const updatePriceAndTimeAmazon = () => {
            fetch('/test/amazon')
                .then(response => response.json())
                .then(data => {
                    const price = data.price;
                    const time = data.time;
                    const priceTimeElement = document.querySelector('#price-time-amazon');
                    console.log(priceTimeElement);
                    priceTimeElement.textContent = `${time} - ${price}`;
                })
                .catch(error => console.error(error));
        };

        setInterval(updatePriceAndTimeAmazon, 10000);
        setInterval(updatePriceAndTime, 10000);
    </script>
</body>
</html>




