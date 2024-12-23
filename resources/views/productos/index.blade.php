<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <h1 class="mb-4">Listado de Productos</h1>

        <!-- Mostrar la lista de productos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cod. Barra</th>
                    <th>Código Antiguo</th>
                    <th>Código Nuevo</th>
                    <th>Tipo de Código</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
    <tr>
        <td>{{ $producto->pro_codigo ?? 'No Disponible' }}</td>
        <td>{{ $producto->pro_descri ?? 'No Disponible' }}</td>
        <td>{{ $producto->wmscodigobarra->first()->codigo ?? 'No Disponible' }}</td>
        <td>{{ $producto->wmscodigobarra->first()->codigo_antig ?? 'No Disponible' }}</td>
        <td>{{ $producto->wmscodigobarra->first()->codigo_nuevo ?? 'No Disponible' }}</td>
        <td>{{ $producto->wmscodigobarra->first()->tipo_codigo ?? 'No Disponible' }}</td>
    </tr>
@endforeach

            </tbody>
        </table>

        <!-- Paginación -->
        {{ $productos->links() }}

        <!-- Enlace para descargar el CSV -->
        <a href="{{ route('productos.descargar') }}" class="btn btn-primary">Descargar CSV</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
