
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pago de {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .logo {
            text-align: center;
            margin-top: 20px;
        }
/*
        .logo .container {
            display: inline-block;
            background-color: red;
        } */

        .logo img {
            width: 200px; /* Ajusta el tamaño de la imagen según sea necesario */
            border-radius: 10px; /* Da un efecto de borde redondeado a la imagen */
            background-color: blue;
            margin-right: 100px;
            /* float: left; */
        }

        .logo h2 {
            margin-right: 40px;
            margin-top: 60px;
            font-size: 24px;
            /* margin: 10px 0; */
            border-top: 2px solid black; /* Línea superior con color verde */
            border-bottom: 2px solid black; /* Línea inferior con color verde */
            padding: 5px 0;
            color: black;
            float: right;
        }

        .logo h3 {
            font-size: 24px;
            margin: 10px 0;
            border-top: 2px solid #4CAF50; /* Línea superior con color verde */
            border-bottom: 2px solid #4CAF50; /* Línea inferior con color verde */
            padding: 5px 0;
            color: #4CAF50; /* Color del texto verde */
        }

        .header {
            text-align: center;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px;
            border-radius: 8px 8px 0 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .payment-details {
            margin-top: 20px;
        }

        .payment-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .payment-label {
            font-weight: bold;
            color: #555;
            min-width: 150px; /* Ajusta el ancho mínimo según tus necesidades */
            display: inline-block;
        }

        .payment-value {
            color: #333;
            display: inline-block;
            margin-left: 10px; /* Ajusta el margen entre el label y el valor según tus necesidades */
        }
    </style>
</head>
<body>
    <div class="logo">

            <img src="https://softicslab.com/wp-content/uploads/2019/09/img_review.jpg" alt="Guille software">
            <h2>RUC: 12345678901</h2>

        <h3>Guille Sistemita</h3>
    </div>

    <div class="container">
        <div class="header">
            <h2>Comprobante de Pago</h2>
        </div>

        <div class="payment-details">
            <div class="payment-info">
                <span class="payment-label">Nombre:</span>
                <span class="payment-value">{{ $user->name }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">DNI:</span>
                <span class="payment-value">{{ $user->dni }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Correo Electrónico:</span>
                <span class="payment-value">{{ $user->email }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Profesión:</span>
                <span class="payment-value">{{ $user->profession->name }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Salario Neto:</span>
                <span class="payment-value"><strong> S/ </strong>{{ $user->profession->salary }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Bono:</span>
                <span class="payment-value"><strong> S/ </strong>{{ $payment->bonus }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Descuento:</span>
                <span class="payment-value"><strong> S/ </strong>{{ $payment->discount }}</span>
            </div>

            <div class="payment-info">
                <span class="payment-label">Salario Total:</span>
                <span class="payment-value"><strong> S/ </strong>{{ $payment->salary }}</span>
            </div>
            
            <div class="payment-info">
                <span class="payment-label">Estado:</span>
                <span class="payment-value"><strong> {{ $payment->state === 0 ? 'Pendiente' : ($payment->state === 1 ? 'Pagado' : 'Anulado') }} </strong> </span>
            </div>
            
            <div class="payment-info">
                <span class="payment-label">Descripción:</span>
                <span class="payment-value"><strong>{{ $payment->description ?? 'Sin razón.' }}</strong></span>
            </div>
            <div  style=" text-align: right; color: #2e2e2e">
                <span class="payment-value"> {{date("d/m/Y", strtotime($payment->created_at))}}</span>
            </div>
        </div>
    </div>
</body>
</html>
