<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
</head>
<body>
    @extends('layouts.app')

@section('content')
    <h1>Bienvenido a la página de productos</h1>
    <!-- Aquí puedes agregar más contenido de productos -->
@endsection

    @yield('content')
</body>
</html>
